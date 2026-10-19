#!/usr/bin/env bash
# Overwrites PRODUCTION's database and files/div/ with local's -- the reverse of every other
# data-flow direction in this project (normally production/test is the master, see DEPLOY.md
# sec 10.5; refresh-local.sh pulls data DOWN). This script exists only for the temporary phase
# where there is no test environment yet and local is the real source of truth (e.g. a
# temporary Hostpoint host like lizabize.myhostpoint.ch), and is expected to be retired once a
# proper test environment exists and the normal local -> test -> production code-only flow
# takes over for good.
#
# Does NOT touch code -- run deploy.sh production first (or after) to ship code/schema. This
# script only pushes DATA: the database content and files/div/ (user-style content, here
# standing in for "whatever local currently has").
#
# Usage: ./deploy/push-local-to-production.sh
set -euo pipefail

DEPLOY_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd "$DEPLOY_DIR/.." && pwd)"
CONFIG_FILE="$DEPLOY_DIR/deploy.env"

# shellcheck disable=SC1091
source "$DEPLOY_DIR/lib.sh"

if [[ ! -f "$CONFIG_FILE" ]]; then
    echo "Missing $CONFIG_FILE -- copy deploy.env.example to deploy.env and fill in your values." >&2
    exit 1
fi
# shellcheck disable=SC1090
source "$CONFIG_FILE"
: "${SSH_HOST:?set in deploy.env}" "${SSH_USER:?set in deploy.env}"
: "${REMOTE_BASE_PATH_PRODUCTION:?set in deploy.env}"
: "${PHP_VERSION:?set in deploy.env}" "${REMOTE_PHP_BIN:?set in deploy.env}"
: "${DB_NAME_PRODUCTION:?set in deploy.env}"
SSH_PORT="${SSH_PORT:-22}"
SSH=(ssh -p "$SSH_PORT" "$SSH_USER@$SSH_HOST")
RSYNC_SSH="ssh -p $SSH_PORT"

BASE_PATH="$REMOTE_BASE_PATH_PRODUCTION"

LOCAL_ENV_FILE="$PROJECT_DIR/.env.local"
if [[ ! -f "$LOCAL_ENV_FILE" ]]; then
    echo "ERROR: $LOCAL_ENV_FILE does not exist -- set up local dev first, see DEPLOY.md sec 1." >&2
    exit 1
fi
if ! "${SSH[@]}" "test -f '$BASE_PATH/.env.local'"; then
    echo "ERROR: $BASE_PATH/.env.local does not exist on the server yet." >&2
    echo "Create it once by hand first -- see DEPLOY.md sec 3 (one-time server setup)." >&2
    exit 1
fi

echo "==> Checking PHP version pinning matches deploy.env"
check_php_version "$BASE_PATH"

echo "==> Verifying $BASE_PATH points at the expected database"
check_db_name "$BASE_PATH" "$DB_NAME_PRODUCTION"

echo "This will PERMANENTLY OVERWRITE production's database and files/div/ with local's."
echo "A backup of production's current database is taken first, but this is still destructive."
confirm_word "production" "About to push local's database and files/div/ into PRODUCTION ($BASE_PATH)"

MAINTENANCE_BASE=""
cleanup() {
    local exit_code=$?
    if [[ -n "$MAINTENANCE_BASE" ]]; then
        if [[ $exit_code -eq 0 ]]; then
            exit_maintenance "$MAINTENANCE_BASE"
        else
            echo >&2
            echo "======================================================================" >&2
            echo "FAILED (exit $exit_code). Maintenance mode is left ON for $MAINTENANCE_BASE" >&2
            echo "so nothing broken is visible. Once resolved, clear it with:" >&2
            echo "  ssh -p $SSH_PORT $SSH_USER@$SSH_HOST \"cd $MAINTENANCE_BASE && $REMOTE_PHP_BIN bin/console contao:maintenance-mode disable\"" >&2
            echo "======================================================================" >&2
        fi
    fi
}
trap cleanup EXIT

