#!/usr/bin/env bash
set -euo pipefail

LIVE_DIR="/home/t/tyman5tb/talario.ru/public_html"
EXPECTED_BRANCH="prod"
REMOTE_URL="https://github.com/vasilpo/Talario.git"
HEALTH_URL="https://talario.ru/"
HEALTH_MARKER="Talario"

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
[ "$branch" = "$EXPECTED_BRANCH" ] || {
  echo "ERROR: live branch is $branch, expected $EXPECTED_BRANCH"
  exit 1
}

if [ -n "$(git diff --cached --name-only)" ]; then
  echo "ERROR: staged changes exist"
  exit 1
fi

if [ -n "$(git diff --name-only)" ]; then
  echo "ERROR: tracked live changes exist; production must be clean before deployment"
  git diff --name-status
  exit 1
fi

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

check_health() {
  local tmp
  tmp="$(mktemp)"
  trap 'rm -f "$tmp"' RETURN

  local http
  http="$(curl -L -sS -o "$tmp" -w '%{http_code}' --max-time 20 "$HEALTH_URL" || true)"
  case "$http" in
    200|301|302) ;;
    *)
      echo "ERROR: HTTP health failed: $http"
      return 1
      ;;
  esac

  if ! grep -qi "$HEALTH_MARKER" "$tmp"; then
    echo "ERROR: application health marker not found"
    return 1
  fi

  printf '%s' "$http"
}

http="$(check_health)"
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

if [ -L var/cache ]; then
  echo "ERROR: var/cache is a symlink; refusing to clear it"
  git reset --hard "$current"
  exit 1
fi

if [ -d var/cache ]; then
  cache_real="$(readlink -f var/cache)"
  case "$cache_real" in
    "$LIVE_DIR"/var/cache) ;;
    *)
      echo "ERROR: var/cache resolves outside live directory"
      git reset --hard "$current"
      exit 1
      ;;
  esac
  find "$cache_real" -mindepth 1 -maxdepth 1 -exec rm -rf -- {} +
fi

if ! http_after="$(check_health)"; then
  echo "ROLLBACK: resetting to $current"
  git reset --hard "$current"
  exit 1
fi

if [ "$(git rev-parse HEAD)" != "$target" ]; then
  echo "ERROR: live HEAD mismatch after deploy"
  git reset --hard "$current"
  exit 1
fi

echo "DEPLOY_OK head=$target http=$http_after previous=$current"
