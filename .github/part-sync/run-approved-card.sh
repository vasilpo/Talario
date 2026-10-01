#!/usr/bin/env bash
set -euo pipefail

REQUEST_FILE="${1:?request file required}"
ATTEMPT="${2:?run attempt required}"
KEY_FILE="${PARTNER_SYNC_KEY_FILE:-$HOME/.ssh/id_ed25519}"
ENDPOINT='https://talario.ru/dev_copy/index.php?dispatch=talario_analytics.partner_apply'
PREVIEW_ENDPOINT='https://talario.ru/dev_copy/index.php?dispatch=talario_analytics.penaty_preview'

test "${GITHUB_REPOSITORY:-}" = "vasilpo/Talario"
test "${GITHUB_REF:-}" = "refs/heads/development"
[[ "$REQUEST_FILE" =~ ^\.github/part-sync/requests/[A-Za-z0-9._-]+\.json$ ]]
test -f "$REQUEST_FILE"
test "$ATTEMPT" = "1" -o "$ATTEMPT" = "2"
test -s "$KEY_FILE"
ssh-keygen -y -f "$KEY_FILE" >/dev/null

for tool in jq curl sha256sum file base64 ssh-keygen awk tr wc mktemp date python3; do
  command -v "$tool" >/dev/null 2>&1
done

work="$RUNNER_TEMP/partner-sync-approved-card"
evidence="$RUNNER_TEMP/partner-sync-approved-card-evidence"
rm -rf "$work" "$evidence"
install -d -m 700 "$work" "$evidence"
trap 'rm -rf "$work"' EXIT

python3 - "$REQUEST_FILE" <<'PY'
import json,re,sys
path=sys.argv[1]
with open(path,encoding="utf-8") as fh:
    p=json.load(fh)
if p.get("target")!="dev_copy":
    raise SystemExit("target must be dev_copy")
if p.get("operation")!="create":
    raise SystemExit("only create is allowed in approved-card pipeline")
if p.get("dry_run") is not False:
    raise SystemExit("approved request must carry dry_run=false")
if not re.fullmatch(r"part-sync-[A-Za-z0-9._:-]{6,112}",str(p.get("approval_id") or "")):
    raise SystemExit("approval_id missing or invalid")
if not re.fullmatch(r"part-sync-[A-Za-z0-9._:-]{6,112}",str(p.get("request_id") or "")):
    raise SystemExit("request_id missing or invalid")
product=p.get("product")
if not isinstance(product,dict):
    raise SystemExit("product object required")
if not str(product.get("name") or "").strip():
    raise SystemExit("product name required")
if product.get("status")!="H":
    raise SystemExit("approved-card pipeline creates Hidden products only")
if float(product.get("price") or 0)<0:
    raise SystemExit("price must be non-negative")
if not (int(p.get("approved_company_id") or 0)>0 or str(p.get("approved_company_name") or "").strip()):
    raise SystemExit("approved company context required")
if not (product.get("category_ids") or str(p.get("category_name") or "").strip()):
    raise SystemExit("category context required")
images=p.get("image_drive_files",[])
if not isinstance(images,list) or len(images)>12:
    raise SystemExit("image manifest invalid")
total=0
for item in images:
    if not isinstance(item,dict):
        raise SystemExit("image manifest item invalid")
    if not re.fullmatch(r"[A-Za-z0-9_-]+",str(item.get("id") or "")):
        raise SystemExit("image id invalid")
    if not re.fullmatch(r"[0-9a-fA-F]{64}",str(item.get("sha256") or "")):
        raise SystemExit("image sha invalid")
    size=int(item.get("bytes") or 0)
    if size<=0 or size>5242880:
        raise SystemExit("image size invalid")
    total+=size
if total>14000000:
    raise SystemExit("image manifest too large for signed payload")
vp=p.get("variation_plan")
if vp is not None:
    if not isinstance(vp,list) or not vp:
        raise SystemExit("variation_plan invalid")
    if not isinstance(p.get("booking"),dict):
        raise SystemExit("booking required with variations")
