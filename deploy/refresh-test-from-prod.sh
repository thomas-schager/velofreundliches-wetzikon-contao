#!/usr/bin/env bash
# Overwrites testing's database and files/div/ with a fresh copy from production. Entirely
# server-side (mysqldump piped straight into the test database, rsync between the two project
# directories) -- both environments and both databases live on the same Hostpoint
# account/filesystem, so nothing needs to pass through this machine.
#
# Called automatically as step 1 of every `deploy.sh test` run (unless --no-refresh); can also
# be run standalone to just reset testing without deploying code.
#
# Usage: ./deploy/refresh-test-from-prod.sh
set -euo pipefail

DEPLOY_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
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
: "${REMOTE_PHP_BIN:?set in deploy.env}"
: "${DB_NAME_PRODUCTION:?set in deploy.env}"
if [[ -z "${REMOTE_BASE_PATH_TEST:-}" || -z "${DB_NAME_TEST:-}" ]]; then
    echo "ERROR: no test environment is configured in deploy.env yet -- there is nothing to refresh." >&2
    echo "This is expected during the temporary single-host phase, see DEPLOY.md." >&2
    exit 1
fi
SSH_PORT="${SSH_PORT:-22}"
SSH=(ssh -p "$SSH_PORT" "$SSH_USER@$SSH_HOST")

check_base_paths_distinct

PROD_PATH="$REMOTE_BASE_PATH_PRODUCTION"
TEST_PATH="$REMOTE_BASE_PATH_TEST"

if ! "${SSH[@]}" "test -f '$PROD_PATH/.env.local'"; then
    echo "ERROR: production doesn't look deployed yet ($PROD_PATH/.env.local missing) -- nothing to refresh from." >&2
    exit 1
fi
if ! "${SSH[@]}" "test -f '$TEST_PATH/.env.local'"; then
    echo "ERROR: testing doesn't look deployed yet ($TEST_PATH/.env.local missing)." >&2
    exit 1
fi

echo "==> Verifying production and testing point at their expected, distinct databases"
check_db_name "$PROD_PATH" "$DB_NAME_PRODUCTION"
check_db_name "$TEST_PATH" "$DB_NAME_TEST"

echo "==> Copying production's database into testing's"
"${SSH[@]}" bash -s -- "$PROD_PATH" "$TEST_PATH" "$REMOTE_PHP_BIN" < "$DEPLOY_DIR/remote/refresh-db.sh"

echo "==> Copying files/div/ (user-uploaded content -- production is the master)"
"${SSH[@]}" "
    mkdir -p '$TEST_PATH/files/div' '$PROD_PATH/files/div'
    rsync -a --delete '$PROD_PATH/files/div/' '$TEST_PATH/files/div/'
"

echo "==> Synchronizing testing's virtual filesystem against the refreshed files/div/"
"${SSH[@]}" "cd '$TEST_PATH' && $REMOTE_PHP_BIN bin/console contao:filesync --no-interaction"

echo "Testing refreshed from production."
