#!/usr/bin/env bash
set -euo pipefail
PATH="/usr/local/bin:/usr/bin:/bin"
export PATH

DEV_COPY="/home/t/tyman5tb/talario.ru/public_html/dev_copy"
EXPECTED_PATH="$DEV_COPY"
BACKUP_DIR="/home/t/tyman5tb/.local/state/talario/dev-copy-backups"
REQUEST="${SSH_ORIGINAL_COMMAND:-}"

fail() {
  printf 'ERROR: %s\n' "$1" >&2
  exit "${2:-64}"
}

cd "$DEV_COPY"
[ "$(realpath .)" = "$EXPECTED_PATH" ] || fail "unexpected active directory" 65
[ "$(git rev-parse --abbrev-ref HEAD)" = "development" ] || fail "dev_copy is not on development branch" 66

mark_dispatcher() {
  echo "DISPATCHER=talario-dev-github-v1"
}

case "$REQUEST" in
  talario-dev-deploy)
    mark_dispatcher
    [ -z "$(git status --porcelain)" ] || fail "dev_copy has local changes; refusing to deploy" 67
    git remote set-url origin https://github.com/vasilpo/Talario.git
    git pull --ff-only origin development
    if [ -d var/cache ] && [ ! -L var/cache ]; then
      find var/cache -mindepth 1 -maxdepth 1 -exec rm -rf -- {} +
    fi
    echo "DEPLOY_OK $(git rev-parse --short HEAD)"
    ;;

  "talario-dev-ops status")
    mark_dispatcher
    echo "OPERATION=status"
    echo "BRANCH=$(git rev-parse --abbrev-ref HEAD)"
    echo "HEAD=$(git rev-parse HEAD)"
    if [ -n "$(git status --porcelain)" ]; then
      echo "WORKTREE=DIRTY"
    else
      echo "WORKTREE=CLEAN"
    fi
    echo "PHP=$(php8.2 -r 'echo PHP_VERSION;')"
    php8.2 -r '
      $path = "config.local.php";
      $content = is_file($path) ? (string) file_get_contents($path) : "";
      echo "DEV_COPY_FLAG=" . (strpos($content, "TALARIO_PARTNER_SYNC_DEV_COPY") !== false ? "PRESENT" : "MISSING") . PHP_EOL;
      echo "PARTNER_SYNC_HASH=" . (preg_match("/TALARIO_PARTNER_SYNC_TOKEN_HASH.*sha256:[a-f0-9]{64}/", $content) ? "PRESENT" : "MISSING") . PHP_EOL;
    '
    ;;

  "talario-dev-ops git-status")
    mark_dispatcher
    echo "OPERATION=git-status"
    echo "HEAD=$(git rev-parse HEAD)"
    DIRTY_COUNT="$(git status --porcelain | wc -l | tr -d ' ')"
    echo "DIRTY_COUNT=$DIRTY_COUNT"
    ;;

  "talario-dev-ops php-lint")
    mark_dispatcher
    echo "OPERATION=php-lint"
    while IFS= read -r -d '' file; do
      php8.2 -l -- "$file" >/dev/null
    done < <(find app/addons/talario_analytics -type f -name '*.php' -print0 | sort -z)
    echo "PHP_LINT=OK"
    ;;

  "talario-partner-sync-dry-run")
    mark_dispatcher
    echo "OPERATION=partner-sync-dry-run"

    STATE_DIR="$HOME/.local/state/talario/partner-sync"
    mkdir -p "$STATE_DIR"
    chmod 700 "$STATE_DIR"
    [ -d "$STATE_DIR" ] && [ ! -L "$STATE_DIR" ] || fail "invalid partner sync state directory" 74

    PAYLOAD_FILE="$(/usr/bin/mktemp "$STATE_DIR/request.XXXXXX.json")"
    chmod 600 "$PAYLOAD_FILE"
    trap 'rm -f -- "$PAYLOAD_FILE"' EXIT HUP INT TERM

    # Read at most 20 MiB + 1 byte, with a hard receive timeout.
    /usr/bin/timeout 30s /usr/bin/head -c 20971521 > "$PAYLOAD_FILE" || fail "partner sync payload receive timeout" 75
    PAYLOAD_SIZE="$(/usr/bin/wc -c < "$PAYLOAD_FILE" | /usr/bin/tr -d ' ')"
    [ "$PAYLOAD_SIZE" -gt 0 ] || fail "partner sync payload is empty" 71
    [ "$PAYLOAD_SIZE" -le 20971520 ] || fail "partner sync payload exceeds 20 MiB" 72

    VALIDATION="$(/usr/bin/env -u PHPRC -u PHP_INI_SCAN_DIR /usr/local/bin/php8.2 -n -r '
      $path = $argv[1];
      $raw = (string) file_get_contents($path);
      $payload = json_decode($raw, true);
      if (!is_array($payload) || json_last_error() !== JSON_ERROR_NONE) {
          fwrite(STDERR, "INVALID_JSON\n");
          exit(10);
      }
      if (!array_key_exists("dry_run", $payload) || $payload["dry_run"] !== true) {
          fwrite(STDERR, "DRY_RUN_REQUIRED\n");
          exit(11);
      }
      echo "OK";
    ' "$PAYLOAD_FILE" 2>&1)" || fail "partner sync payload validation failed" 73
    [ "$VALIDATION" = "OK" ] || fail "partner sync payload validation failed" 73

    [ "$DEV_COPY" = "/home/t/tyman5tb/talario.ru/public_html/dev_copy" ] || fail "unexpected dev_copy root" 76
    [ -x /usr/local/bin/php8.2 ] || fail "required PHP binary unavailable" 77

    RUNNER_REL="ops/partner-sync-apply.php"
    RUNNER_PATH="$DEV_COPY/$RUNNER_REL"
    [ -f "$RUNNER_PATH" ] && [ ! -L "$RUNNER_PATH" ] || fail "partner sync CLI runner unavailable" 78
    [ -z "$(git status --porcelain --untracked-files=all)" ] || fail "dev_copy worktree must be clean for partner sync" 79

    EXPECTED_RUNNER_HASH="$(git show "HEAD:$RUNNER_REL" | /usr/bin/sha256sum | /usr/bin/awk '{print $1}')"
    ACTUAL_RUNNER_HASH="$(/usr/bin/sha256sum "$RUNNER_PATH" | /usr/bin/awk '{print $1}')"
    [ -n "$EXPECTED_RUNNER_HASH" ] && [ "$EXPECTED_RUNNER_HASH" = "$ACTUAL_RUNNER_HASH" ] \
      || fail "partner sync CLI runner integrity check failed" 80

    /usr/bin/env -u PHPRC -u PHP_INI_SCAN_DIR /usr/local/bin/php8.2 "$RUNNER_PATH" < "$PAYLOAD_FILE"
    ;;

  "talario-dev-ops worktree-repair")
    mark_dispatcher
    echo "OPERATION=worktree-repair"

    mapfile -d '' -t STATUS_ITEMS < <(git status --porcelain=v1 -z --untracked-files=all)
    if [ "${#STATUS_ITEMS[@]}" -eq 0 ]; then
      echo "REPAIR=NOOP"
      echo "WORKTREE=CLEAN"
      exit 0
    fi

    APPROVED_PATHS=()
    for item in "${STATUS_ITEMS[@]}"; do
      [[ "$item" == "?? config.local.php.bak-partner-sync-"* ]] || fail "worktree contains changes outside the approved Partner Sync backup pattern" 69
      path="${item:3}"
      [[ "$path" == config.local.php.bak-partner-sync-* ]] || fail "unexpected repair path" 69
      [ -f "$path" ] && [ ! -L "$path" ] || fail "approved backup candidate is not a regular file" 69
      APPROVED_PATHS+=("$path")
    done

    mkdir -p "$BACKUP_DIR"
    chmod 700 "$BACKUP_DIR"

    repaired=0
    stamp="$(date -u +%Y%m%dT%H%M%SZ)"
    for path in "${APPROVED_PATHS[@]}"; do
      dest="$BACKUP_DIR/${path}.${stamp}.${repaired}"
      mv -- "$path" "$dest"
      chmod 600 "$dest"
      repaired=$((repaired + 1))
    done

    [ -z "$(git status --porcelain)" ] || fail "worktree still dirty after approved repair" 70
    echo "REPAIRED_COUNT=$repaired"
    echo "WORKTREE=CLEAN"
    ;;

  *)
    fail "SSH command is not allowlisted" 68
    ;;
esac