print("REQUEST_SCHEMA=PASS")
print("REQUEST_STATUS=H")
print("REQUEST_IMAGES="+str(len(images)))
print("REQUEST_VARIATIONS="+str(len(vp or [])))
PY

cp "$REQUEST_FILE" "$work/request.json"
jq 'del(
  .target,
  .request_id,
  .source_type,
  .source_sheet_id,
  .source_sheet_name,
  .source_sheet_row,
  .source_image_folder,
  .source_image_count,
  .ignore_row,
  .image_drive_files
)' "$work/request.json" > "$work/base.json"

echo '[]' > "$work/images.json"
image_tmpdir="$work/images"
install -d -m 700 "$image_tmpdir"
index=0
while IFS= read -r entry; do
  id="$(jq -r '.id' <<<"$entry")"
  expected_sha="$(jq -r '.sha256' <<<"$entry" | tr '[:upper:]' '[:lower:]')"
  expected_bytes="$(jq -r '.bytes' <<<"$entry")"
  image_file="$image_tmpdir/image-$index"
  curl --fail --location --silent --show-error --tlsv1.2 --max-time 60 --max-filesize 5242880 \
    "https://drive.usercontent.google.com/download?id=$id&export=download&confirm=t" \
    --output "$image_file"
  bytes="$(wc -c < "$image_file" | tr -d '[:space:]')"
  test "$bytes" -eq "$expected_bytes"
  actual_sha="$(sha256sum "$image_file" | awk '{print $1}')"
  test "$actual_sha" = "$expected_sha"
  mime="$(file --brief --mime-type "$image_file")"
  case "$mime" in image/jpeg|image/png|image/webp) ;; *) echo "unsupported image mime: $mime" >&2; exit 12;; esac
  alt="$(jq -r '.product.name' "$work/request.json")"
  b64="$(base64 < "$image_file" | tr -d '\n')"
  jq --arg b64 "$b64" --arg alt "$alt" '. + [{content_base64:$b64,alt:$alt}]' \
    "$work/images.json" > "$work/images.next"
  mv "$work/images.next" "$work/images.json"
  index=$((index+1))
done < <(jq -c '.image_drive_files[]?' "$work/request.json")
echo "SOURCE_IMAGES_VERIFIED=$index"

jq --slurpfile imgs "$work/images.json" '. + {images:$imgs[0]} | .dry_run=false' \
  "$work/base.json" > "$work/apply.json"
jq '.dry_run=true | del(.approval_id)' "$work/apply.json" > "$work/dry.json"

post_signed() {
  scope="$1"
  purpose="$2"
  body="$3"
  request_id="$4"
  output="$5"
  ts="$(date +%s)"
  hash="$(sha256sum "$body" | awk '{print $1}')"
  message="$work/message"
  signature="$work/message.sig"
  printf '%s\n%s\n%s\n%s\n%s\n' "$scope" "$purpose" "$request_id" "$ts" "$hash" > "$message"
  rm -f "$signature"
  ssh-keygen -Y sign -q -f "$KEY_FILE" -n talario-part-sync "$message"
  sig="$(base64 < "$signature" | tr -d '\n')"
  curl --silent --show-error --output "$output" --write-out '%{http_code}' --max-time 180 \
    -X POST "$([[ "$purpose" = "preview" ]] && printf '%s' "$PREVIEW_ENDPOINT" || printf '%s' "$ENDPOINT")" \
    -H 'Content-Type: application/json' \
    -H "X-Talario-Request-Id: $request_id" \
    -H "X-Talario-Timestamp: $ts" \
    -H "X-Talario-Signature: $sig" \
    --data-binary "@$body"
}

dry_req="part-sync-apply-${GITHUB_RUN_ID}-dry"
dry_code="$(post_signed 'talario-part-sync' 'apply' "$work/dry.json" "$dry_req" "$work/dry.out")"
echo "SIGNED_DRY_RUN_HTTP=$dry_code"
test "$dry_code" = "200"

