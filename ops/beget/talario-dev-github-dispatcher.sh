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

  "talario-dev-ops partner-sync-apply")
    mark_dispatcher
    echo "OPERATION=partner-sync-apply"

    LOCAL_HEAD="$(git rev-parse HEAD 2>/dev/null || true)"
    REMOTE_HEAD="$(git rev-parse refs/remotes/origin/development 2>/dev/null || true)"
    [ -n "$LOCAL_HEAD" ] && [ "$LOCAL_HEAD" = "$REMOTE_HEAD" ] \
      || fail "partner sync apply requires deployed protected development HEAD" 71
    [ -z "$(git status --porcelain=v1 --untracked-files=all)" ] \
      || fail "partner sync apply requires a clean worktree" 72
    echo "APPLY_HEAD=$LOCAL_HEAD"

    EXPECTED_BLOB="$(git rev-parse 'HEAD:ops/partner-sync-apply.php' 2>/dev/null || true)"
    [ -n "$EXPECTED_BLOB" ] || fail "partner sync CLI blob is unavailable" 73

    [ "$DEV_COPY" = "/home/t/tyman5tb/talario.ru/public_html/dev_copy" ] \
      && [ -d "$DEV_COPY/ops" ] && [ -w "$DEV_COPY/ops" ] \
      || fail "partner sync CLI temp directory is unavailable" 74

    TMP_RUNNER="$(mktemp "$DEV_COPY/ops/.partner-sync-apply.XXXXXX.php")" \
      || fail "partner sync CLI temp runner could not be created" 75
    [ -n "$TMP_RUNNER" ] && [ -f "$TMP_RUNNER" ] \
      || fail "partner sync CLI temp runner is unavailable" 75

    cleanup_partner_sync_runner() {
      rm -f -- "$TMP_RUNNER"
    }
    trap cleanup_partner_sync_runner EXIT HUP INT TERM

    git cat-file blob "$EXPECTED_BLOB" > "$TMP_RUNNER"
    chmod 600 "$TMP_RUNNER"
    [ "$(git hash-object -- "$TMP_RUNNER")" = "$EXPECTED_BLOB" ] \
      || fail "partner sync CLI temp runner failed integrity check" 76

    set +e
    /usr/local/bin/php8.2 "$TMP_RUNNER"
    RC=$?
    set -e
    cleanup_partner_sync_runner
    trap - EXIT HUP INT TERM
    echo "APPLY_RC=$RC"
    exit "$RC"
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
