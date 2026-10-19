#!/usr/bin/env bash
# Runs ON THE SERVER (piped over SSH by push-local-to-production.sh, not executed locally).
#
# tl_page.useSSL is page *content* that travels wholesale with every database push -- but
# production here is HTTPS-only (Hostpoint enforces HTTP -> HTTPS for this domain at the Apache
# level), while local runs over plain HTTP and so naturally has useSSL=0 on its root page(s).
# Contao's own RedirectController actively redirects HTTPS requests to HTTP for any root page
# where useSSL is false -- which collides head-on with Hostpoint's HTTP -> HTTPS enforcement into
# an infinite redirect loop. This is an environment property, not real content (same reasoning as
# why .env.local is never shipped by a code deploy), so it's forced back to 1 here, every time,
# right after the database import -- regardless of what local's copy says.
set -euo pipefail

BASE_PATH="$1"
PHP_BIN="${2:-php}"

eval "$("$PHP_BIN" -r '
$env = file_get_contents($argv[1]);
if (!preg_match("/^DATABASE_URL=\"?([^\"\n]+)/m", $env, $m)) {
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

MYSQL_PWD="$DB_PASS" mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME" -e \
    "UPDATE tl_page SET useSSL = 1 WHERE type = 'root' AND useSSL = 0"
