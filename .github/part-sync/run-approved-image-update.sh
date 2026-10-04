#!/usr/bin/env bash
set -euo pipefail

REQUEST_FILE="${1:?request file required}"
ATTEMPT="${2:?run attempt required}"
KEY_FILE="${PARTNER_SYNC_KEY_FILE:-$HOME/.ssh/id_ed25519}"
ENDPOINT='https://talario.ru/dev_copy/index.php?dispatch=talario_analytics.partner_apply'

# Intentionally bounded to the current dev_copy image-only repair.
test "${GITHUB_REPOSITORY:-}" = "vasilpo/Talario"
test "${GITHUB_REF:-}" = "refs/heads/development"
test "$REQUEST_FILE" = '.github/part-sync/requests/karate-images-transfer-20261004.json'
test "$ATTEMPT" = '1' -o "$ATTEMPT" = '2'
test -s "$KEY_FILE"

for tool in jq curl sha256sum file base64 ssh-keygen awk tr wc mktemp date python3; do
  command -v "$tool" >/dev/null 2>&1
done

work="$RUNNER_TEMP/partner-sync-approved-image-update"
rm -rf "$work"
install -d -m 700 "$work/images"
trap 'rm -rf "$work"' EXIT

python3 - "$REQUEST_FILE" <<'PY'
import json,sys,urllib.parse
p=json.load(open(sys.argv[1],encoding='utf-8'))
expected_keys={'request_id','target','operation','dry_run','approved_company_id','product_id','product','image_update_transport','image_drive_files','approval_id'}
if set(p)!=expected_keys:
    raise SystemExit('unexpected request keys')
if p.get('request_id')!='part-sync-karate-images-transfer-20261004-v1':
    raise SystemExit('wrong request id')
if p.get('target')!='dev_copy' or p.get('operation')!='update' or p.get('dry_run') is not False:
    raise SystemExit('wrong target or operation')
if p.get('image_update_transport')!='ephemeral_oai':
    raise SystemExit('wrong transport')
if int(p.get('product_id') or 0)!=1238 or int(p.get('approved_company_id') or 0)!=12:
    raise SystemExit('wrong bounded target')
if p.get('approval_id')!='part-sync-karate-images-20261004':
    raise SystemExit('wrong approval id')
if (p.get('product') or {})!={'name':'Каратэ «Первый удар»'}:
    raise SystemExit('unexpected product contract')
items=p.get('image_drive_files')
if not isinstance(items,list) or len(items)!=3:
    raise SystemExit('exactly three images required')
expected=[
 ('1iEQ2m7gwhZtxArF9JQ6GLlV1B4EKCFW1',4584,'c3ea05ff3a8320387b57cee8101a354d6bb7dc4989df17a08279eaa6bc9e2f3a'),
 ('1uECR1SRExy-1J8cg1Px4RpQk3-DVoUj_',11622,'164570c7fa062958e4e9aafb88a48011402c1787bc9090a90e3e9431ccf13547'),
 ('1EAFzIXMwIZRG3dj4dt6I-KXiIpGbTH9j',8914,'e6d72c6b35d4d0759934ab2f871b09dc6b2a2a22954838431f7eb590ee68ce87'),
]
for i,(item,want) in enumerate(zip(items,expected)):
    if set(item)!={'id','bytes','sha256','transfer_url'}:
        raise SystemExit(f'unexpected image keys {i}')
    if (str(item.get('id') or ''),int(item.get('bytes') or 0),str(item.get('sha256') or '').lower())!=want:
        raise SystemExit(f'image manifest mismatch {i}')
    url=str(item.get('transfer_url') or '')
    u=urllib.parse.urlparse(url)
    host=(u.hostname or '').lower()
    if u.scheme!='https' or not host.endswith('.oaiusercontent.com') or not u.path.startswith('/files/') or not u.path.endswith('/raw'):
        raise SystemExit(f'invalid ephemeral transfer url {i}')
print('IMAGE_UPDATE_REQUEST=PASS')
PY

