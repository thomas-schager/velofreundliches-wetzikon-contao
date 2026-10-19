#!/usr/bin/env bash
# Runs ON THE SERVER (piped over SSH by refresh-test-from-prod.sh, not executed locally).
# Dumps the source database and restores it directly into the target's via one pipe -- both on
# the same MariaDB host, so this never touches disk or leaves the server.
#
# The target's existing tables are dropped first (see below), separately from the dump/restore
# pipe -- mysqldump's own DROP TABLE IF EXISTS only covers tables that exist in the SOURCE, not
# anything the target has that the source doesn't. That gap matters here specifically: testing
# routinely runs migrations ahead of production (deploy.sh test migrates testing; promote.sh
# only later carries the same code/migrations to production), so a table a migration already
# created on testing can survive untouched by this refresh while tl_migration's history gets
# overwritten with production's older state -- the next migrate then tries to create a table
# that's still physically there and aborts. Dropping everything first makes the target a true
# replica of the source every time, regardless of what it had before.
set -euo pipefail

SOURCE_PATH="$1"
TARGET_PATH="$2"
PHP_BIN="${3:-php}"

# eval's SOURCE_HOST/PORT/USER/PASS/NAME or TARGET_* directly into this shell -- no temp file (a
# mktemp'd file would land in the server's default temp dir, outside both paths).
#
# parse_url() does NOT decode percent-encoding -- rawurldecode every component, or
# mysql/mysqldump get handed the still-encoded string and authentication fails.
read_db_vars() {
    local env_file="$1" prefix="$2"
    eval "$("$PHP_BIN" -r '
    $env = file_get_contents($argv[1]);
    if (!preg_match("/^DATABASE_URL=\"?([^\"\n]+)\"?/m", $env, $m)) {
        fwrite(STDERR, "DATABASE_URL not found in " . $argv[1] . "\n");
        exit(1);
    }
    $url = parse_url($m[1]);
    $prefix = $argv[2];
    $vars = [
        "{$prefix}_HOST" => $url["host"] ?? "127.0.0.1",
        "{$prefix}_PORT" => (string) ($url["port"] ?? 3306),
        "{$prefix}_USER" => rawurldecode($url["user"] ?? ""),
        "{$prefix}_PASS" => rawurldecode($url["pass"] ?? ""),
        "{$prefix}_NAME" => rawurldecode(ltrim($url["path"] ?? "", "/")),
    ];
    foreach ($vars as $k => $v) {
        printf("%s=%s\n", $k, escapeshellarg($v));
    }
    ' "$env_file" "$prefix")"
}

read_db_vars "$SOURCE_PATH/.env.local" SOURCE
read_db_vars "$TARGET_PATH/.env.local" TARGET

# Drop every table currently in the target before importing -- see the comment at the top of
# this file. No DROP DATABASE here: a shared-hosting DB user (this account included) commonly
# can't create/drop the database itself, only its tables, so this works regardless of that
# privilege.
TARGET_TABLES="$(MYSQL_PWD="$TARGET_PASS" mysql -N -h "$TARGET_HOST" -P "$TARGET_PORT" -u "$TARGET_USER" "$TARGET_NAME" -e \
    "SELECT GROUP_CONCAT(table_name) FROM information_schema.tables WHERE table_schema = '$TARGET_NAME'")"
if [[ -n "$TARGET_TABLES" && "$TARGET_TABLES" != "NULL" ]]; then
    MYSQL_PWD="$TARGET_PASS" mysql -h "$TARGET_HOST" -P "$TARGET_PORT" -u "$TARGET_USER" "$TARGET_NAME" -e \
        "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS $TARGET_TABLES; SET FOREIGN_KEY_CHECKS = 1;"
fi

MYSQL_PWD="$SOURCE_PASS" mysqldump --no-tablespaces --single-transaction \
    -h "$SOURCE_HOST" -P "$SOURCE_PORT" -u "$SOURCE_USER" "$SOURCE_NAME" \
| MYSQL_PWD="$TARGET_PASS" mysql -h "$TARGET_HOST" -P "$TARGET_PORT" -u "$TARGET_USER" "$TARGET_NAME"

echo "Copied $SOURCE_NAME -> $TARGET_NAME"
