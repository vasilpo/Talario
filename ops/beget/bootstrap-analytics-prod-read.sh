#!/usr/bin/env bash
set -euo pipefail

PATH="/usr/local/bin:/usr/bin:/bin"
export PATH
umask 077

ROOT="/home/t/tyman5tb/talario.ru/public_html"
CONFIG="$ROOT/config.local.php"
PHP="/usr/local/bin/php8.2"
CURL="/usr/bin/curl"
STATE_DIR="$HOME/.local/state/talario/analytics-prod"
RUNTIME_DIR="/dev/shm"
EXPECTED_BRANCH="prod"

fail() {
  printf 'ERROR: %s\n' "$1" >&2
  exit "${2:-64}"
}

[ -d "$ROOT" ] || fail "PROD root not found" 65
cd "$ROOT"
[ "$(git rev-parse --abbrev-ref HEAD)" = "$EXPECTED_BRANCH" ] || fail "PROD is not on prod branch" 66
[ -f "$CONFIG" ] && [ ! -L "$CONFIG" ] || fail "unsafe config.local.php" 67
[ -x "$PHP" ] || fail "php8.2 unavailable" 68
[ -x "$CURL" ] || fail "curl unavailable" 69

mkdir -p "$STATE_DIR"
chmod 700 "$STATE_DIR"

STAMP="$(date -u +%Y%m%dT%H%M%SZ)"
BACKUP="$STATE_DIR/config.local.php.$STAMP.bak"
TMP="$STATE_DIR/config.local.php.$STAMP.tmp"
RESPONSE="$STATE_DIR/smoke.$STAMP.json"
TOKEN_FILE="$(/usr/bin/mktemp "$RUNTIME_DIR/talario-analytics-prod-token.XXXXXX")"
HEADER_FILE="$(/usr/bin/mktemp "$RUNTIME_DIR/talario-analytics-prod-header.XXXXXX")"
chmod 600 "$TOKEN_FILE" "$HEADER_FILE"

cp -p "$CONFIG" "$BACKUP"
chmod 600 "$BACKUP"

read -r TOKEN HASH < <("$PHP" -r '
$token = bin2hex(random_bytes(32));
echo $token, " ", hash("sha256", $token), PHP_EOL;
')
[ "${#TOKEN}" -eq 64 ] || fail "token generation failed" 70
[ "${#HASH}" -eq 64 ] || fail "token hash generation failed" 70

export TALARIO_CONFIG="$CONFIG"
export TALARIO_TMP="$TMP"
export TALARIO_HASH="$HASH"

"$PHP" -r '
$src = getenv("TALARIO_CONFIG");
$tmp = getenv("TALARIO_TMP");
$hash = getenv("TALARIO_HASH");
$raw = file_get_contents($src);
if ($raw === false) {
    fwrite(STDERR, "config read failed\n");
    exit(2);
}
$raw = preg_replace("/^\\s*define\\(\\s*[\\x27\x22]TALARIO_ANALYTICS_TOKEN_HASH[\\x27\x22]\\s*,.*?\\);\\s*$/m", "", $raw);
$block = "\ndefine(\x27TALARIO_ANALYTICS_TOKEN_HASH\x27, \x27sha256:" . $hash . "\x27);\n";
if (preg_match("/\\?>\\s*$/", $raw)) {
    $raw = preg_replace("/\\?>\\s*$/", $block . "\n?>\n", $raw, 1);
} else {
    $raw = rtrim($raw) . "\n" . $block;
}
if (file_put_contents($tmp, $raw, LOCK_EX) === false) {
    fwrite(STDERR, "config temp write failed\n");
    exit(3);
}
' || fail "config transformation failed" 71

chmod --reference="$CONFIG" "$TMP"
"$PHP" -l "$TMP" >/dev/null || fail "generated config failed PHP lint" 72

SUCCESS=0
CONFIG_REPLACED=0

cleanup() {
  rc=$?
  if [ "$SUCCESS" -ne 1 ] && [ "$CONFIG_REPLACED" -eq 1 ]; then
    cp -p "$BACKUP" "$CONFIG" || true
  fi
  rm -f "$TMP" "$RESPONSE" "$HEADER_FILE"
  if [ "$SUCCESS" -ne 1 ] && [ -f "$TOKEN_FILE" ]; then
    : > "$TOKEN_FILE"
    rm -f "$TOKEN_FILE"
  fi
  exit "$rc"
}
trap cleanup EXIT
trap 'exit 130' INT
trap 'exit 143' TERM

CONFIG_REPLACED=1
mv "$TMP" "$CONFIG"

printf '%s' "$TOKEN" > "$TOKEN_FILE"
printf 'Authorization: Bearer %s\n' "$TOKEN" > "$HEADER_FILE"
unset TOKEN

HTTP="$("$CURL" -sS -o "$RESPONSE" -w '%{http_code}' --max-time 30 \
  --resolve talario.ru:443:127.0.0.1 \
  --header "@$HEADER_FILE" \
  "https://talario.ru/index.php?dispatch=talario_analytics.orders&date1=2026-09-17&date2=2026-09-22&page=1&limit=100")"
rm -f "$HEADER_FILE"
[ "$HTTP" = "200" ] || fail "orders smoke returned HTTP $HTTP" 73

"$PHP" -r '
$d = json_decode(file_get_contents($argv[1]), true);
if (!is_array($d) || !isset($d["orders"]) || !is_array($d["orders"])) {
    fwrite(STDERR, "unexpected orders response\n");
    exit(4);
}
$orders = [];
foreach ($d["orders"] as $order) {
    $orders[(string) ($order["order_id"] ?? "")] = $order;
}
$expected = ["219" => 0.0, "220" => 750.0, "221" => 2500.0];
foreach ($expected as $id => $total) {
    if (!isset($orders[$id])) {
        fwrite(STDERR, "missing order " . $id . "\n");
        exit(5);
    }
    if (abs((float) ($orders[$id]["total"] ?? 0) - $total) > 0.001) {
        fwrite(STDERR, "unexpected total for order " . $id . "\n");
        exit(6);
    }
    if (!array_key_exists("legacy_booking_count", $orders[$id])) {
        fwrite(STDERR, "missing legacy booking count for order " . $id . "\n");
        exit(7);
    }
}
' "$RESPONSE" || fail "orders schema/order smoke failed" 74

SUCCESS=1
rm -f "$RESPONSE"
rm -f "$BACKUP"

/usr/bin/nohup /bin/sh -c '
  sleep 600
  if [ -f "$1" ]; then
    : > "$1"
    rm -f "$1"
  fi
' sh "$TOKEN_FILE" >/dev/null 2>&1 &

echo "BOOTSTRAP_OK"
echo "TOKEN_FILE=$TOKEN_FILE"
echo "TOKEN_TTL_SECONDS=600"
echo "ORDERS_219_220_221=PASS"
