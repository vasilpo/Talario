#!/usr/bin/env bash
set -euo pipefail
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

  "talario-partner-sync-enable-penaty-pilot")
    mark_dispatcher
    echo "OPERATION=partner-sync-enable-penaty-pilot"

    CONFIG="$DEV_COPY/config.local.php"
    STATE_DIR="$HOME/.local/state/talario/partner-sync"
    mkdir -p "$STATE_DIR"
    chmod 700 "$STATE_DIR"
    [ -f "$CONFIG" ] && [ ! -L "$CONFIG" ] || fail "invalid dev_copy local config" 85

    CONFIG_UID="$(/usr/bin/stat -c '%u' "$CONFIG")"
    CURRENT_UID="$(/usr/bin/id -u)"
    [ "$CONFIG_UID" = "$CURRENT_UID" ] || fail "dev_copy local config owner mismatch" 85

    BACKUP="$STATE_DIR/config.local.php.before-penaty.$(/usr/bin/date -u +%Y%m%dT%H%M%SZ)"
    /bin/cat "$CONFIG" > "$BACKUP"
    chmod 600 "$BACKUP"

    CONFIG_TMP="$(/usr/bin/mktemp "$STATE_DIR/config.XXXXXX.php")"
    chmod 600 "$CONFIG_TMP"
    trap 'rm -f -- "$CONFIG_TMP"' EXIT HUP INT TERM

    /usr/local/bin/php8.2 -r '
      $src = $argv[1];
      $dst = $argv[2];
      $content = file_get_contents($src);
      if (!is_string($content) || strpos($content, "<?php") === false) {
          fwrite(STDERR, "CONFIG_READ_FAILED\n");
          exit(10);
      }
      $updates = [
          "TALARIO_PARTNER_SYNC_DEV_WRITE" => "true",
          "TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS" => "\x2739\x27",
      ];
      foreach ($updates as $name => $value) {
          $pattern = "/define\\s*\\(\\s*[\x27\x22]" . preg_quote($name, "/") . "[\x27\x22]\\s*,\\s*[^;]+\\);/";
          $count = preg_match_all($pattern, $content);
          if ($count > 1) {
              fwrite(STDERR, "DUPLICATE_CONFIG_DEFINE\n");
              exit(11);
          }
          $line = "define(\x27" . $name . "\x27, " . $value . ");";
          if ($count === 1) {
              $content = preg_replace($pattern, $line, $content, 1);
          } else {
              $pos = strrpos($content, "?>");
              if ($pos === false) {
                  $content = rtrim($content) . PHP_EOL . $line . PHP_EOL;
              } else {
                  $content = substr($content, 0, $pos) . $line . PHP_EOL . substr($content, $pos);
              }
          }
      }
      if (file_put_contents($dst, $content) === false) {
          fwrite(STDERR, "CONFIG_WRITE_FAILED\n");
          exit(12);
      }
    ' "$CONFIG" "$CONFIG_TMP" || fail "failed to prepare Penaty pilot config" 85

    /usr/local/bin/php8.2 -l "$CONFIG_TMP" >/dev/null || fail "Penaty pilot config syntax invalid" 85
    /bin/cat "$CONFIG_TMP" > "$CONFIG"
    chmod 600 "$CONFIG"

    /usr/local/bin/php8.2 -r '
      $content = file_get_contents($argv[1]);
      $ok_write = preg_match("/define\\s*\\(\\s*[\x27\x22]TALARIO_PARTNER_SYNC_DEV_WRITE[\x27\x22]\\s*,\\s*true\\s*\\);/", $content);
      $ok_company = preg_match("/define\\s*\\(\\s*[\x27\x22]TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS[\x27\x22]\\s*,\\s*[\x27\x22]39[\x27\x22]\\s*\\);/", $content);
      exit(($ok_write && $ok_company) ? 0 : 13);
    ' "$CONFIG" || fail "Penaty pilot config verification failed" 85

    echo "PILOT_CONFIG=OK"
    echo "PILOT_COMPANY_ID=39"
    echo "DEV_WRITE=ENABLED"
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

    PHP_REAL="$(/usr/bin/realpath /usr/local/bin/php8.2)"
    [ -n "$PHP_REAL" ] && [ -f "$PHP_REAL" ] && [ -x "$PHP_REAL" ] || fail "trusted PHP binary resolution failed" 82
    PHP_UID="$(/usr/bin/stat -c '%u' "$PHP_REAL")"
    [ "$PHP_UID" = "0" ] || fail "trusted PHP binary owner mismatch" 82
    PHP_MODE="$(/usr/bin/stat -c '%a' "$PHP_REAL")"
    (( (8#$PHP_MODE & 0022) == 0 )) || fail "trusted PHP binary is group/world writable" 82

    VALIDATION="$(/usr/bin/env -i HOME="$HOME" PATH="/usr/bin:/bin" "$PHP_REAL" -n -r '
      $path = $argv[1];
      $raw = file_get_contents($path);
      if ($raw === false) {
          fwrite(STDERR, "PAYLOAD_READ_FAILED\n");
          exit(9);
      }
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
    [ -x /usr/bin/git ] || fail "required git binary unavailable" 81

    RUNNER_REL="ops/partner-sync-apply.php"
    EXPECTED_RUNNER_SHA256="74bb7882e0f40b7984e66ed12985cf497c257b1c10f22c91efaea36fba55d407"
    [ -z "$(/usr/bin/git -C "$DEV_COPY" status --porcelain --untracked-files=all)" ] || fail "dev_copy worktree must be clean for partner sync" 79
    RUNNER_COMMIT="$(/usr/bin/git -C "$DEV_COPY" rev-parse HEAD)"
    [ -n "$RUNNER_COMMIT" ] || fail "partner sync runner commit resolution failed" 80

    RUNNER_TMP="$(/usr/bin/mktemp "$STATE_DIR/runner.XXXXXX.php")"
    chmod 600 "$RUNNER_TMP"
    trap 'rm -f -- "$PAYLOAD_FILE" "$RUNNER_TMP"' EXIT HUP INT TERM
    /usr/bin/git -C "$DEV_COPY" show "$RUNNER_COMMIT:$RUNNER_REL" > "$RUNNER_TMP" \
      || fail "partner sync CLI runner integrity check failed" 80
    [ -s "$RUNNER_TMP" ] || fail "partner sync CLI runner integrity check failed" 80
    ACTUAL_RUNNER_SHA256="$(/usr/bin/sha256sum "$RUNNER_TMP" | /usr/bin/awk '{print $1}')"
    [ "$ACTUAL_RUNNER_SHA256" = "$EXPECTED_RUNNER_SHA256" ] \
      || fail "partner sync CLI runner is not allowlisted" 84

    RUNNER_UID="$(/usr/bin/id -u)"
    [[ "$RUNNER_UID" =~ ^[0-9]+$ ]] || fail "runner uid resolution failed" 82

    set +e
    /usr/bin/timeout --signal=TERM --kill-after=5s 60s \
      /usr/bin/env -i HOME="$HOME" PATH="/usr/bin:/bin" \
      TALARIO_PARTNER_SYNC_ROOT="$DEV_COPY" TALARIO_PARTNER_SYNC_RUNNER_UID="$RUNNER_UID" \
      "$PHP_REAL" "$RUNNER_TMP" < "$PAYLOAD_FILE"
    RUN_RC=$?
    set -e
    case "$RUN_RC" in
      124|137) fail "partner sync dry-run execution timeout" 83 ;;
      *) exit "$RUN_RC" ;;
    esac
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
