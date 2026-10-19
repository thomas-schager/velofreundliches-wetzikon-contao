#!/usr/bin/env bash
# Runs ON THE SERVER (piped over SSH by refresh-local.sh, not executed locally). Reads
# DATABASE_URL out of an environment's .env.local and streams a plain mysqldump to stdout --
# refresh-local.sh pipes this straight into the local MariaDB, so nothing lands on the server's
# disk and nothing crosses the local/remote boundary except the dump itself.
set -euo pipefail

BASE_PATH="$1"
PHP_BIN="${2:-php}"

# See backup-db.sh / refresh-db.sh for why this is eval'd directly (no temp file) and why every
# component needs rawurldecode (parse_url() does not decode percent-encoding).
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

MYSQL_PWD="$DB_PASS" mysqldump --no-tablespaces --single-transaction \
    -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME"