python3 - "$work/request.json" "$work/dry.out" <<'PY'
import json,sys
request=json.load(open(sys.argv[1],encoding="utf-8"))
dry=json.load(open(sys.argv[2],encoding="utf-8"))
if dry.get("schema_version")!="partner-sync.write-plan.v1" or dry.get("dry_run") is not True:
    raise SystemExit("dry-run schema mismatch")
plan=dry.get("plan") or {}
product=plan.get("product") or {}
expected=request["product"]
if plan.get("operation")!="create":
    raise SystemExit("dry-run operation mismatch")
if int(product.get("company_id") or 0)<=0:
    raise SystemExit("dry-run company unresolved")
if product.get("product")!=expected.get("name"):
    raise SystemExit("dry-run name mismatch")
for key in ("status","address","short_description","full_description","meta_keywords"):
    if key in expected and product.get(key)!=expected.get(key):
        raise SystemExit("dry-run product field mismatch: "+key)
if float(product.get("price") or 0)!=float(expected.get("price") or 0):
    raise SystemExit("dry-run price mismatch")
manifest=request.get("image_drive_files") or []
images=plan.get("images")
if not isinstance(images,dict) or int(images.get("count") or 0)!=len(manifest):
    raise SystemExit("dry-run image count mismatch")
vp=request.get("variation_plan")
vr=plan.get("variations")
windows=plan.get("variation_booking_windows")
if vp:
    if not isinstance(vr,dict) or vr.get("resolved") is not True or int(vr.get("count") or 0)!=len(vp):
        raise SystemExit("dry-run variation resolution mismatch")
    if not isinstance(windows,list) or len(windows)!=len(vp):
        raise SystemExit("dry-run variation booking mismatch")
print("SIGNED_DRY_RUN=PASS")
print("RESOLVED_COMPANY_ID="+str(int(product["company_id"])))
print("RESOLVED_CATEGORY_IDS="+",".join(map(str,product.get("category_ids") or [])))
print("RESOLVED_VARIATIONS="+str(len(vp or [])))
PY

if [ "$ATTEMPT" = "1" ]; then
  echo "RESULT=DRY_RUN_PASS_REQUIRES_EXPLICIT_RERUN"
  exit 78
fi

apply_req="part-sync-apply-${GITHUB_RUN_ID}-create"
apply_code="$(post_signed 'talario-part-sync' 'apply' "$work/apply.json" "$apply_req" "$work/apply.out")"
echo "CREATE_HTTP=$apply_code"
if [ "$apply_code" != "201" ]; then
  jq -c '{error:(.error//null),stage:(.stage//null),kind:(.kind//null),detail:(.detail//null)}' "$work/apply.out" 2>/dev/null || true
  exit 61
fi

python3 - "$work/request.json" "$work/dry.out" "$work/apply.out" "$work/result.env" <<'PY'
import json,sys
request=json.load(open(sys.argv[1],encoding="utf-8"))
dry=json.load(open(sys.argv[2],encoding="utf-8"))
result=json.load(open(sys.argv[3],encoding="utf-8"))
plan=dry["plan"]
expected=request["product"]
rb=result.get("readback") or {}
if result.get("schema_version")!="partner-sync.write-result.v1" or result.get("dry_run") is not False or result.get("operation")!="create":
    raise SystemExit("create schema mismatch")
product_id=int(result.get("product_id") or 0)
if product_id<=0:
    raise SystemExit("missing product id")
if int(rb.get("company_id") or 0)!=int(plan["product"].get("company_id") or 0):
    raise SystemExit("company readback mismatch")
if rb.get("name")!=expected.get("name") or rb.get("status")!="H":
    raise SystemExit("name/status readback mismatch")
if float(rb.get("price") or 0)!=float(expected.get("price") or 0):
    raise SystemExit("price readback mismatch")
for key in ("address","full_description"):
    if key in expected and rb.get(key)!=expected.get(key):
        raise SystemExit("readback mismatch: "+key)
if rb.get("filter_features")!=(plan.get("filter_features") or {}):
    raise SystemExit("filter feature readback mismatch")
manifest=request.get("image_drive_files") or []
image_rb=rb.get("images") or {}
if int(image_rb.get("main") or 0)+int(image_rb.get("additional") or 0)!=len(manifest):
    raise SystemExit("image readback mismatch")
