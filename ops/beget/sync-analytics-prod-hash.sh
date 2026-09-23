#!/usr/bin/env bash
set -euo pipefail

PATH="/usr/local/bin:/usr/bin:/bin"
export PATH
umask 077

ROOT="/home/t/tyman5tb/talario.ru/public_html"
CONFIG="$ROOT/config.local.php"
PHP="/usr/local/bin/php8.2"
STATE_DIR="$HOME/.local/state/talario/analytics-prod"
EXPECTED_BRANCH="prod"
HASH="${1:-}"

fail() {
  printf 'ERROR: %s\n' "$1" >&2
  exit "${2:-64}"
}

[[ "$HASH" =~ ^[a-f0-9]{64}$ ]] || fail "invalid sha256" 65
[ -d "$ROOT" ] || fail "PROD root not found" 66
cd "$ROOT"
[ "$(git rev-parse --abbrev-ref HEAD)" = "$EXPECTED_BRANCH" ] || fail "PROD is not on prod branch" 67
[ -f "$CONFIG" ] && [ ! -L "$CONFIG" ] || fail "unsafe config.local.php" 68
[ -x "$PHP" ] || fail "php8.2 unavailable" 69

mkdir -p "$STATE_DIR"
chmod 700 "$STATE_DIR"
STAMP="$(date -u +%Y%m%dT%H%M%SZ)"
BACKUP="$STATE_DIR/config.local.php.$STAMP.bak"
TMP="$STATE_DIR/config.local.php.$STAMP.tmp"

cp -p "$CONFIG" "$BACKUP"
chmod 600 "$BACKUP"

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
' || fail "config transformation failed" 70

chmod --reference="$CONFIG" "$TMP"
"$PHP" -l "$TMP" >/dev/null || fail "generated config failed PHP lint" 71

SUCCESS=0
cleanup() {
  rc=$?
  if [ "$SUCCESS" -ne 1 ]; then
    cp -p "$BACKUP" "$CONFIG" || true
  fi
  rm -f "$TMP"
  exit "$rc"
}
trap cleanup EXIT
trap 'exit 130' INT
trap 'exit 143' TERM

mv "$TMP" "$CONFIG"

STORED="$("$PHP" -r '
define("AREA", "C");
require $argv[1] . "/config.php";
echo defined("TALARIO_ANALYTICS_TOKEN_HASH") ? TALARIO_ANALYTICS_TOKEN_HASH : "";
' "$ROOT")"
[ "$STORED" = "sha256:$HASH" ] || fail "stored hash verification failed" 72

SUCCESS=1
rm -f "$BACKUP"
trap - EXIT INT TERM

echo "ANALYTICS_PROD_HASH_SYNC=PASS"
echo "CONFIG_BACKUP_ROLLBACK=PASS"