echo "==> Entering maintenance mode"
MAINTENANCE_BASE="$BASE_PATH"
enter_maintenance "$BASE_PATH"

echo "==> Backing up production's database before overwriting"
BACKUP_FILE="$(backup_db "$BASE_PATH")"
echo "    saved to $BACKUP_FILE"

# Local credentials, parsed the same way as refresh-local.sh -- see dump-db.sh for why
# rawurldecode is needed on every component.
eval "$(php -r '
$env = file_get_contents($argv[1]);
if (!preg_match("/^DATABASE_URL=\"?([^\"\n]+)\"?/m", $env, $m)) {
    fwrite(STDERR, "DATABASE_URL not found in " . $argv[1] . "\n");
    exit(1);
}
$url = parse_url($m[1]);
$vars = [
    "LOCAL_DB_HOST" => $url["host"] ?? "127.0.0.1",
    "LOCAL_DB_PORT" => (string) ($url["port"] ?? 3306),
    "LOCAL_DB_USER" => rawurldecode($url["user"] ?? ""),
    "LOCAL_DB_PASS" => rawurldecode($url["pass"] ?? ""),
    "LOCAL_DB_NAME" => rawurldecode(ltrim($url["path"] ?? "", "/")),
];
foreach ($vars as $k => $v) {
    printf("%s=%s\n", $k, escapeshellarg($v));
}
' "$LOCAL_ENV_FILE")"

echo "==> Pushing local's database into production (dropping production's tables first)"
# restore-db.sh's own stdin is where the mysqldump data below needs to land -- it can't also be
# how the script itself gets delivered (an explicit `<` redirect on the ssh call would win over
# the pipe for that fd, starving mysqldump's output with nowhere to go, see the bug this
# replaced). So: write the script to a remote temp file first (a separate ssh call, stdin fully
# free for that), then run it by path in a second ssh call whose stdin is reserved for the dump.
REMOTE_TMP="$("${SSH[@]}" mktemp)"
"${SSH[@]}" "cat > '$REMOTE_TMP'" < "$DEPLOY_DIR/remote/restore-db.sh"
MYSQL_PWD="$LOCAL_DB_PASS" mysqldump --no-tablespaces --single-transaction \
    -h "$LOCAL_DB_HOST" -P "$LOCAL_DB_PORT" -u "$LOCAL_DB_USER" "$LOCAL_DB_NAME" \
    | "${SSH[@]}" "bash '$REMOTE_TMP' '$BASE_PATH' '$REMOTE_PHP_BIN'; STATUS=\$?; rm -f '$REMOTE_TMP'; exit \$STATUS"

# Local runs over plain HTTP and so has useSSL=0 on its root page(s) -- which just travelled here
# verbatim as part of the dump above. Production is HTTPS-only (Hostpoint enforces HTTP -> HTTPS
# for this domain), so left as-is this collides into a redirect loop (Contao's own
# RedirectController actively bounces HTTPS -> HTTP for any useSSL=0 root page). Forced back to 1
# here, every time, since it's an environment property, not real content -- see force-root-ssl.sh.
echo "==> Forcing useSSL=1 on production's root page(s) (environment-specific, not local content)"
"${SSH[@]}" bash -s -- "$BASE_PATH" "$REMOTE_PHP_BIN" < "$DEPLOY_DIR/remote/force-root-ssl.sh"

echo "==> Pushing files/div/ (local content) into production"
mkdir -p "$PROJECT_DIR/files/div"
"${SSH[@]}" "mkdir -p '$BASE_PATH/files/div'"
rsync -az --delete -e "$RSYNC_SSH" \
    "$PROJECT_DIR/files/div/" "$SSH_USER@$SSH_HOST:$BASE_PATH/files/div/"

echo "==> Synchronizing production's virtual filesystem against the pushed files/div/"
"${SSH[@]}" "cd '$BASE_PATH' && $REMOTE_PHP_BIN bin/console contao:filesync --no-interaction"

echo
echo "Production's database and files/div/ now match local."
