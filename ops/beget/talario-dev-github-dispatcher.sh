#!/usr/bin/env bash
set -euo pipefail

DEV_COPY="/home/t/tyman5tb/talario.ru/public_html/dev_copy"
EXPECTED_PATH="$DEV_COPY"
REQUEST="${SSH_ORIGINAL_COMMAND:-}"

fail() {
  printf 'ERROR: %s\n' "$1" >&2
  exit "${2:-64}"
}

[ "$(realpath "$DEV_COPY")" = "$EXPECTED_PATH" ] || fail "unexpected dev_copy path" 65
cd "$DEV_COPY"

[ "$(git rev-parse --abbrev-ref HEAD)" = "development" ] || fail "dev_copy is not on development branch" 66

case "$REQUEST" in
  talario-dev-deploy)
    [ -z "$(git status --porcelain)" ] || {
      echo "ERROR: dev_copy has local changes; refusing to deploy"
      git status --short
      exit 67
    }
    git remote set-url origin https://github.com/vasilpo/Talario.git
    git pull --ff-only origin development
    if [ -d var/cache ] && [ ! -L var/cache ]; then
      find var/cache -mindepth 1 -maxdepth 1 -exec rm -rf -- {} +
    fi
    echo "DEPLOY_OK $(git rev-parse --short HEAD)"
    ;;

  "talario-dev-ops status")
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
    echo "OPERATION=git-status"
    git status --short
    echo "HEAD=$(git rev-parse HEAD)"
    ;;

  "talario-dev-ops php-lint")
    echo "OPERATION=php-lint"
    find app/addons/talario_analytics -type f -name '*.php' -print0       | sort -z       | xargs -0 -n1 php8.2 -l
    echo "PHP_LINT_OK"
    ;;

  *)
    fail "SSH command is not allowlisted" 68
    ;;
esac
