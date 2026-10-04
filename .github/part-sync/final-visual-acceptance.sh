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
request_id="part-sync-preview-final-address-1238-${GITHUB_RUN_ID}"
ts="$(date +%s)"
body_hash="$(sha256sum "$tmp/body.json" | awk '{print $1}')"
printf 'talario-part-sync-penaty\npreview\n%s\n%s\n%s\n' "$request_id" "$ts" "$body_hash" > "$tmp/message"
ssh-keygen -Y sign -q -f ~/.ssh/id_ed25519 -n talario-part-sync "$tmp/message"
sig_b64="$(base64 -w0 "$tmp/message.sig")"

code="$(curl --silent --show-error --connect-timeout 10 --max-time 20 --request POST \
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

browser="$(command -v google-chrome || command -v chromium-browser || command -v chromium)"
test -n "$browser"
url="$(cat "$tmp/preview_url")"
timeout 45s "$browser" \
  --headless=new \
  --no-sandbox \
  --disable-dev-shm-usage \
  --disable-gpu \
  --window-size=1440,3000 \
  --screenshot="$evidence/screenshot.png" \
  --dump-dom \
  "$url" > "$tmp/page.html"

test -s "$tmp/page.html"
test -s "$evidence/screenshot.png"
chmod 600 "$evidence/screenshot.png"

python3 - "$tmp/page.html" "$evidence/visual-summary.txt" <<'PY'
import sys
from html.parser import HTMLParser

html_file, summary_file = sys.argv[1:3]
html = open(html_file, encoding='utf-8', errors='replace').read()

class Probe(HTMLParser):
    def __init__(self):
        super().__init__(convert_charrefs=True)
        self.map_address = 0
        self.map_canvas = 0
        self.content_description = 0
    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        classes = set((attrs.get('class') or '').split())
        if 'talario-lesson-map__address' in classes:
            self.map_address += 1
        if 'talario-lesson-map__canvas' in classes:
            self.map_canvas += 1
        if attrs.get('id') == 'content_description':
            self.content_description += 1

probe = Probe()
probe.feed(html)
product_marker = ('Каратэ' in html) or ('Первый удар' in html)

with open(summary_file, 'w', encoding='utf-8') as fh:
    fh.write('PRODUCT_ID=1238\n')
    fh.write(f'PRODUCT_MARKER={"PASS" if product_marker else "FAIL"}\n')
    fh.write(f'CONTENT_DESCRIPTION_COUNT={probe.content_description}\n')
    fh.write(f'MAP_ADDRESS_COUNT={probe.map_address}\n')
    fh.write(f'MAP_CANVAS_COUNT={probe.map_canvas}\n')

if not product_marker:
    raise SystemExit('product marker missing')
if probe.content_description != 1:
    raise SystemExit('description block changed or missing')
if probe.map_address != 0:
    raise SystemExit('duplicate map address still rendered')
if probe.map_canvas != 1:
    raise SystemExit('map canvas changed or missing')

print('PRODUCT_1238=PASS')
print('CONTENT_DESCRIPTION=PASS')
print('MAP_ADDRESS_REMOVED=PASS')
print('MAP_CANVAS_PRESERVED=PASS')
PY
chmod 600 "$evidence/visual-summary.txt"
echo 'FINAL_VISUAL_ACCEPTANCE=PASS'
