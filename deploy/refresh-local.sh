#!/usr/bin/env bash
# Overwrites the LOCAL database and files/div/ with a fresh copy from production or testing.
# No Backend-project equivalent exists for this direction (that system only ever pulls prod ->
# test, never down to a developer's machine) -- this is new, Contao-project-specific.
#
# The DB dump is streamed straight from the remote mysqldump into the local mysql client over
# one SSH pipe; files/div/ is pulled via rsync. Nothing lands on the server's disk. No
# maintenance mode (local isn't a public deploy target) and no production-style confirmation
# gate (nothing remote is modified), but a local DB backup is taken first regardless, as cheap
# insurance before the drop-and-restore.
#
# Usage: ./deploy/refresh-local.sh production|test
set -euo pipefail

DEPLOY_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd "$DEPLOY_DIR/.." && pwd)"
CONFIG_FILE="$DEPLOY_DIR/deploy.env"

# shellcheck disable=SC1091
source "$DEPLOY_DIR/lib.sh"

SOURCE="${1:-}"
if [[ "$SOURCE" != "production" && "$SOURCE" != "test" ]]; then
    echo "Usage: $0 production|test" >&2
    exit 1
fi

if [[ ! -f "$CONFIG_FILE" ]]; then
    echo "Missing $CONFIG_FILE -- copy deploy.env.example to deploy.env and fill in your values." >&2
    exit 1
fi
# shellcheck disable=SC1090
source "$CONFIG_FILE"
: "${SSH_HOST:?set in deploy.env}" "${SSH_USER:?set in deploy.env}"
: "${REMOTE_BASE_PATH_PRODUCTION:?set in deploy.env}"
: "${REMOTE_PHP_BIN:?set in deploy.env}"
: "${DB_NAME_PRODUCTION:?set in deploy.env}"
if [[ "$SOURCE" == "test" ]]; then
    : "${REMOTE_BASE_PATH_TEST:?set in deploy.env -- no test environment is configured yet, see DEPLOY.md}"
    : "${DB_NAME_TEST:?set in deploy.env -- no test environment is configured yet, see DEPLOY.md}"
fi
SSH_PORT="${SSH_PORT:-22}"
SSH=(ssh -p "$SSH_PORT" "$SSH_USER@$SSH_HOST")
RSYNC_SSH="ssh -p $SSH_PORT"

check_base_paths_distinct

if [[ "$SOURCE" == "production" ]]; then
    SOURCE_PATH="$REMOTE_BASE_PATH_PRODUCTION"
    EXPECTED_DB_NAME="$DB_NAME_PRODUCTION"
else
    SOURCE_PATH="$REMOTE_BASE_PATH_TEST"
    EXPECTED_DB_NAME="$DB_NAME_TEST"
fi

LOCAL_ENV_FILE="$PROJECT_DIR/.env.local"
if [[ ! -f "$LOCAL_ENV_FILE" ]]; then
    echo "ERROR: $LOCAL_ENV_FILE does not exist -- set up local dev first, see DEPLOY.md sec 1." >&2
    exit 1
fi
if ! "${SSH[@]}" "test -f '$SOURCE_PATH/.env.local'"; then
    echo "ERROR: $SOURCE doesn't look deployed yet ($SOURCE_PATH/.env.local missing) -- nothing to refresh from." >&2
    exit 1
fi

echo "==> Verifying $SOURCE points at its expected database"
check_db_name "$SOURCE_PATH" "$EXPECTED_DB_NAME"

# Local credentials, parsed the same way as every remote script -- just run locally instead of
# over SSH. See remote/db-name.sh and friends for why rawurldecode is needed on every component.
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

echo "==> Backing up local database before overwriting"
mkdir -p "$PROJECT_DIR/var/backups"
LOCAL_BACKUP="$PROJECT_DIR/var/backups/${LOCAL_DB_NAME}_$(date +%Y%m%d%H%M%S).sql.gz"
MYSQL_PWD="$LOCAL_DB_PASS" mysqldump --no-tablespaces --single-transaction \
    -h "$LOCAL_DB_HOST" -P "$LOCAL_DB_PORT" -u "$LOCAL_DB_USER" "$LOCAL_DB_NAME" | gzip > "$LOCAL_BACKUP"
echo "    saved to $LOCAL_BACKUP"

echo "==> Dropping local tables (see remote/refresh-db.sh for why this happens separately from the restore)"
LOCAL_TABLES="$(MYSQL_PWD="$LOCAL_DB_PASS" mysql -N -h "$LOCAL_DB_HOST" -P "$LOCAL_DB_PORT" -u "$LOCAL_DB_USER" "$LOCAL_DB_NAME" -e \
    "SELECT GROUP_CONCAT(table_name) FROM information_schema.tables WHERE table_schema = '$LOCAL_DB_NAME'")"
if [[ -n "$LOCAL_TABLES" && "$LOCAL_TABLES" != "NULL" ]]; then
    MYSQL_PWD="$LOCAL_DB_PASS" mysql -h "$LOCAL_DB_HOST" -P "$LOCAL_DB_PORT" -u "$LOCAL_DB_USER" "$LOCAL_DB_NAME" -e \
        "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS $LOCAL_TABLES; SET FOREIGN_KEY_CHECKS = 1;"
fi

echo "==> Streaming $SOURCE's database into local"
"${SSH[@]}" bash -s -- "$SOURCE_PATH" "$REMOTE_PHP_BIN" < "$DEPLOY_DIR/remote/dump-db.sh" \
    | MYSQL_PWD="$LOCAL_DB_PASS" mysql -h "$LOCAL_DB_HOST" -P "$LOCAL_DB_PORT" -u "$LOCAL_DB_USER" "$LOCAL_DB_NAME"

echo "==> Pulling files/div/ (user-uploaded content) from $SOURCE"
mkdir -p "$PROJECT_DIR/files/div"
rsync -az --delete -e "$RSYNC_SSH" \
    "$SSH_USER@$SSH_HOST:$SOURCE_PATH/files/div/" "$PROJECT_DIR/files/div/"

echo "==> Synchronizing the local virtual filesystem against the refreshed files/div/"
(cd "$PROJECT_DIR" && php bin/console contao:filesync --no-interaction)

echo
echo "Local refreshed from $SOURCE."
