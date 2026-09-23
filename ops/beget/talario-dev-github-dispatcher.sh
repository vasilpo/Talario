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

if [ "$REQUEST" = "talario-analytics-prod-sync" ]; then
  echo "DISPATCHER=talario-dev-github-v1"
  echo "OPERATION=analytics-prod-sync"

  PROD="/home/t/tyman5tb/talario.ru/public_html"
  EXPECTED_PROD_SHA="2dea53c94eecc33d84980bab1b808a35258e03f7"
  STATE_DIR="$HOME/.local/state/talario/analytics-prod"
  mkdir -p "$STATE_DIR"
  chmod 700 "$STATE_DIR"
  [ -d "$STATE_DIR" ] && [ ! -L "$STATE_DIR" ] || fail "invalid analytics state directory" 86

  HASH_FILE="$(/usr/bin/mktemp "$STATE_DIR/hash.XXXXXX")"
  DEPLOY_RUNNER="$(/usr/bin/mktemp "$STATE_DIR/deploy.XXXXXX.sh")"
  HASH_RUNNER="$(/usr/bin/mktemp "$STATE_DIR/hash-sync.XXXXXX.sh")"
  chmod 600 "$HASH_FILE" "$DEPLOY_RUNNER" "$HASH_RUNNER"
  trap 'rm -f -- "$HASH_FILE" "$DEPLOY_RUNNER" "$HASH_RUNNER"' EXIT HUP INT TERM

  /usr/bin/timeout 10s /usr/bin/head -c 66 > "$HASH_FILE" || fail "analytics hash receive timeout" 86
  HASH_SIZE="$(/usr/bin/wc -c < "$HASH_FILE" | /usr/bin/tr -d ' ')"
  [ "$HASH_SIZE" -ge 64 ] && [ "$HASH_SIZE" -le 65 ] || fail "invalid analytics hash size" 86
  HASH="$(/usr/bin/tr -d '\r\n' < "$HASH_FILE")"
  [[ "$HASH" =~ ^[a-f0-9]{64}$ ]] || fail "invalid analytics sha256" 86

  [ -d "$PROD/.git" ] || fail "PROD git root missing" 87
  [ "$(/usr/bin/realpath "$PROD")" = "$PROD" ] || fail "unexpected PROD root" 87
  [ "$(/usr/bin/git -C "$PROD" rev-parse --abbrev-ref HEAD)" = "prod" ] || fail "PROD branch mismatch" 87
  [ -z "$(/usr/bin/git -C "$PROD" status --porcelain --untracked-files=no)" ] || fail "PROD tracked worktree dirty" 87

  /usr/bin/git -C "$PROD" fetch --quiet https://github.com/vasilpo/Talario.git prod
  TARGET="$(/usr/bin/git -C "$PROD" rev-parse FETCH_HEAD)"
  [ "$TARGET" = "$EXPECTED_PROD_SHA" ] || fail "unexpected PROD target" 88

  /usr/bin/git -C "$PROD" show "$TARGET:ops/deploy-prod.sh" > "$DEPLOY_RUNNER" || fail "deploy runner extraction failed" 88
  /usr/bin/git -C "$PROD" show "$TARGET:ops/beget/sync-analytics-prod-hash.sh" > "$HASH_RUNNER" || fail "hash runner extraction failed" 88
  [ -s "$DEPLOY_RUNNER" ] && [ -s "$HASH_RUNNER" ] || fail "reviewed runner missing" 88
  chmod 700 "$DEPLOY_RUNNER" "$HASH_RUNNER"

  TALARIO_CONFIRM_PROD_DEPLOY="$TARGET" /usr/bin/bash "$DEPLOY_RUNNER" deploy
  [ "$(/usr/bin/git -C "$PROD" rev-parse HEAD)" = "$EXPECTED_PROD_SHA" ] || fail "exact PROD deploy verification failed" 89

  /usr/bin/bash "$HASH_RUNNER" "$HASH"
  /usr/bin/curl -fsS --max-time 20 https://talario.ru/ > "$STATE_DIR/storefront-health.tmp" || fail "storefront health request failed" 90
  /usr/bin/grep -qi 'Talario' "$STATE_DIR/storefront-health.tmp" || fail "storefront health marker missing" 90
  rm -f "$STATE_DIR/storefront-health.tmp"

  echo "PROD_EXACT_SHA=$EXPECTED_PROD_SHA"
  echo "ANALYTICS_PROD_SYNC=PASS"
  echo "STOREFRONT_HEALTH=PASS"
  exit 0
