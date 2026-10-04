#!/usr/bin/env bash
set -euo pipefail

REQUEST_FILE="${1:?request file required}"
ATTEMPT="${2:?run attempt required}"
KEY_FILE="${PARTNER_SYNC_KEY_FILE:-$HOME/.ssh/id_ed25519}"
ENDPOINT='https://talario.ru/index.php?dispatch=talario_partner_sync.apply'

[ "${GITHUB_REPOSITORY:-}" = 'vasilpo/Talario' ]
[ "${GITHUB_REF:-}" = 'refs/heads/prod' ]
[[ "$REQUEST_FILE" =~ ^\.github/part-sync/prod-requests/[A-Za-z0-9._-]+\.json$ ]]
[ -f "$REQUEST_FILE" ]
[[ "$ATTEMPT" =~ ^[0-9]+$ ]]
[ "$ATTEMPT" -ge 1 ]
[ "$ATTEMPT" -le 2 ]
[ -s "$KEY_FILE" ]
ssh-keygen -y -f "$KEY_FILE" >/dev/null

for tool in jq curl sha256sum file base64 ssh-keygen awk tr wc mktemp date python3; do
  command -v "$tool" >/dev/null 2>&1
done

work="$RUNNER_TEMP/partner-sync-prod-create"
evidence="$RUNNER_TEMP/partner-sync-prod-create-evidence"
rm -rf "$work" "$evidence"
install -d -m 700 "$work" "$evidence"
trap 'rm -rf "$work"' EXIT

python3 - "$REQUEST_FILE" <<'PY'
import datetime,json,re,sys
path=sys.argv[1]
with open(path,encoding='utf-8') as fh:
    p=json.load(fh)
if p.get('target')!='prod':
    raise SystemExit('target must be prod')
if p.get('operation')!='create':
    raise SystemExit('PROD Partner Sync is CREATE-only')
if 'product_id' in p:
    raise SystemExit('product_id forbidden for PROD CREATE')
if p.get('dry_run') is not False:
    raise SystemExit('approved request must carry dry_run=false')
if not re.fullmatch(r'part-sync-prod-[A-Za-z0-9._:-]{6,104}',str(p.get('approval_id') or '')):
    raise SystemExit('approval_id missing or invalid')
product=p.get('product')
if not isinstance(product,dict):
    raise SystemExit('product required')
if int(product.get('company_id') or 0)<=0:
    raise SystemExit('product company_id required')
if int(p.get('approved_company_id') or 0)!=int(product.get('company_id') or 0):
    raise SystemExit('approved_company_id mismatch')
if product.get('status')!='H':
    raise SystemExit('PROD CREATE must be Hidden')
if not str(product.get('name') or '').strip():
    raise SystemExit('product name required')
if not isinstance(product.get('category_ids'),list) or not product.get('category_ids'):
    raise SystemExit('category_ids required')
transfers=p.get('image_transfer_files',[])
if not isinstance(transfers,list) or len(transfers)>12:
    raise SystemExit('image_transfer_files invalid')
if transfers:
    expires=str(p.get('image_transfer_expires_at') or '')
    if not re.fullmatch(r'\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z',expires):
        raise SystemExit('image transfer expiry required')
    expiry=datetime.datetime.strptime(expires,'%Y-%m-%dT%H:%M:%SZ').replace(tzinfo=datetime.timezone.utc)
    now=datetime.datetime.now(datetime.timezone.utc)
    if expiry<=now or (expiry-now).total_seconds()>3600:
        raise SystemExit('image transfer URL must expire within one hour')
for item in transfers:
    if not isinstance(item,dict):
        raise SystemExit('image transfer item invalid')
    url=str(item.get('url') or '')
    if not url.startswith('https://') or len(url)>2048:
        raise SystemExit('image transfer URL invalid')
    if not re.fullmatch(r'[0-9a-fA-F]{64}',str(item.get('sha256') or '')):
        raise SystemExit('image transfer sha invalid')
    size=int(item.get('bytes') or 0)
    if size<=0 or size>5242880:
        raise SystemExit('image transfer size invalid')
print('PROD_REQUEST_SCHEMA=PASS')
print('PROD_CREATE_ONLY=PASS')
print('PROD_HIDDEN_ONLY=PASS')
print('PROD_IMAGE_COUNT='+str(len(transfers)))
PY

cp "$REQUEST_FILE" "$work/request.json"
jq 'del(.target,.image_transfer_files,.image_transfer_expires_at,.source)' "$work/request.json" > "$work/base.json"

echo '[]' > "$work/images.json"
install -d -m 700 "$work/images"
index=0
while IFS= read -r entry; do
  url="$(jq -r '.url' <<<"$entry")"
  expected_sha="$(jq -r '.sha256' <<<"$entry" | tr '[:upper:]' '[:lower:]')"
  expected_bytes="$(jq -r '.bytes' <<<"$entry")"
  image_file="$work/images/image-$index"
  code="$(curl --location --silent --show-error --fail-with-body --proto '=https' --tlsv1.2 \
    --connect-timeout 10 --max-time 45 --max-filesize 5242880 \
    --output "$image_file" --write-out '%{http_code}' "$url" || true)"
  [ "$code" = '200' ] || { echo "IMAGE_TRANSFER_HTTP_FAILED index=$index http=$code" >&2; exit 12; }
  bytes="$(wc -c < "$image_file" | tr -d '[:space:]')"
  [ "$bytes" -eq "$expected_bytes" ] || { echo "IMAGE_TRANSFER_SIZE_MISMATCH index=$index" >&2; exit 12; }
  actual_sha="$(sha256sum "$image_file" | awk '{print $1}')"
  [ "$actual_sha" = "$expected_sha" ] || { echo "IMAGE_TRANSFER_SHA_MISMATCH index=$index" >&2; exit 12; }
  mime="$(file --brief --mime-type "$image_file")"
  case "$mime" in image/jpeg|image/png|image/webp) ;; *) echo "IMAGE_TRANSFER_MIME_INVALID index=$index mime=$mime" >&2; exit 12;; esac
  alt="$(jq -r '.product.name' "$work/request.json")"
  b64="$(base64 < "$image_file" | tr -d '\n')"
  jq --arg b64 "$b64" --arg alt "$alt" '. + [{content_base64:$b64,alt:$alt}]' \
    "$work/images.json" > "$work/images.next"
  mv "$work/images.next" "$work/images.json"
  index=$((index+1))
