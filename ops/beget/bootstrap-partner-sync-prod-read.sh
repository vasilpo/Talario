#!/usr/bin/env bash
set -euo pipefail

PATH="/usr/local/bin:/usr/bin:/bin"
export PATH
umask 077

ROOT="/home/t/tyman5tb/talario.ru/public_html"
CONFIG="$ROOT/config.local.php"
PHP="/usr/local/bin/php8.2"
CURL="/usr/bin/curl"
STATE_DIR="$HOME/.local/state/talario/partner-sync-prod"
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
HEADER="$STATE_DIR/header.$STAMP.conf"

cp -p "$CONFIG" "$BACKUP"
chmod 600 "$BACKUP"

TOKEN="$("$PHP" -r 'echo bin2hex(random_bytes(32));')"
HASH="$("$PHP" -r 'echo hash("sha256", $argv[1]);' "$TOKEN")"
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
$raw = preg_replace("/^\\s*define\\(\\x27TALARIO_PARTNER_SYNC_PROD_READ\\x27\\s*,.*?\\);\\s*$/m", "", $raw);
$raw = preg_replace("/^\\s*define\\(\\x27TALARIO_PARTNER_SYNC_TOKEN_HASH\\x27\\s*,.*?\\);\\s*$/m", "", $raw);
$block = "\ndefine(\x27TALARIO_PARTNER_SYNC_PROD_READ\x27, true);\n"
       . "define(\x27TALARIO_PARTNER_SYNC_TOKEN_HASH\x27, \x27sha256:" . $hash . "\x27);\n";
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
  rm -f "$TMP" "$RESPONSE" "$HEADER"
  exit "$rc"
}
trap cleanup EXIT
trap 'exit 130' INT
trap 'exit 143' TERM

mv "$TMP" "$CONFIG"
CONFIG_REPLACED=1

printf 'header = "Authorization: Bearer %s"\n' "$TOKEN" > "$HEADER"
chmod 600 "$HEADER"

HTTP="$("$CURL" -sS -o "$RESPONSE" -w '%{http_code}' --max-time 30 \
  --resolve talario.ru:443:127.0.0.1 \
  --config "$HEADER" \
  "https://talario.ru/index.php?dispatch=talario_analytics.catalog&limit=1")"
[ "$HTTP" = "200" ] || fail "catalog smoke returned HTTP $HTTP" 73

"$PHP" -r '
$d = json_decode(file_get_contents($argv[1]), true);
if (!is_array($d) || ($d["schema_version"] ?? "") !== "partner-sync.catalog.v1") {
    fwrite(STDERR, "unexpected catalog response\n");
    exit(4);
}
' "$RESPONSE" || fail "catalog schema smoke failed" 74

SUCCESS=1

rm -f "$RESPONSE" "$HEADER"
rm -f "$BACKUP"

echo "BOOTSTRAP_OK"
echo "RAW_TOKEN_FOLLOWS_ONCE"
printf '%s\n' "$TOKEN"