echo '[]' > "$work/images.json"
index=0
while IFS= read -r entry; do
  url="$(jq -r '.transfer_url' <<<"$entry")"
  expected_sha="$(jq -r '.sha256' <<<"$entry" | tr '[:upper:]' '[:lower:]')"
  expected_bytes="$(jq -r '.bytes' <<<"$entry")"
  image_file="$work/images/image-$index.webp"
  code="$(curl --location --silent --show-error --tlsv1.2 --max-time 60 --max-filesize 5242880 --output "$image_file" --write-out '%{http_code}' "$url" || true)"
  test "$code" = '200'
  bytes="$(wc -c < "$image_file" | tr -d '[:space:]')"
  test "$bytes" = "$expected_bytes"
  actual_sha="$(sha256sum "$image_file" | awk '{print $1}')"
  test "$actual_sha" = "$expected_sha"
  test "$(file --brief --mime-type "$image_file")" = 'image/webp'
  b64="$(base64 < "$image_file" | tr -d '\n')"
  jq --arg b64 "$b64" '. + [{content_base64:$b64,alt:"Каратэ «Первый удар»"}]' "$work/images.json" > "$work/images.next"
  mv "$work/images.next" "$work/images.json"
  index=$((index+1))
done < <(jq -c '.image_drive_files[]' "$REQUEST_FILE")
test "$index" = '3'
echo 'SOURCE_IMAGES_VERIFIED=3'

jq -n --slurpfile imgs "$work/images.json" '{
  operation:"update",
  dry_run:true,
  approved_company_id:12,
  product_id:1238,
  product:{name:"Каратэ «Первый удар»"},
  images:$imgs[0]
}' > "$work/dry.json"
jq '.dry_run=false | .approval_id="part-sync-karate-images-20261004"' "$work/dry.json" > "$work/apply.json"

post_signed() {
  body="$1"; request_id="$2"; output="$3"
  ts="$(date +%s)"
  hash="$(sha256sum "$body" | awk '{print $1}')"
  message="$work/message"
  signature="$work/message.sig"
  printf 'talario-part-sync\napply\n%s\n%s\n%s\n' "$request_id" "$ts" "$hash" > "$message"
  rm -f "$signature"
  ssh-keygen -Y sign -q -f "$KEY_FILE" -n talario-part-sync "$message"
  sig="$(base64 < "$signature" | tr -d '\n')"
  curl --silent --show-error --output "$output" --write-out '%{http_code}' --max-time 180 \
    -X POST "$ENDPOINT" \
    -H 'Content-Type: application/json' \
    -H "X-Talario-Request-Id: $request_id" \
    -H "X-Talario-Timestamp: $ts" \
    -H "X-Talario-Signature: $sig" \
    --data-binary "@$body"
}

dry_req="part-sync-apply-${GITHUB_RUN_ID}-image-dry"
dry_code="$(post_signed "$work/dry.json" "$dry_req" "$work/dry.out")"
test "$dry_code" = '200'
jq -e '.schema_version=="partner-sync.write-plan.v1" and .dry_run==true and .plan.operation=="update" and .plan.product_id==1238 and .plan.images.count==3 and .plan.images.replace==true and (.plan.product|keys)==["product"] and .plan.product.product=="Каратэ «Первый удар»" and .plan.booking==null and .plan.variations==null and .plan.filter_features==null' "$work/dry.out" >/dev/null
echo 'SIGNED_DRY_RUN=PASS'

if [ "$ATTEMPT" = '1' ]; then
  echo 'PARTNER_SYNC_STATE=READY_FOR_UPDATE'
  echo 'RESULT=DRY_RUN_PASS_REQUIRES_EXPLICIT_RERUN'
  exit 78
fi

apply_req="part-sync-apply-${GITHUB_RUN_ID}-image-apply"
apply_code="$(post_signed "$work/apply.json" "$apply_req" "$work/apply.out")"
test "$apply_code" = '200'
jq -e '
  .schema_version=="partner-sync.write-result.v1"
  and .dry_run==false
  and .operation=="update"
  and .product_id==1238
  and .readback.product_id==1238
  and .readback.company_id==12
  and .readback.name=="Каратэ «Первый удар»"
  and .readback.status=="H"
  and .readback.images.main==1
  and .readback.images.additional==2
' "$work/apply.out" >/dev/null

echo 'PART_SYNC_PHOTOS=PASS'
echo 'PRODUCT_ID=1238'
echo 'MAIN_IMAGES=1'
echo 'ADDITIONAL_IMAGES=2'
