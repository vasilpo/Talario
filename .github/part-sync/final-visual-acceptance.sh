#!/usr/bin/env bash
set -euo pipefail

test "${GITHUB_EVENT_NAME:-}" = "push"
test "${GITHUB_REPOSITORY:-}" = "vasilpo/Talario"
test "${GITHUB_REF:-}" = "refs/heads/development"
test "${GITHUB_ACTOR:-}" = "vasilpo"
test -s ~/.ssh/id_ed25519
ssh-keygen -y -f ~/.ssh/id_ed25519 >/dev/null
echo "TRUST_GATE=PASS"
echo "SIGNER_READY=PASS"

tmp="${RUNNER_TEMP}/part-sync-final-visual"
rm -rf "$tmp"
mkdir -p "$tmp"
chmod 700 "$tmp"
trap 'rm -rf "$tmp"' EXIT

printf '%s' '{"product_id":1158}' > "$tmp/body.json"
request_id='part-sync-penaty-preview-20260924'
ts="$(date +%s)"
body_hash="$(sha256sum "$tmp/body.json" | awk '{print $1}')"
printf 'talario-part-sync-penaty\npreview\n%s\n%s\n%s\n' "$request_id" "$ts" "$body_hash" > "$tmp/message"
ssh-keygen -Y sign -q -f ~/.ssh/id_ed25519 -n talario-part-sync "$tmp/message"
sig_b64="$(base64 -w0 "$tmp/message.sig")"

code="$(curl --silent --show-error --request POST \
  'https://talario.ru/dev_copy/index.php?dispatch=talario_analytics.penaty_preview' \
  -H 'Content-Type: application/json' \
  -H "X-Talario-Request-Id: $request_id" \
  -H "X-Talario-Timestamp: $ts" \
  -H "X-Talario-Signature: $sig_b64" \
  --data-binary "@$tmp/body.json" \
  --output "$tmp/preview.json" \
  --write-out '%{http_code}')"
echo "PREVIEW_HTTP=$code"
test "$code" = "200"

python3 - "$tmp/preview.json" "$tmp/preview_url" <<'PY'
import json, sys
from urllib.parse import urlparse, parse_qs

src, out = sys.argv[1], sys.argv[2]
d = json.load(open(src, encoding="utf-8"))
if d.get("schema_version") != "partner-sync.preview.v4":
    raise SystemExit("preview schema mismatch")
if int(d.get("product_id") or 0) != 1158:
    raise SystemExit("preview product mismatch")
if int(d.get("company_id") or 0) != 39:
    raise SystemExit("preview company mismatch")
if d.get("status") != "H" or d.get("single_use") is not True:
    raise SystemExit("preview state mismatch")
url = str(d.get("preview_url") or "")
p = urlparse(url)
q = parse_qs(p.query)
if p.scheme != "https" or p.hostname != "talario.ru" or not p.path.startswith("/dev_copy/"):
    raise SystemExit("preview url scope mismatch")
if not q.get("skey") or len(q["skey"][0]) < 32:
    raise SystemExit("preview one-use key missing")
with open(out, "w", encoding="utf-8") as fh:
    fh.write(url)
print("PREVIEW_RESOLVE=PASS")
PY
chmod 600 "$tmp/preview_url"

command -v google-chrome >/dev/null || command -v chromium-browser >/dev/null || command -v chromium >/dev/null
python3 -m pip install --quiet selenium

python3 - "$tmp/preview_url" "$tmp/screenshot.png" "$tmp/visual-summary.txt" <<'PY'
import sys, time
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By

url_file, screenshot, summary_file = sys.argv[1:4]
url = open(url_file, encoding="utf-8").read().strip()

opts = Options()
opts.add_argument("--headless=new")
opts.add_argument("--no-sandbox")
opts.add_argument("--disable-dev-shm-usage")
opts.add_argument("--disable-gpu")
opts.add_argument("--window-size=1440,3000")

driver = webdriver.Chrome(options=opts)
try:
    driver.get(url)
    deadline = time.time() + 30
    while time.time() < deadline:
        if driver.execute_script("return document.readyState") == "complete":
            break
        time.sleep(0.25)
    time.sleep(3)

    current = driver.current_url
    body = driver.find_element(By.TAG_NAME, "body").text
    compact = body.replace(" ", "").replace("\u00a0", "")
    from urllib.parse import urlparse
    current_parts = urlparse(current)
    if current_parts.scheme != "https" or current_parts.hostname != "talario.ru" or not current_parts.path.startswith("/dev_copy/"):
        raise SystemExit("visual final url mismatch")
    if "LUNCH" in body or "closed for maintenance" in body.lower():
        raise SystemExit("maintenance stub still visible")
    if "Для доступа к этому ресурсу необходима авторизация" in body or "auth.login_form" in current:
        raise SystemExit("login page visible")
    if "Академия интеллекта" not in body and "Минисад" not in body:
        raise SystemExit("Penaty marker missing")
    if "2500" not in compact:
        raise SystemExit("price marker missing")

    img_count = len(driver.find_elements(By.TAG_NAME, "img"))
    iframe_count = len(driver.find_elements(By.TAG_NAME, "iframe"))
    select_count = len(driver.find_elements(By.TAG_NAME, "select"))
    button_count = len(driver.find_elements(By.TAG_NAME, "button"))

    height = int(driver.execute_script(
        "return Math.min(Math.max(document.body.scrollHeight, document.documentElement.scrollHeight, 3000), 12000)"
    ))
    driver.set_window_size(1440, height)
    time.sleep(1)
    if not driver.save_screenshot(screenshot):
        raise SystemExit("screenshot failed")

    with open(summary_file, "w", encoding="utf-8") as fh:
        fh.write("VISUAL_PAGE=PASS\n")
        fh.write("PRODUCT_ID=1158\n")
        fh.write("MAINTENANCE_STUB=ABSENT\n")
        fh.write("LOGIN_PAGE=ABSENT\n")
        fh.write("PENATY_MARKER=PASS\n")
        fh.write("PRICE_MARKER=PASS\n")
        fh.write(f"IMG_COUNT={img_count}\n")
        fh.write(f"IFRAME_COUNT={iframe_count}\n")
        fh.write(f"SELECT_COUNT={select_count}\n")
        fh.write(f"BUTTON_COUNT={button_count}\n")
        fh.write(f"SCREENSHOT_HEIGHT={height}\n")

    print("VISUAL_PAGE=PASS")
    print("PENATY_MARKER=PASS")
    print("PRICE_MARKER=PASS")
    print("SCREENSHOT=PASS")
finally:
    driver.quit()
PY
