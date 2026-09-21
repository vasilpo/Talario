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

  "talario-dev-ops partner-sync-smoke")
    mark_dispatcher
    echo "OPERATION=partner-sync-smoke"

    TOKEN_FILE="$HOME/.local/state/talario/partner-sync.token"
    [ -f "$TOKEN_FILE" ] && [ ! -L "$TOKEN_FILE" ] || fail "Partner Sync token file is unavailable" 71
    [ "$(stat -c '%a' "$TOKEN_FILE")" = "600" ] || fail "Partner Sync token file permissions are not 600" 71

    token="$(cat "$TOKEN_FILE")"
    [ "${#token}" -ge 32 ] || fail "Partner Sync token is invalid" 71

    store_access_key="$(php8.2 -r '
      $config = [];
      require "config.local.php";
      $required = ["db_host","db_user","db_password","db_name","table_prefix"];
      foreach ($required as $key) {
          if (!isset($config[$key]) || $config[$key] === "") {
              fwrite(STDERR, "CONFIG_MISSING\n");
              exit(2);
          }
      }
      if (!preg_match("/^[A-Za-z0-9_]+$/", (string) $config["table_prefix"])) {
          fwrite(STDERR, "TABLE_PREFIX_INVALID\n");
          exit(3);
      }
      $host = (string) $config["db_host"];
      $port = null;
      if (substr_count($host, ":") === 1) {
          [$host, $port] = explode(":", $host, 2);
      }
      $mysqli = $port
          ? new mysqli($host, $config["db_user"], $config["db_password"], $config["db_name"], (int) $port)
          : new mysqli($host, $config["db_user"], $config["db_password"], $config["db_name"]);
      if ($mysqli->connect_errno) {
          fwrite(STDERR, "DB_CONNECT_FAILED\n");
          exit(4);
      }
      $table = $config["table_prefix"] . "storefronts";
      $result = $mysqli->query("SELECT access_key FROM `" . $table . "` WHERE access_key <> \"\" ORDER BY storefront_id ASC LIMIT 1");
      if (!$result) {
          fwrite(STDERR, "STOREFRONT_QUERY_FAILED\n");
          exit(5);
      }
      $row = $result->fetch_assoc();
      $key = isset($row["access_key"]) ? trim((string) $row["access_key"]) : "";
      if ($key === "") {
          fwrite(STDERR, "STOREFRONT_KEY_MISSING\n");
          exit(6);
      }
      echo rawurlencode($key);
    ')"
    [ -n "$store_access_key" ] || fail "storefront access key is unavailable" 72

    tmpdir="$(mktemp -d "$HOME/.local/state/talario/partner-sync-smoke.XXXXXX")"
    chmod 700 "$tmpdir"
    trap 'rm -rf -- "$tmpdir"' EXIT

    request() {
      local method="$1"
      local auth_mode="$2"
      local suffix="$3"
      local outfile="$4"
      local cfg="$tmpdir/curl.$RANDOM.cfg"
      local url="https://talario.ru/dev_copy/index.php?dispatch=talario_analytics.catalog&store_access_key=$store_access_key&limit=500$suffix"

      {
        printf 'silent\n'
        printf 'show-error\n'
        printf 'connect-timeout = 15\n'
        printf 'max-time = 60\n'
        printf 'request = "%s"\n' "$method"
        printf 'url = "%s"\n' "$url"
        case "$auth_mode" in
          valid)
            printf 'header = "Authorization: Bearer %s"\n' "$token"
            ;;
          wrong)
            printf 'header = "Authorization: Bearer 0000000000000000000000000000000000000000"\n'
            ;;
          none)
            ;;
          *)
            fail "invalid runtime smoke auth mode" 76
            ;;
        esac
      } > "$cfg"
      chmod 600 "$cfg"

      curl --config "$cfg" --output "$outfile" --write-out '%{http_code}'
      rm -f -- "$cfg"
    }

    from_date="$(date +%F)"
    to_date="$(date -d '+30 days' +%F)"
    common_suffix="&from=$from_date&to=$to_date"

    missing_auth_status="$(request GET none "$common_suffix" "$tmpdir/missing.json")"
    [ "$missing_auth_status" = "401" ] || fail "missing-auth check failed" 73

    wrong_auth_status="$(request GET wrong "$common_suffix" "$tmpdir/wrong.json")"
    [ "$wrong_auth_status" = "401" ] || fail "wrong-auth check failed" 73

    post_status="$(request POST valid "$common_suffix" "$tmpdir/post.json")"
    [ "$post_status" = "405" ] || fail "write-method rejection check failed" 73

    valid_status="$(request GET valid "$common_suffix" "$tmpdir/catalog.json")"
    [ "$valid_status" = "200" ] || fail "authorized catalog request failed" 74

    php8.2 -r '
      $path = $argv[1];
      $data = json_decode((string) file_get_contents($path), true);
      if (!is_array($data) || ($data["schema_version"] ?? "") !== "partner-sync.catalog.v1") {
          fwrite(STDERR, "CATALOG_SCHEMA_INVALID\n");
          exit(2);
      }
      $partners = is_array($data["partners"] ?? null) ? $data["partners"] : [];
      $products = is_array($data["products"] ?? null) ? $data["products"] : [];
      $schedule = is_array($data["schedule"] ?? null) ? $data["schedule"] : [];
      $price_rows = 0;
      $products_with_price = 0;
      $variation_count = 0;
      $variations_with_price = 0;
      $public_url_unsafe = 0;
      foreach ($products as $product) {
          if ((float) ($product["price"] ?? 0) > 0) {
              $products_with_price++;
          }
          $rows = is_array($product["prices"] ?? null) ? $product["prices"] : [];
          $price_rows += count($rows);
          $vars = is_array($product["variations"] ?? null) ? $product["variations"] : [];
          $variation_count += count($vars);
          foreach ($vars as $variation) {
              if ((float) ($variation["price"] ?? 0) > 0) {
                  $variations_with_price++;
              }
          }
          $url = (string) ($product["public_url"] ?? "");
          if ($url !== "" && (strpos($url, "store_access_key") !== false || strpos($url, "Authorization") !== false)) {
              $public_url_unsafe++;
          }
      }
      if ($public_url_unsafe !== 0) {
          fwrite(STDERR, "PUBLIC_URL_LEAK_CHECK_FAILED\n");
          exit(3);
      }
      $truncated = is_array($data["truncated"] ?? null) ? $data["truncated"] : [];
      echo "RUNTIME_AUTH=OK\n";
      echo "RUNTIME_METHOD_GUARD=OK\n";
      echo "SCHEMA_VERSION=partner-sync.catalog.v1\n";
      echo "PARTNERS=" . count($partners) . "\n";
      echo "PRODUCTS=" . count($products) . "\n";
      echo "PRODUCTS_WITH_PRICE=" . $products_with_price . "\n";
      echo "PRICE_ROWS=" . $price_rows . "\n";
      echo "VARIATIONS=" . $variation_count . "\n";
      echo "VARIATIONS_WITH_PRICE=" . $variations_with_price . "\n";
      echo "SCHEDULE=" . count($schedule) . "\n";
      echo "PRODUCTS_TRUNCATED=" . (!empty($truncated["products"]) ? "YES" : "NO") . "\n";
      echo "SCHEDULE_TRUNCATED=" . (!empty($truncated["schedule"]) ? "YES" : "NO") . "\n";
      echo "HAS_MORE=" . (!empty($data["has_more"]) ? "YES" : "NO") . "\n";
      echo "NEXT_PRODUCT_ID=" . (isset($data["next_product_id"]) && $data["next_product_id"] !== null ? (int) $data["next_product_id"] : 0) . "\n";
      echo "NEXT_SCHEDULE_MARKER=" . (!empty($data["next_schedule_marker"]) ? "PRESENT" : "NONE") . "\n";
      echo "PUBLIC_URLS=SAFE\n";
    ' "$tmpdir/catalog.json"

    first_partner_id="$(php8.2 -r '
      $data = json_decode((string) file_get_contents($argv[1]), true);
      $partners = is_array($data["partners"] ?? null) ? $data["partners"] : [];
      echo isset($partners[0]["partner_id"]) ? (int) $partners[0]["partner_id"] : 0;
    ' "$tmpdir/catalog.json")"

    if [ "$first_partner_id" -gt 0 ]; then
      scoped_status="$(request GET valid "&partner_id=$first_partner_id$common_suffix" "$tmpdir/scoped.json")"
      [ "$scoped_status" = "200" ] || fail "partner-scoped catalog request failed" 75
      php8.2 -r '
        $expected = (int) $argv[2];
        $data = json_decode((string) file_get_contents($argv[1]), true);
        if (!is_array($data)) {
            exit(2);
        }
        foreach ((array) ($data["partners"] ?? []) as $partner) {
            if ((int) ($partner["partner_id"] ?? 0) !== $expected) {
                exit(3);
            }
        }
        foreach ((array) ($data["products"] ?? []) as $product) {
            if ((int) ($product["partner_id"] ?? 0) !== $expected) {
                exit(4);
            }
        }
        echo "PARTNER_SCOPE=OK\n";
      ' "$tmpdir/scoped.json" "$first_partner_id"
    else
      echo "PARTNER_SCOPE=NO_ACTIVE_PARTNERS"
    fi
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
