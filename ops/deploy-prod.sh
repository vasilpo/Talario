#!/usr/bin/env bash
set -euo pipefail

LIVE_DIR="/home/t/tyman5tb/talario.ru/public_html"
EXPECTED_BRANCH="prod"
REMOTE_URL="https://github.com/vasilpo/Talario.git"

cd "$LIVE_DIR"

mode="${1:-preflight}"
case "$mode" in
  preflight|deploy) ;;
  *)
    echo "Usage: $0 [preflight|deploy]"
    exit 2
    ;;
esac

branch="$(git rev-parse --abbrev-ref HEAD)"
[ "$branch" = "$EXPECTED_BRANCH" ] || { echo "ERROR: live branch is $branch, expected $EXPECTED_BRANCH"; exit 1; }

if [ -n "$(git diff --cached --name-only)" ]; then
  echo "ERROR: staged changes exist"
  exit 1
fi

unexpected_tracked=0
while IFS= read -r file; do
  case "$file" in
    design/themes/responsive/css/addons/hybrid_auth/styles.less) ;;
    *)
      echo "ERROR: unexpected tracked change: $file"
      unexpected_tracked=1
      ;;
  esac
done < <(git diff --name-only)
[ "$unexpected_tracked" -eq 0 ] || exit 1

unexpected_untracked=0
while IFS= read -r file; do
  case "$file" in
    devcopy/*|local_conf.php|local_conf.php1) ;;
    *)
      echo "ERROR: unexpected untracked file: $file"
      unexpected_untracked=1
      ;;
  esac
done < <(git ls-files --others --exclude-standard)
[ "$unexpected_untracked" -eq 0 ] || exit 1

git fetch --quiet "$REMOTE_URL" prod
target="$(git rev-parse FETCH_HEAD)"
current="$(git rev-parse HEAD)"

git merge-base --is-ancestor "$current" "$target" || {
  echo "ERROR: current live HEAD is not an ancestor of target prod"
  exit 1
}

http="$(curl -L -sS -o /dev/null -w '%{http_code}' --max-time 20 https://talario.ru/ || true)"
case "$http" in
  200|301|302) ;;
  *) echo "ERROR: baseline HTTP health failed: $http"; exit 1 ;;
esac

echo "PRECHECK_OK current=$current target=$target http=$http"

[ "$mode" = "deploy" ] || exit 0

if [ "${TALARIO_CONFIRM_PROD_DEPLOY:-}" != "$target" ]; then
  echo "ERROR: set TALARIO_CONFIRM_PROD_DEPLOY=$target to confirm this exact deployment"
  exit 1
fi

backup="$HOME/.talario-prod-head-before-deploy"
printf '%s\n' "$current" > "$backup"
chmod 600 "$backup"

git reset --hard "$target"

if [ -d var/cache ]; then
  find var/cache -mindepth 1 -maxdepth 1 -exec rm -rf {} +
fi

http_after="$(curl -L -sS -o /dev/null -w '%{http_code}' --max-time 20 https://talario.ru/ || true)"
case "$http_after" in
  200|301|302) ;;
  *)
    echo "ERROR: post-deploy HTTP health failed: $http_after"
    echo "ROLLBACK: resetting to $current"
    git reset --hard "$current"
    exit 1
    ;;
esac

[ "$(git rev-parse HEAD)" = "$target" ] || {
  echo "ERROR: live HEAD mismatch after deploy"
  git reset --hard "$current"
  exit 1
}

echo "DEPLOY_OK head=$target http=$http_after previous=$current"
