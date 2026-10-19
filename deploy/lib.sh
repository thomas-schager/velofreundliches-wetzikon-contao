#!/usr/bin/env bash
# Shared functions for deploy.sh, promote.sh, refresh-test-from-prod.sh, and refresh-local.sh.
# Sourced, not run directly. Callers must set SSH (array), SSH_PORT, PROJECT_DIR, DEPLOY_DIR,
# REMOTE_PHP_BIN, and PHP_VERSION before using any of these.

# Aborts if REMOTE_BASE_PATH_TEST and REMOTE_BASE_PATH_PRODUCTION are identical -- a copy-paste
# mistake here would mean every "safe" testing operation (refresh, deploy.sh test) is silently
# operating on production instead, bypassing every production-specific safeguard.
#
# No-op if REMOTE_BASE_PATH_TEST is unset/empty -- there is currently no test environment (see
# DEPLOY.md's note on the temporary single-host phase), and callers that only ever touch
# production (deploy.sh production, push-local-to-production.sh) have nothing to compare against.
check_base_paths_distinct() {
    if [[ -z "${REMOTE_BASE_PATH_TEST:-}" ]]; then
        return 0
    fi
    if [[ "$REMOTE_BASE_PATH_TEST" == "$REMOTE_BASE_PATH_PRODUCTION" ]]; then
        echo "ERROR: REMOTE_BASE_PATH_TEST and REMOTE_BASE_PATH_PRODUCTION are identical in deploy.env." >&2
        echo "That would make testing operations silently act on production. Fix deploy.env." >&2
        exit 1
    fi
}

# Independent check that base_path's .env.local actually points at the database we expect for
# that environment (DB_NAME_TEST / DB_NAME_PRODUCTION in deploy.env) -- so a hand-edited or
# miscopied .env.local on the server can never cause a backup/migration/refresh to silently
# operate on the wrong database. This is the only thing that determines which database gets
# touched; without it, that's implicit in whatever DATABASE_URL happens to say.
check_db_name() {
    local base_path="$1" expected="$2" actual
    actual="$("${SSH[@]}" bash -s -- "$base_path" "$REMOTE_PHP_BIN" < "$DEPLOY_DIR/remote/db-name.sh")"
    if [[ "$actual" != "$expected" ]]; then
        echo "ERROR: expected database '$expected' at $base_path/.env.local but found '$actual'." >&2
        echo "Refusing to proceed -- this looks like a misconfiguration, not something to override." >&2
        exit 1
    fi
}

# Unlike the Backend project (which commits public/.htaccess to git specifically to carry this
# line through deploys), Contao's public/ is entirely generated and gitignored -- public/.htaccess
# is deliberately excluded from every deploy/promote rsync (see those scripts) so a deploy can
# never silently clobber Hostpoint's panel-managed "Use php-fpm <version>" header with the vanilla
# skeleton version. That means there's no local file to check against; this reads the *live*
# server file directly over SSH instead and compares it to deploy.env's PHP_VERSION.
check_php_version() {
    local base_path="$1" configured
    configured="$("${SSH[@]}" "grep -m1 '^Use php-fpm ' '$base_path/public/.htaccess' 2>/dev/null | awk '{print \$3}'" || true)"
    if [[ -z "$configured" ]]; then
        echo "ERROR: $base_path/public/.htaccess has no 'Use php-fpm <version>' line yet." >&2
        echo "Set the PHP version for this domain in Hostpoint's control panel first -- see DEPLOY.md sec 3." >&2
        exit 1
    fi
    if [[ "$configured" != "$PHP_VERSION" ]]; then
        echo "ERROR: $base_path/public/.htaccess says 'Use php-fpm $configured' but deploy.env's PHP_VERSION is '$PHP_VERSION'." >&2
        echo "These must match -- fix whichever one is stale before deploying." >&2
        exit 1
    fi
}

# Generic typed-confirmation gate. Used both for "type production" before touching production
# and for "type DESTROY" when a pending migration looks destructive.
confirm_word() {
    local word="$1" prompt="$2" reply
    read -rp "$prompt (type '$word' to continue): " reply
    if [[ "$reply" != "$word" ]]; then
        echo "Aborted -- confirmation did not match." >&2
        exit 1
    fi
}

# Native Contao command (contao:maintenance-mode), not a file-rename trick -- it writes
# var/maintenance.html, which public/index.php checks for and serves as a 503 before Symfony (or
# even the vendor autoloader) boots, see that file. More robust than the Backend's .htaccess
# swap: it works even if the deploy that's currently in flight has left the app in a half-broken
# state, since it doesn't depend on anything booting correctly.
enter_maintenance() {
    local base_path="$1"
    "${SSH[@]}" "cd '$base_path' && $REMOTE_PHP_BIN bin/console contao:maintenance-mode enable --no-interaction"
}

exit_maintenance() {
    local base_path="$1"
    "${SSH[@]}" "cd '$base_path' && $REMOTE_PHP_BIN bin/console contao:maintenance-mode disable --no-interaction"
}

# Prints the path of the gzipped dump it created.
backup_db() {
    local base_path="$1"
    "${SSH[@]}" bash -s -- "$base_path" "$REMOTE_PHP_BIN" < "$DEPLOY_DIR/remote/backup-db.sh"
}

# Dry-runs pending migrations against base_path's database and, if the SQL that would run
# contains an obviously destructive operation, requires typing DESTROY before the real
# migration is allowed to happen. This is a heuristic, not a guarantee -- it catches dropped
# columns/tables and TRUNCATE, not e.g. a column type narrowed in a way that truncates values.
check_destructive_migrations() {
    local base_path="$1" dry_run
    dry_run="$("${SSH[@]}" "cd '$base_path' && $REMOTE_PHP_BIN bin/console contao:migrate --dry-run --no-interaction" 2>&1)"
    if echo "$dry_run" | grep -qiE 'DROP (COLUMN|TABLE)|TRUNCATE'; then
        echo "----------------------------------------------------------------------" >&2
        echo "Pending migration(s) contain a potentially destructive operation:" >&2
        echo "$dry_run" | grep -iE 'DROP (COLUMN|TABLE)|TRUNCATE' >&2
        echo "----------------------------------------------------------------------" >&2
        confirm_word "DESTROY" "This will run against $base_path's database"
    fi
}