vp=request.get("variation_plan") or []
vr=result.get("variations")
if vp:
    if not isinstance(vr,dict) or int(vr.get("count") or 0)!=len(vp):
        raise SystemExit("variation readback count mismatch")
    items=vr.get("items") or []
    if len(items)!=len(vp):
        raise SystemExit("variation readback items mismatch")
    expected_keys={(str(x["age_group"]),str(x["purchase_option"])) for x in vp}
    actual_keys={(str(x.get("age_group","")),str(x.get("purchase_option",""))) for x in items}
    if actual_keys!=expected_keys:
        raise SystemExit("variation identity readback mismatch")
    expected_duration={int(x.get("duration") or (x.get("schedule") or [{}])[0].get("duration") or 0) for x in vp}
    actual_duration={int(x.get("duration") or 0) for x in items}
    if actual_duration!=expected_duration:
        raise SystemExit("variation duration readback mismatch")
print("CREATE_READBACK=PASS")
print("PRODUCT_ID="+str(product_id))
print("COMPANY_ID="+str(int(rb["company_id"])))
print("NAME="+str(rb["name"]))
with open(sys.argv[4],"w",encoding="utf-8") as fh:
    fh.write("PRODUCT_ID="+str(product_id)+"\n")
    fh.write("COMPANY_ID="+str(int(rb["company_id"]))+"\n")
PY

product_id="$(awk -F= '$1=="PRODUCT_ID"{print $2}' "$work/result.env")"
company_id="$(awk -F= '$1=="COMPANY_ID"{print $2}' "$work/result.env")"
test "$product_id" -gt 0
test "$company_id" -gt 0

jq -n --argjson product_id "$product_id" --argjson approved_company_id "$company_id" \
  '{product_id:$product_id,approved_company_id:$approved_company_id}' > "$work/preview.json"
preview_req="part-sync-preview-${GITHUB_RUN_ID}-${product_id}"
preview_code="$(post_signed 'talario-part-sync-penaty' 'preview' "$work/preview.json" "$preview_req" "$work/preview.out")"
echo "SIGNED_PREVIEW_HTTP=$preview_code"
test "$preview_code" = "200"

python3 - "$work/preview.out" "$product_id" "$company_id" "$work/preview_url" "$work/state_url" <<'PY'
import json,sys
from urllib.parse import urlparse,parse_qs
d=json.load(open(sys.argv[1],encoding="utf-8"))
pid=int(sys.argv[2]); cid=int(sys.argv[3])
if d.get("schema_version")!="partner-sync.preview.v4" or int(d.get("product_id") or 0)!=pid or int(d.get("company_id") or 0)!=cid:
    raise SystemExit("preview binding mismatch")
if d.get("status")!="H" or d.get("single_use") is not True:
    raise SystemExit("preview state mismatch")
vis=d.get("visibility") or {}
if vis.get("preview") is not True or vis.get("company_scope") is not True or str(vis.get("company_status"))!="A":
    raise SystemExit("preview visibility mismatch")
url=str(d.get("preview_url") or "")
state=str(d.get("state_url") or "")
u=urlparse(url); s=urlparse(state)
if u.scheme!="https" or u.hostname!="talario.ru" or not u.path.startswith("/dev_copy/"):
    raise SystemExit("preview url scope mismatch")
if not parse_qs(u.query).get("skey"):
    raise SystemExit("preview skey missing")
if s.scheme!="https" or s.hostname!="talario.ru" or not s.path.startswith("/dev_copy/"):
    raise SystemExit("state url scope mismatch")
open(sys.argv[4],"w",encoding="utf-8").write(url)
open(sys.argv[5],"w",encoding="utf-8").write(state)
print("PREVIEW_BINDING=PASS")
PY

command -v google-chrome >/dev/null || command -v chromium-browser >/dev/null || command -v chromium >/dev/null
python3 -m pip install --quiet selenium

python3 - "$work/preview_url" "$work/state_url" "$REQUEST_FILE" "$evidence/screenshot.png" "$evidence/summary.txt" "$product_id" "$company_id" <<'PY'
import json,os,sys,time
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By

