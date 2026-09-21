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

  "talario-prod-partner-sync-bootstrap")
    mark_dispatcher
    echo "OPERATION=prod-partner-sync-bootstrap"

    PROD_ROOT="/home/t/tyman5tb/talario.ru/public_html"
    SAFE_HOME="/home/t/tyman5tb"
    BOOTSTRAP_REL="ops/beget/bootstrap-partner-sync-prod-read.sh"
    EXPECTED_PROD_COMMIT="c27310ec82c38fcf574ca0c56f0c39ece3f5c66d"

    [ -d "$PROD_ROOT" ] || fail "PROD root missing" 71
    [ "$(git -C "$PROD_ROOT" rev-parse --abbrev-ref HEAD)" = "prod" ] || fail "PROD is not on prod branch" 72
    [ -z "$(git -C "$PROD_ROOT" status --porcelain)" ] || fail "PROD worktree must be clean" 73

    git -C "$PROD_ROOT" fetch --quiet origin prod \
      || fail "failed to fetch origin/prod" 74

    CURRENT_HEAD="$(git -C "$PROD_ROOT" rev-parse HEAD 2>/dev/null)" \
      || fail "failed to read PROD HEAD" 75
    REMOTE_HEAD="$(git -C "$PROD_ROOT" rev-parse origin/prod 2>/dev/null)" \
      || fail "failed to read origin/prod" 76

    [ "$CURRENT_HEAD" = "$EXPECTED_PROD_COMMIT" ] \
      || fail "PROD HEAD is not the reviewed bootstrap commit" 77
    [ "$REMOTE_HEAD" = "$EXPECTED_PROD_COMMIT" ] \
      || fail "origin/prod moved beyond the reviewed bootstrap commit" 78

    git -C "$PROD_ROOT" cat-file -e "$EXPECTED_PROD_COMMIT:$BOOTSTRAP_REL" \
      || fail "reviewed bootstrap blob unavailable" 79

    git -C "$PROD_ROOT" show "$EXPECTED_PROD_COMMIT:$BOOTSTRAP_REL" \
      | /usr/bin/env -i \
          HOME="$SAFE_HOME" \
          PATH="/usr/local/bin:/usr/bin:/bin" \
          /usr/bin/bash -s
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
