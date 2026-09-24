#!/usr/bin/env bash
set -euo pipefail

DEV_COPY="/home/t/tyman5tb/talario.ru/public_html/dev_copy"
SOURCE="$DEV_COPY/ops/beget/talario-dev-github-dispatcher.sh"
TARGET="$HOME/.local/bin/talario-dev-github-dispatcher"
STATE_DIR="$HOME/.local/state/talario/dispatcher-backups"
REQUIRED_COMMIT="8e38f1d899593ba47d143aec5f20fc9a91500851"

fail() {
  printf 'ERROR: %s\n' "$1" >&2
  exit "${2:-64}"
}

[ -d "$DEV_COPY/.git" ] || fail "dev_copy git root missing" 65
[ "$(/usr/bin/realpath "$DEV_COPY")" = "$DEV_COPY" ] || fail "unexpected dev_copy root" 65
[ "$(/usr/bin/git -C "$DEV_COPY" rev-parse --abbrev-ref HEAD)" = "development" ] || fail "dev_copy is not on development" 66
/usr/bin/git -C "$DEV_COPY" merge-base --is-ancestor "$REQUIRED_COMMIT" HEAD   || fail "reviewed dispatcher commit is not present" 67

[ -f "$SOURCE" ] && [ ! -L "$SOURCE" ] || fail "unsafe dispatcher source" 68
[ -f "$TARGET" ] && [ ! -L "$TARGET" ] || fail "unsafe live dispatcher target" 69

/usr/bin/bash -n "$SOURCE" || fail "dispatcher source failed bash syntax check" 70
/usr/bin/grep -Fq '"talario-analytics-prod-sync")' "$SOURCE"   || fail "reviewed analytics PROD sync command missing" 71
/usr/bin/grep -Fq 'fail "SSH command is not allowlisted" 68' "$SOURCE"   || fail "fail-closed dispatcher guard missing" 71
/usr/bin/grep -Fq 'EXPECTED_PROD_SHA="2dea53c94eecc33d84980bab1b808a35258e03f7"' "$SOURCE"   || fail "exact PROD SHA guard missing" 71

TARGET_UID="$(/usr/bin/stat -c '%u' "$TARGET")"
CURRENT_UID="$(/usr/bin/id -u)"
[ "$TARGET_UID" = "$CURRENT_UID" ] || fail "live dispatcher owner mismatch" 72

mkdir -p "$STATE_DIR"
chmod 700 "$STATE_DIR"
[ -d "$STATE_DIR" ] && [ ! -L "$STATE_DIR" ] || fail "unsafe backup directory" 73

STAMP="$(/usr/bin/date -u +%Y%m%dT%H%M%SZ)"
BACKUP="$STATE_DIR/talario-dev-github-dispatcher.$STAMP.bak"
TMP="$STATE_DIR/talario-dev-github-dispatcher.$STAMP.tmp"

/usr/bin/cp -p "$TARGET" "$BACKUP"
chmod 600 "$BACKUP"
/usr/bin/cp "$SOURCE" "$TMP"
chmod 700 "$TMP"
/usr/bin/bash -n "$TMP" || fail "temporary dispatcher failed syntax check" 74

SUCCESS=0
cleanup() {
  rc=$?
  if [ "$SUCCESS" -ne 1 ]; then
    /usr/bin/cp -p "$BACKUP" "$TARGET" 2>/dev/null || true
  fi
  rm -f "$TMP"
  exit "$rc"
}
trap cleanup EXIT
trap 'exit 130' INT
trap 'exit 143' TERM

/usr/bin/mv "$TMP" "$TARGET"
/usr/bin/bash -n "$TARGET" || fail "installed dispatcher failed syntax check" 75
/usr/bin/grep -Fq '"talario-analytics-prod-sync")' "$TARGET"   || fail "installed dispatcher verification failed" 76

SUCCESS=1
trap - EXIT INT TERM

echo "DISPATCHER_INSTALL=PASS"
echo "REVIEWED_COMMIT_PRESENT=PASS"
echo "ANALYTICS_PROD_SYNC_ALLOWLIST=PASS"
echo "BACKUP_CREATED=$BACKUP"