fi

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

    [ "$DEV_COPY" = "$EXPECTED_PATH" ] || fail "unexpected dev_copy root" 85
    [ "$(/usr/bin/realpath "$DEV_COPY")" = "$EXPECTED_PATH" ] || fail "unexpected dev_copy realpath" 85

    CONFIG="$DEV_COPY/config.local.php"
    EXPECTED_CONFIG="$EXPECTED_PATH/config.local.php"
    [ "$CONFIG" = "$EXPECTED_CONFIG" ] || fail "unexpected dev_copy config path" 85
    [ "$(/usr/bin/realpath "$CONFIG")" = "$EXPECTED_CONFIG" ] || fail "unexpected dev_copy config realpath" 85

    STATE_DIR="$HOME/.local/state/talario/partner-sync"
    mkdir -p "$STATE_DIR"
    chmod 700 "$STATE_DIR"
    [ -d "$STATE_DIR" ] && [ ! -L "$STATE_DIR" ] || fail "invalid partner sync state directory" 85

    CURRENT_UID="$(/usr/bin/id -u)"
    [[ "$CURRENT_UID" =~ ^[0-9]+$ ]] || fail "current uid resolution failed" 85
    BACKUP="$STATE_DIR/config.local.php.before-penaty.$(/usr/bin/date -u +%Y%m%dT%H%M%SZ)"

    /usr/local/bin/php8.2 -r '
      $path = $argv[1];
      $expected = $argv[2];
      $expected_uid = (int) $argv[3];
      $backup = $argv[4];

      $real = realpath($path);
      if ($real === false || $real !== $expected) {
          fwrite(STDERR, "CONFIG_PATH_MISMATCH\n");
          exit(10);
      }

      $fh = @fopen($path, "r+b");
      if (!is_resource($fh) || !flock($fh, LOCK_EX)) {
          fwrite(STDERR, "CONFIG_OPEN_FAILED\n");
          exit(11);
      }

      $st = fstat($fh);
      $lst = @lstat($path);
      if (
          !is_array($st)
          || !is_array($lst)
          || (($st["mode"] & 0170000) !== 0100000)
          || (int) $st["uid"] !== $expected_uid
          || (($st["mode"] & 0022) !== 0)
          || (int) $st["dev"] !== (int) $lst["dev"]
          || (int) $st["ino"] !== (int) $lst["ino"]
      ) {
          flock($fh, LOCK_UN);
          fclose($fh);
          fwrite(STDERR, "CONFIG_TRUST_FAILED\n");
          exit(12);
      }

      rewind($fh);
      $content = stream_get_contents($fh);
      if (!is_string($content) || strpos($content, "<?php") === false) {
          flock($fh, LOCK_UN);
          fclose($fh);
          fwrite(STDERR, "CONFIG_READ_FAILED\n");
          exit(13);
      }

      if (file_put_contents($backup, $content, LOCK_EX) === false) {
          flock($fh, LOCK_UN);
          fclose($fh);
          fwrite(STDERR, "CONFIG_BACKUP_FAILED\n");
          exit(14);
      }
      @chmod($backup, 0600);

      $updates = [
          "TALARIO_PARTNER_SYNC_DEV_WRITE" => "true",
          "TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS" => "\x2739\x27",
      ];
      foreach ($updates as $name => $value) {
          $pattern = "/define\\s*\\(\\s*[\x27\x22]" . preg_quote($name, "/") . "[\x27\x22]\\s*,\\s*[^;]+\\);/";
          $count = preg_match_all($pattern, $content);
          if ($count > 1) {
              flock($fh, LOCK_UN);
              fclose($fh);
              fwrite(STDERR, "DUPLICATE_CONFIG_DEFINE\n");
              exit(15);
          }
          $line = "define(\x27" . $name . "\x27, " . $value . ");";
          if ($count === 1) {
              $content = preg_replace($pattern, $line, $content, 1);
          } else {
              $pos = strrpos($content, "?>");
              $content = $pos === false
                  ? rtrim($content) . PHP_EOL . $line . PHP_EOL
                  : substr($content, 0, $pos) . $line . PHP_EOL . substr($content, $pos);
          }
      }

      rewind($fh);
      if (!ftruncate($fh, 0) || fwrite($fh, $content) !== strlen($content) || !fflush($fh)) {
          flock($fh, LOCK_UN);
          fclose($fh);
          fwrite(STDERR, "CONFIG_WRITE_FAILED\n");
          exit(16);
      }
      if (function_exists("fsync")) {
          @fsync($fh);
      }

      rewind($fh);
      $written = stream_get_contents($fh);
      $ok_write = preg_match("/define\\s*\\(\\s*[\x27\x22]TALARIO_PARTNER_SYNC_DEV_WRITE[\x27\x22]\\s*,\\s*true\\s*\\);/", $written);
      $ok_company = preg_match("/define\\s*\\(\\s*[\x27\x22]TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS[\x27\x22]\\s*,\\s*[\x27\x22]39[\x27\x22]\\s*\\);/", $written);

      flock($fh, LOCK_UN);
      fclose($fh);

      if (!$ok_write || !$ok_company) {
          fwrite(STDERR, "CONFIG_VERIFY_FAILED\n");
          exit(17);
      }
    ' "$CONFIG" "$EXPECTED_CONFIG" "$CURRENT_UID" "$BACKUP" || fail "Penaty pilot config update failed" 85

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