url=open(sys.argv[1],encoding="utf-8").read().strip()
state_url=open(sys.argv[2],encoding="utf-8").read().strip()
request=json.load(open(sys.argv[3],encoding="utf-8"))
screenshot=sys.argv[4]; summary=sys.argv[5]
product_id=int(sys.argv[6]); company_id=int(sys.argv[7])
name=str(request["product"]["name"])
partner=str(request.get("approved_company_name") or "").strip()

opts=Options()
opts.add_argument("--headless=new")
opts.add_argument("--no-sandbox")
opts.add_argument("--disable-dev-shm-usage")
opts.add_argument("--disable-gpu")
opts.add_argument("--window-size=1440,3000")
driver=webdriver.Chrome(options=opts)
try:
    driver.get(url)
    deadline=time.time()+30
    while time.time()<deadline:
        if driver.execute_script("return document.readyState")=="complete":
            break
        time.sleep(.25)
    time.sleep(3)
    body=driver.find_element(By.TAG_NAME,"body").text
    lower=body.lower()
    if name not in body:
        raise SystemExit("product name missing on storefront")
    if partner and partner not in body:
        raise SystemExit("partner name missing on storefront")
    if "магазин закрыт на обслуживание" in lower or "closed for maintenance" in lower:
        raise SystemExit("maintenance page visible")
    if "страница не найдена" in lower or "товар не найден" in lower or "product not found" in lower:
        raise SystemExit("product page not found")
    if "для доступа к этому ресурсу необходима авторизация" in lower:
        raise SystemExit("login page visible")
    height=int(driver.execute_script("return Math.min(Math.max(document.body.scrollHeight,document.documentElement.scrollHeight,3000),12000)"))
    driver.set_window_size(1440,height)
    time.sleep(1)
    if not driver.save_screenshot(screenshot):
        raise SystemExit("screenshot failed")
    os.chmod(screenshot,0o600)

    raw=driver.execute_async_script("""
const u=arguments[0], done=arguments[arguments.length-1];
fetch(u,{method:"POST",credentials:"same-origin",cache:"no-store",redirect:"error",referrerPolicy:"same-origin",headers:{"Accept":"application/json"}})
.then(async r=>done(JSON.stringify({status:r.status,body:await r.text()})))
.catch(()=>done(JSON.stringify({status:0,body:""})));
""",state_url)
    state_result=json.loads(raw)
    if int(state_result.get("status") or 0)!=200:
        raise SystemExit("preview state http mismatch")
    state=json.loads(state_result.get("body") or "{}")
    if state.get("session_handoff_token_valid") is not True:
        raise SystemExit("preview state token invalid")
    if state.get("preview_marker_exact") is not True:
        raise SystemExit("preview marker mismatch")
    if state.get("store_access_key_present") is not True or state.get("store_access_key_matches_runtime") is not True:
        raise SystemExit("store access key mismatch")

    with open(summary,"w",encoding="utf-8") as fh:
        fh.write(f"PRODUCT_ID={product_id}\n")
        fh.write(f"COMPANY_ID={company_id}\n")
        fh.write(f"NAME={name}\n")
        fh.write("STORE_FRONT=PASS\n")
        fh.write("SESSION_HANDOFF=PASS\n")
        fh.write("SCREENSHOT=PASS\n")
        fh.write(f"BODY_LEN={len(body)}\n")
        fh.write(f"IMG_COUNT={len(driver.find_elements(By.TAG_NAME,'img'))}\n")
        fh.write(f"BUTTON_COUNT={len(driver.find_elements(By.TAG_NAME,'button'))}\n")
        fh.write(f"SCREENSHOT_HEIGHT={height}\n")
    os.chmod(summary,0o600)
    print("STOREFRONT_CARD=PASS")
    print("SESSION_HANDOFF=PASS")
    print("SCREENSHOT=PASS")
finally:
    driver.quit()
PY

echo "PARTNER_SYNC_APPROVED_CARD=PASS"
echo "PRODUCT_ID=$product_id"
echo "COMPANY_ID=$company_id"
