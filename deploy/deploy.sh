#!/usr/bin/env bash
# Deploys the current local working tree to either the testing or production environment.
# Fixed, real directories on the server -- no releases/current/symlinks, see DEPLOY.md.
#
# Usage: ./deploy/deploy.sh test|production [--no-refresh]
#
# What happens, in order:
#   1. check the PHP version Hostpoint's panel set for this domain (read live from the server's
#      public/.htaccess) matches deploy.env's PHP_VERSION
#   2. (production only) require typing "production" to continue
#   3. (test only, skipped if --no-refresh) refresh testing's database + files/div/ from
#      production, see refresh-test-from-prod.sh
#   4. build locally (composer install, which also generates public/ and assets/ via Contao's
#      own contao-setup post-install hook), rsync the tree into the target's fixed directory
#   5. enter maintenance mode on the target (after the copy -- see the comment on
#      enter_maintenance's call site below for why not before)
#   6. warm the cache, back up the target's database
#   7. dry-run pending migrations; if any look destructive, require typing "DESTROY"
#   8. run the migrations for real
#   9. regenerate public/ symlinks and reconcile the DBAFS (contao:symlinks, contao:filesync)
#  10. leave maintenance mode -- cleared on success, left on with recovery instructions printed
#      on failure, so a broken deploy is never visible
#
# --no-refresh (test only): skips step 3, so testing's database and files/div/ are left exactly
# as they are instead of being overwritten from production -- for pushing a code change to
# testing without losing data you've accumulated there. Steps 4-9 (code, migrations) still run
# as normal. Trade-off: you lose the "every attempt begins from the same real baseline"
# guarantee this script otherwise gives you -- migrations run against whatever testing currently
# has, not a fresh copy of production, so this is meant as an occasional opt-in, not the default
# workflow.
set -euo pipefail

DEPLOY_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(cd "$DEPLOY_DIR/.." && pwd)"
CONFIG_FILE="$DEPLOY_DIR/deploy.env"

# shellcheck disable=SC1091
source "$DEPLOY_DIR/lib.sh"

TARGET="${1:-}"
if [[ "$TARGET" != "test" && "$TARGET" != "production" ]]; then
    echo "Usage: $0 test|production [--no-refresh]" >&2
    exit 1
fi

SECOND_ARG="${2:-}"
if [[ -n "$SECOND_ARG" && "$SECOND_ARG" != "--no-refresh" ]]; then
    echo "Usage: $0 test|production [--no-refresh]" >&2
    exit 1
fi
if [[ "$SECOND_ARG" == "--no-refresh" && "$TARGET" != "test" ]]; then
    echo "ERROR: --no-refresh only applies to 'test' -- production never auto-refreshes from anything." >&2
    exit 1
fi
SKIP_REFRESH="$SECOND_ARG"

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

# REMOTE_BASE_PATH_TEST/DB_NAME_TEST are only required when actually deploying to test -- there is
# currently no test environment (temporary single-host phase, see DEPLOY.md), and `deploy.sh
# production` has no use for them.
if [[ "$TARGET" == "test" ]]; then
    : "${REMOTE_BASE_PATH_TEST:?set in deploy.env -- no test environment is configured yet, see DEPLOY.md}"
    : "${DB_NAME_TEST:?set in deploy.env -- no test environment is configured yet, see DEPLOY.md}"
fi

check_base_paths_distinct

if [[ "$TARGET" == "test" ]]; then
    BASE_PATH="$REMOTE_BASE_PATH_TEST"
    EXPECTED_DB_NAME="$DB_NAME_TEST"
else
    BASE_PATH="$REMOTE_BASE_PATH_PRODUCTION"
    EXPECTED_DB_NAME="$DB_NAME_PRODUCTION"
fi

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

echo "==> Checking server layout for $BASE_PATH"
if ! "${SSH[@]}" "test -f '$BASE_PATH/.env.local'"; then
    echo "ERROR: $BASE_PATH/.env.local does not exist on the server yet." >&2
    echo "Create it once by hand first -- see DEPLOY.md sec 3 (one-time server setup)." >&2
    exit 1
fi

echo "==> Checking PHP version pinning matches deploy.env"
check_php_version "$BASE_PATH"

if [[ "$TARGET" == "production" ]]; then
    confirm_word "production" "About to deploy to PRODUCTION ($BASE_PATH)"
fi

echo "==> Verifying $BASE_PATH points at the expected database"
check_db_name "$BASE_PATH" "$EXPECTED_DB_NAME"

if [[ "$TARGET" == "test" ]]; then
    if [[ "$SKIP_REFRESH" == "--no-refresh" ]]; then
        echo "==> Skipping refresh (--no-refresh): testing's database and files/div/ are left as-is"
        echo "    Migrations will run against testing's current state, not a fresh copy of production."
    else
        echo "==> Refreshing testing's database and files/div/ from production"
        "$DEPLOY_DIR/refresh-test-from-prod.sh"
    fi
fi

echo "==> Building locally (composer install --no-dev)"
cd "$PROJECT_DIR"
# Unlike the Backend project's plain Symfony app, we do NOT pass --no-scripts here: Composer's
# post-install hook (vendor/bin/contao-setup) is what generates public/ and assets/ locally --
# skeleton:install, assets:install --symlink, contao:install, contao:symlinks, cache warmup --
# and we want exactly that output in the tree we're about to ship. The cache it warms is excluded
# from the rsync below (var/ is never shipped) and gets rebuilt for real, server-side, against
# the target's own .env.local, later in this script -- so there's no risk of a locally-warmed
# cache baking in the wrong environment's config.
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Uploading code to $BASE_PATH"
rsync -az --delete \
    --exclude='.git' \
    --exclude='.env.local' \
    --exclude='var/' \
    --exclude='public/.htaccess' \
    --exclude='files/div/' \
    --include='files/div/.public' \
    --exclude='deploy/' \
    -e "$RSYNC_SSH" \
    ./ "$SSH_USER@$SSH_HOST:$BASE_PATH/"

# Entered only now, after the copy -- see refresh-test-from-prod.sh and promote.sh for the same
# reasoning applied elsewhere. The accepted trade-off: the copy itself (a few seconds) isn't
# covered by maintenance mode, everything from here on is.
echo "==> Entering maintenance mode"
MAINTENANCE_BASE="$BASE_PATH"
enter_maintenance "$BASE_PATH"

echo "==> Warming cache"
"${SSH[@]}" "cd '$BASE_PATH' && $REMOTE_PHP_BIN bin/console cache:clear --env=prod"

echo "==> Backing up database"
BACKUP_FILE="$(backup_db "$BASE_PATH")"
echo "    saved to $BACKUP_FILE"

echo "==> Checking pending migrations for destructive operations"
check_destructive_migrations "$BASE_PATH"

echo "==> Running database migrations"
"${SSH[@]}" "cd '$BASE_PATH' && $REMOTE_PHP_BIN bin/console contao:migrate --no-interaction"

echo "==> Regenerating public/ symlinks"
"${SSH[@]}" "cd '$BASE_PATH' && $REMOTE_PHP_BIN bin/console contao:symlinks --env=prod"

echo "==> Synchronizing the virtual filesystem (contao:filesync)"
"${SSH[@]}" "cd '$BASE_PATH' && $REMOTE_PHP_BIN bin/console contao:filesync --no-interaction"

echo "==> Warming up cache"
"${SSH[@]}" "cd '$BASE_PATH' && $REMOTE_PHP_BIN bin/console cache:warmup --env=prod"

echo
echo "Deployed to $TARGET ($BASE_PATH)."
