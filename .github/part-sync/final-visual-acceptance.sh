#!/usr/bin/env bash
set -euo pipefail

test "${GITHUB_EVENT_NAME:-}" = "push"
test "${GITHUB_REPOSITORY:-}" = "vasilpo/Talario"
test "${GITHUB_REF:-}" = "refs/heads/development"
test "${GITHUB_ACTOR:-}" = "vasilpo"
test -s ~/.ssh/id_ed25519
ssh-keygen -y -f ~/.ssh/id_ed25519 >/dev/null

tmp="${RUNNER_TEMP}/part-sync-final-visual"
evidence="${RUNNER_TEMP}/part-sync-final-visual-evidence"
rm -rf "$tmp" "$evidence"
mkdir -p "$tmp" "$evidence"
chmod 700 "$tmp" "$evidence"
trap 'rm -rf "$tmp"' EXIT

printf '%s' '{"approved_company_id":12,"product_id":1238}' > "$tmp/body.json"
request_id="part-sync-preview-address-1238-${GITHUB_RUN_ID}"
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
src, out = sys.argv[1:3]
d = json.load(open(src, encoding='utf-8'))
if d.get('schema_version') != 'partner-sync.preview.v4':
    raise SystemExit('preview schema mismatch')
if int(d.get('product_id') or 0) != 1238 or int(d.get('company_id') or 0) != 12:
    raise SystemExit('preview target mismatch')
if d.get('status') != 'H' or d.get('single_use') is not True:
    raise SystemExit('preview state mismatch')
url = str(d.get('preview_url') or '')
p = urlparse(url)
q = parse_qs(p.query)
if p.scheme != 'https' or p.hostname != 'talario.ru' or not p.path.startswith('/dev_copy/') or not q.get('skey'):
    raise SystemExit('preview url scope mismatch')
open(out, 'w', encoding='utf-8').write(url)
print('PREVIEW_1238=PASS')
PY
chmod 600 "$tmp/preview_url"

command -v google-chrome >/dev/null || command -v chromium-browser >/dev/null || command -v chromium >/dev/null
python3 -m pip install --quiet selenium

python3 - "$tmp/preview_url" "$evidence/screenshot.png" "$evidence/visual-summary.txt" <<'PY'
import json, os, sys, time
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By

url_file, screenshot, summary_file = sys.argv[1:4]
url = open(url_file, encoding='utf-8').read().strip()
opts = Options()
opts.add_argument('--headless=new')
opts.add_argument('--no-sandbox')
opts.add_argument('--disable-dev-shm-usage')
opts.add_argument('--disable-gpu')
opts.add_argument('--window-size=1440,3000')
driver = webdriver.Chrome(options=opts)
try:
    driver.get(url)
    deadline = time.time() + 30
    while time.time() < deadline:
        if driver.execute_script('return document.readyState') == 'complete':
            break
        time.sleep(0.25)
    time.sleep(3)
    body = driver.find_element(By.TAG_NAME, 'body').text
    if 'Каратэ' not in body and 'Первый удар' not in body:
        raise SystemExit('product marker missing')
    matches = driver.execute_script(r'''
const norm = (s) => (s || '').toLowerCase().replace(/[\s.,«»"'()\-–—:;]/g, '');
const hit = (s) => {
  const n = norm(s);
  return n.includes('красногорск') && n.includes('ленина') && n.includes('1стр1');
};
return Array.from(document.querySelectorAll('body *')).filter((el) => {
  if (!hit(el.innerText)) return false;
  return !Array.from(el.children).some((child) => hit(child.innerText));
}).map((el) => {
  const chain = [];
  let cur = el;
  for (let i = 0; cur && i < 8; i++, cur = cur.parentElement) {
    chain.push({tag: cur.tagName, id: cur.id || '', cls: String(cur.className || '')});
  }
  const rect = el.getBoundingClientRect();
  return {
    tag: el.tagName,
    id: el.id || '',
    cls: String(el.className || ''),
    text: (el.innerText || '').trim().replace(/\s+/g, ' '),
    y: Math.round(rect.top + window.scrollY),
    chain
  };
});
''')
    height = int(driver.execute_script("return Math.min(Math.max(document.body.scrollHeight, document.documentElement.scrollHeight, 3000), 12000)"))
    driver.set_window_size(1440, height)
    time.sleep(1)
    if not driver.save_screenshot(screenshot):
        raise SystemExit('screenshot failed')
    os.chmod(screenshot, 0o600)
    with open(summary_file, 'w', encoding='utf-8') as fh:
        fh.write('PRODUCT_ID=1238\n')
        fh.write(f'ADDRESS_MATCH_COUNT={len(matches)}\n')
        for i, item in enumerate(matches, 1):
            fh.write(f'MATCH_{i}_TAG={item["tag"]}\n')
            fh.write(f'MATCH_{i}_ID={item["id"]}\n')
            fh.write(f'MATCH_{i}_CLASS={item["cls"]}\n')
            fh.write(f'MATCH_{i}_TEXT={item["text"]}\n')
            fh.write(f'MATCH_{i}_Y={item["y"]}\n')
            fh.write(f'MATCH_{i}_CHAIN={json.dumps(item["chain"], ensure_ascii=False)}\n')
    os.chmod(summary_file, 0o600)
    print(f'ADDRESS_MATCH_COUNT={len(matches)}')
    for i, item in enumerate(matches, 1):
        print(f'MATCH_{i}_TAG={item["tag"]}')
        print(f'MATCH_{i}_ID={item["id"]}')
        print(f'MATCH_{i}_CLASS={item["cls"]}')
        print(f'MATCH_{i}_TEXT={item["text"]}')
        print(f'MATCH_{i}_Y={item["y"]}')
        print('MATCH_%d_CHAIN=%s' % (i, json.dumps(item['chain'], ensure_ascii=False)))
    if len(matches) < 1:
        raise SystemExit('address marker unexpectedly absent')
    print('ADDRESS_DIAGNOSTIC=PASS')
finally:
    driver.quit()
PY
