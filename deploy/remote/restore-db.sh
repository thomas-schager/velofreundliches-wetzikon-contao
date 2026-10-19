#!/usr/bin/env bash
# Runs ON THE SERVER (piped over SSH by push-local-to-production.sh, not executed locally).
# Reads DATABASE_URL out of BASE_PATH's .env.local, drops every table currently in that
# database, then imports a plain-SQL dump from stdin into it -- the reverse direction of
# dump-db.sh/refresh-db.sh, used only during the temporary phase where local is the master.
#
# Tables are dropped first, separately from the import, for the same reason as refresh-db.sh:
# a plain mysqldump's own DROP TABLE IF EXISTS only covers tables present in the dump being
# imported, not anything the target already has that the dump doesn't -- dropping everything
# first makes the target a true replica of what's being pushed, regardless of what it had before.
set -euo pipefail

BASE_PATH="$1"
PHP_BIN="${2:-php}"

eval "$("$PHP_BIN" -r '
$env = file_get_contents($argv[1]);
if (!preg_match("/^DATABASE_URL=\"?([^\"\n]+)\"?/m", $env, $m)) {
    fwrite(STDERR, "DATABASE_URL not found in " . $argv[1] . "\n");
    exit(1);
}
$url = parse_url($m[1]);
$vars = [
    "DB_HOST" => $url["host"] ?? "127.0.0.1",
    "DB_PORT" => (string) ($url["port"] ?? 3306),
    "DB_USER" => rawurldecode($url["user"] ?? ""),
    "DB_PASS" => rawurldecode($url["pass"] ?? ""),
    "DB_NAME" => rawurldecode(ltrim($url["path"] ?? "", "/")),
];
foreach ($vars as $k => $v) {
    printf("%s=%s\n", $k, escapeshellarg($v));
}
' "$BASE_PATH/.env.local")"

TARGET_TABLES="$(MYSQL_PWD="$DB_PASS" mysql -N -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" -e \
    "SELECT GROUP_CONCAT(table_name) FROM information_schema.tables WHERE table_schema = '$DB_NAME'")"
if [[ -n "$TARGET_TABLES" && "$TARGET_TABLES" != "NULL" ]]; then
    MYSQL_PWD="$DB_PASS" mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" -e \
        "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS $TARGET_TABLES; SET FOREIGN_KEY_CHECKS = 1;"
fi

MYSQL_PWD="$DB_PASS" mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME"
