#!/usr/bin/env bash
set -euo pipefail

test "${GITHUB_EVENT_NAME:-}" = "push"
test "${GITHUB_REPOSITORY:-}" = "vasilpo/Talario"
test "${GITHUB_REF:-}" = "refs/heads/development"
test "${GITHUB_ACTOR:-}" = "vasilpo"

EXPECTED_SIGNER='ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIA/89+6Q50ah8vHptYSd4T6GsrhW+mYwf/xpNyZyAdDP'
ACTUAL_SIGNER="$(ssh-keygen -y -f ~/.ssh/id_ed25519 | awk '{print $1 " " $2}')"
test "$ACTUAL_SIGNER" = "$EXPECTED_SIGNER"
echo "SIGNING_KEY_MATCH=PASS"

tmpdir="${RUNNER_TEMP}/part-sync-map-e2e"
rm -rf "$tmpdir"
mkdir -p "$tmpdir"
chmod 700 "$tmpdir"
trap 'rm -rf "$tmpdir"' EXIT

python3 - "$tmpdir/dry.json" <<'PY'
import json, sys
days=["monday","tuesday","wednesday","thursday","friday","saturday","sunday"]
booking_days={day:{"enabled":day=="monday","start":"10:00" if day=="monday" else "","end":"11:00" if day=="monday" else ""} for day in days}
payload={
  "operation":"create",
  "dry_run":True,
  "approved_company_id":39,
  "product":{
    "company_id":39,
    "name":"PART-SYNC Map Acceptance 2026-10-01",
    "price":1000.0,
    "category_ids":[270],
    "status":"H",
    "address":"г. Красногорск, ул. Вилора Трифонова дом 1",
    "short_description":"с 6 лет",
    "full_description":"<p>Техническая Hidden-карточка dev_copy для E2E acceptance карты PART-SYNC.</p>",
    "meta_keywords":"PART-SYNC map acceptance 2026-10-01"
  },
  "booking":{"from":"2026-10-01","to":"2027-10-01","slot_time":60,"free_time":0,"days":booking_days},
  "capacity":1,
  "variation_plan":[{
    "age_group":"6-9 лет",
    "purchase_option":"Разовое занятие",
    "price":1000.0,
    "schedule":[{"day":"monday","start":"10:00","end":"11:00","duration":60,"capacity":1}],
    "capacity":1
  }]
}
with open(sys.argv[1],"w",encoding="utf-8") as fh:
    json.dump(payload,fh,ensure_ascii=False,separators=(",",":"))
print("PAYLOAD_BUILD=PASS")
PY

post_signed() {
  body="$1"; req="$2"; out="$3"
  ts="$(date +%s)"
  body_hash="$(sha256sum "$body" | awk '{print $1}')"
  printf 'talario-part-sync\napply\n%s\n%s\n%s\n' "$req" "$ts" "$body_hash" > "$tmpdir/message"
  rm -f "$tmpdir/message.sig"
  ssh-keygen -Y sign -q -f ~/.ssh/id_ed25519 -n talario-part-sync "$tmpdir/message"
  sig="$(base64 < "$tmpdir/message.sig" | tr -d '\n')"
  curl --silent --show-error --output "$out" --write-out '%{http_code}' \
    --connect-timeout 15 --max-time 180 \
    -X POST 'https://talario.ru/dev_copy/index.php?dispatch=talario_analytics.partner_apply' \
    -H 'Content-Type: application/json' \
    -H "X-Talario-Request-Id: $req" \
    -H "X-Talario-Timestamp: $ts" \
    -H "X-Talario-Signature: $sig" \
    --data-binary "@$body"
}

dry_code="$(post_signed "$tmpdir/dry.json" "part-sync-apply-map-e2e-${GITHUB_RUN_ID}-dry" "$tmpdir/dry.out")"
echo "DRY_HTTP=$dry_code"
test "$dry_code" = "200"

python3 - "$tmpdir/dry.out" <<'PY'
import json,sys
d=json.load(open(sys.argv[1],encoding="utf-8"))
assert d.get("schema_version")=="partner-sync.write-plan.v1"
assert d.get("dry_run") is True
p=(d.get("plan") or {}).get("product") or {}
assert p.get("status")=="H"
assert p.get("address")=="г. Красногорск, ул. Вилора Трифонова дом 1"
print("DRY_RUN=PASS")
print("DRY_ADDRESS=PASS")
PY

python3 - "$tmpdir/dry.json" "$tmpdir/create.json" <<'PY'
import json,sys
p=json.load(open(sys.argv[1],encoding="utf-8"))
p["dry_run"]=False
p["approval_id"]="part-sync-map-e2e-accept-20261001-v2"
with open(sys.argv[2],"w",encoding="utf-8") as fh:
    json.dump(p,fh,ensure_ascii=False,separators=(",",":"))
PY

create_code="$(post_signed "$tmpdir/create.json" "part-sync-apply-map-e2e-${GITHUB_RUN_ID}-create" "$tmpdir/create.out")"
echo "CREATE_HTTP=$create_code"
if [ "$create_code" != "201" ]; then
  python3 - "$tmpdir/create.out" <<'PY'
import json,sys
try:
    d=json.load(open(sys.argv[1],encoding="utf-8"))
except Exception:
    print("CREATE_ERROR=invalid_response")
    raise SystemExit(1)
print("CREATE_ERROR="+str(d.get("error","unknown")))
print("RUNNER_RC="+str(d.get("runner_rc","")))
raise SystemExit(1)
PY
fi

python3 - "$tmpdir/create.out" <<'PY'
import json,sys
d=json.load(open(sys.argv[1],encoding="utf-8"))
rb=d.get("readback") or {}
pid=int(d.get("product_id") or 0)
assert d.get("schema_version")=="partner-sync.write-result.v1"
assert d.get("operation")=="create"
assert d.get("dry_run") is False
assert pid>0
assert rb.get("status")=="H"
assert rb.get("address")=="г. Красногорск, ул. Вилора Трифонова дом 1"
print("CREATE=PASS")
print("PRODUCT_ID="+str(pid))
print("READBACK_NAME="+str(rb.get("name","")))
print("READBACK_STATUS="+str(rb.get("status","")))
print("READBACK_ADDRESS="+str(rb.get("address","")))
print("PART_SYNC_ADDRESS_E2E_WRITE=PASS")
PY