done < <(jq -c '.image_transfer_files[]?' "$work/request.json")
echo "PROD_IMAGES_VERIFIED=$index"

jq --slurpfile imgs "$work/images.json" '. + {images:$imgs[0]} | .operation="create" | .product.status="H" | .dry_run=false' \
  "$work/base.json" > "$work/apply.json"
jq '.dry_run=true | del(.approval_id)' "$work/apply.json" > "$work/dry.json"

post_signed() {
  body="$1"
  request_id="$2"
  output="$3"
  ts="$(date +%s)"
  hash="$(sha256sum "$body" | awk '{print $1}')"
  message="$work/message-$request_id"
  signature="$message.sig"
  printf 'talario-part-sync-prod\napply\n%s\n%s\n%s\n' "$request_id" "$ts" "$hash" > "$message"
  rm -f "$signature"
  ssh-keygen -Y sign -q -f "$KEY_FILE" -n talario-part-sync-prod "$message"
  sig="$(base64 < "$signature" | tr -d '\n')"
  curl --silent --show-error --output "$output" --write-out '%{http_code}' \
    --connect-timeout 10 --max-time 90 --proto '=https' --tlsv1.2 \
    -X POST "$ENDPOINT" \
    -H 'Content-Type: application/json' \
    -H "X-Talario-Request-Id: $request_id" \
    -H "X-Talario-Timestamp: $ts" \
    -H "X-Talario-Signature: $sig" \
    --data-binary "@$body"
}

dry_id="part-sync-prod-apply-${GITHUB_RUN_ID}-${ATTEMPT}-dry"
dry_code="$(post_signed "$work/dry.json" "$dry_id" "$work/dry.out")"
echo "PROD_SIGNED_DRY_RUN_HTTP=$dry_code"
[ "$dry_code" = '200' ] || { jq -r '.error // "unknown"' "$work/dry.out" >&2 || true; exit 20; }
jq -e '.dry_run == true and .schema_version == "partner-sync.write-plan.v1" and .plan.operation == "create" and .plan.product.status == "H"' "$work/dry.out" >/dev/null
expected_company="$(jq -r '.approved_company_id' "$work/apply.json")"
actual_company="$(jq -r '.plan.product.company_id' "$work/dry.out")"
[ "$expected_company" = "$actual_company" ]
echo 'PROD_DRY_RUN_PLAN=PASS'

if [ "$ATTEMPT" = '1' ]; then
  printf 'STATE=DRY_RUN_PASSED\nNEXT=RERUN_SAME_JOB_ONCE\n' > "$evidence/summary.txt"
  echo 'PROD_ATTEMPT_1_DRY_RUN_ONLY=PASS'
  exit 0
fi

apply_id="part-sync-prod-apply-${GITHUB_RUN_ID}-${ATTEMPT}-create"
apply_code="$(post_signed "$work/apply.json" "$apply_id" "$work/apply.out")"
echo "PROD_SIGNED_CREATE_HTTP=$apply_code"
[ "$apply_code" = '201' ] || { jq -r '.error // "unknown"' "$work/apply.out" >&2 || true; exit 21; }

python3 - "$work/apply.json" "$work/apply.out" <<'PY'
import json,sys
req=json.load(open(sys.argv[1],encoding='utf-8'))
out=json.load(open(sys.argv[2],encoding='utf-8'))
rb=out.get('readback') or {}
product=req['product']
if out.get('operation')!='create' or out.get('dry_run') is not False:
    raise SystemExit('create response contract mismatch')
if int(rb.get('product_id') or 0)<=0:
    raise SystemExit('product_id missing')
if int(rb.get('company_id') or 0)!=int(req['approved_company_id']):
    raise SystemExit('company readback mismatch')
if str(rb.get('name') or '')!=str(product.get('name') or ''):
    raise SystemExit('name readback mismatch')
if rb.get('status')!='H':
    raise SystemExit('status readback mismatch')
expected_images=len(req.get('images') or [])
images=rb.get('images') or {}
actual_images=int(images.get('main') or 0)+int(images.get('additional') or 0)
if actual_images!=expected_images:
    raise SystemExit('image readback mismatch')
if expected_images and int(images.get('main') or 0)!=1:
    raise SystemExit('main image missing')
vp=req.get('variation_plan')
if vp is not None:
    variations=out.get('variations') or {}
    if int(variations.get('count') or 0)!=len(vp):
        raise SystemExit('variation readback mismatch')
print('PROD_CREATE_READBACK=PASS')
print('PROD_PRODUCT_ID='+str(rb['product_id']))
PY

cp "$work/apply.out" "$evidence/create-result.json"
printf 'STATE=CREATE_VERIFIED\n' > "$evidence/summary.txt"
echo 'PROD_CREATE_COMPLETE=PASS'
