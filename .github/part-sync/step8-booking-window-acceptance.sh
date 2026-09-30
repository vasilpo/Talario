#!/usr/bin/env bash
set -euo pipefail

test "${GITHUB_EVENT_NAME:-}" = "push"
test "${GITHUB_REPOSITORY:-}" = "vasilpo/Talario"
test "${GITHUB_REF:-}" = "refs/heads/development"
test "${GITHUB_ACTOR:-}" = "vasilpo"
echo "TRUST_GATE=PASS"

test -s ~/.ssh/id_ed25519
ssh-keygen -y -f ~/.ssh/id_ed25519 >/dev/null
echo "SIGNING_KEY_AVAILABLE=PASS"

tmpdir="$RUNNER_TEMP/part-sync-step8-window-acceptance"
rm -rf "$tmpdir"
mkdir -p "$tmpdir"
chmod 700 "$tmpdir"
trap 'rm -rf "$tmpdir"' EXIT

python3 - "$tmpdir/dry.json" <<'PY'
import json, sys

days = ["monday","tuesday","wednesday","thursday","friday","saturday","sunday"]
booking_days = {day: {"enabled": False, "start": "", "end": ""} for day in days}
booking_days["monday"] = {"enabled": True, "start": "09:00", "end": "12:00"}
booking_days["tuesday"] = {"enabled": True, "start": "11:00", "end": "15:00"}

specs = {
    "3-5 лет": {
        "capacity": 2,
        "schedule": [{"day":"monday","start":"10:00","end":"11:00","duration":60,"capacity":2}],
    },
    "6-9 лет": {
        "capacity": 3,
        "schedule": [{"day":"tuesday","start":"12:00","end":"13:30","duration":90,"capacity":3}],
    },
}

variation_plan = []
for age_group in ["3-5 лет", "6-9 лет"]:
    spec = specs[age_group]
    for purchase_option, price in [
        ("Разовое занятие", 1000.0),
        ("Абонемент 4 занятия", 3600.0),
    ]:
        variation_plan.append({
            "age_group": age_group,
            "purchase_option": purchase_option,
            "price": price,
            "schedule": spec["schedule"],
            "capacity": spec["capacity"],
        })

payload = {
    "operation": "create",
    "dry_run": True,
    "approved_company_id": 39,
    "product": {
        "company_id": 39,
        "name": "PART-SYNC Step 8 Booking Window Acceptance 2026-09-30",
        "price": 1000.0,
        "category_ids": [270],
        "status": "H",
        "short_description": "с 3 лет",
        "full_description": "<p>Техническая Hidden-карточка dev_copy для acceptance Step 8.</p>",
        "meta_keywords": "PART-SYNC Step 8 acceptance",
    },
    "booking": {
        "from": "2026-09-30",
        "to": "2027-09-30",
        "slot_time": 60,
        "free_time": 0,
        "days": booking_days,
    },
    "capacity": 1,
    "variation_plan": variation_plan,
}
with open(sys.argv[1], "w", encoding="utf-8") as fh:
    json.dump(payload, fh, ensure_ascii=False, separators=(",", ":"))
print("PAYLOAD_BUILD=PASS")
PY

post_signed() {
  body="$1"
  request_id="$2"
  out="$3"
  ts="$(date +%s)"
  body_hash="$(sha256sum "$body" | awk '{print $1}')"
  msg="$tmpdir/message"
  sig="$tmpdir/message.sig"
  printf 'talario-part-sync\napply\n%s\n%s\n%s\n' "$request_id" "$ts" "$body_hash" > "$msg"
  rm -f "$sig"
  ssh-keygen -Y sign -q -f ~/.ssh/id_ed25519 -n talario-part-sync "$msg"
  sig_b64="$(base64 -w0 "$sig")"
  curl --silent --show-error --request POST \
    'https://talario.ru/dev_copy/index.php?dispatch=talario_analytics.partner_apply' \
    -H 'Content-Type: application/json' \
    -H "X-Talario-Request-Id: $request_id" \
    -H "X-Talario-Timestamp: $ts" \
    -H "X-Talario-Signature: $sig_b64" \
    --data-binary "@$body" \
    --output "$out" \
    --write-out '%{http_code}'
}

dry_code="$(post_signed "$tmpdir/dry.json" 'part-sync-step8-window-dry-20260930-v1' "$tmpdir/dry.out")"
echo "DRY_HTTP=$dry_code"
test "$dry_code" = "200"

python3 - "$tmpdir/dry.out" <<'PY'
import json, sys

d=json.load(open(sys.argv[1],encoding="utf-8"))
plan=d.get("plan") or {}
variations=plan.get("variations") or {}
windows=plan.get("variation_booking_windows") or []
ff=plan.get("filter_features") or {}

if d.get("schema_version") != "partner-sync.write-plan.v1" or d.get("dry_run") is not True:
    raise SystemExit("dry-run schema mismatch")
if variations.get("resolved") is not True or int(variations.get("count") or 0) != 4:
    raise SystemExit("dry-run variation resolution mismatch")
if ff.get("ages") != [3,4,5,6,7,8,9] or ff.get("category") != "Ранее развитие":
    raise SystemExit("dry-run filter regression")

expected = {
    ("3-5 лет","Разовое занятие"): ("monday","10:00","11:00",60,2,"09:00","12:00"),
    ("3-5 лет","Абонемент 4 занятия"): ("monday","10:00","11:00",60,2,"09:00","12:00"),
    ("6-9 лет","Разовое занятие"): ("tuesday","12:00","13:30",90,3,"11:00","15:00"),
    ("6-9 лет","Абонемент 4 занятия"): ("tuesday","12:00","13:30",90,3,"11:00","15:00"),
}
seen={}
for item in windows:
    key=(item.get("age_group"),item.get("purchase_option"))
    schedule=item.get("schedule") or []
    if key not in expected or len(schedule) != 1:
        raise SystemExit("dry-run booking-window item mismatch")
    row=schedule[0]
    bw=row.get("booking_window") or {}
    actual=(row.get("day"),row.get("start"),row.get("end"),int(row.get("duration") or 0),
            int(row.get("capacity") or 0),bw.get("start"),bw.get("end"))
    if actual != expected[key]:
        raise SystemExit("dry-run booking-window values mismatch")
    seen[key]=actual
if seen != expected:
    raise SystemExit("dry-run booking-window set mismatch")

print("DRY_RUN=PASS")
print("DURATION_60=PASS")
print("DURATION_90=PASS")
print("BOOKING_WINDOW=PASS")
print("CAPACITY_PLAN=PASS")
print("VARIATIONS=4")
PY

python3 - "$tmpdir/dry.json" "$tmpdir/create.json" <<'PY'
import json, sys
p=json.load(open(sys.argv[1],encoding="utf-8"))
p["dry_run"]=False
p["approval_id"]="part-sync-step8-window-accept-20260930-v1"
with open(sys.argv[2],"w",encoding="utf-8") as fh:
    json.dump(p,fh,ensure_ascii=False,separators=(",",":"))
PY

create_code="$(post_signed "$tmpdir/create.json" 'part-sync-step8-window-create-20260930-v1' "$tmpdir/create.out")"
echo "CREATE_HTTP=$create_code"
test "$create_code" = "201"

python3 - "$tmpdir/create.out" <<'PY'
import json, sys

d=json.load(open(sys.argv[1],encoding="utf-8"))
rb=d.get("readback") or {}
ff=rb.get("filter_features") or {}
variations=d.get("variations") or {}
items=variations.get("items") or []

if d.get("schema_version") != "partner-sync.write-result.v1" or d.get("operation") != "create":
    raise SystemExit("create schema mismatch")
if rb.get("status") != "H":
    raise SystemExit("created product is not Hidden")
if ff.get("ages") != [3,4,5,6,7,8,9] or ff.get("category") != "Ранее развитие":
    raise SystemExit("create filter regression")
if int(variations.get("count") or 0) != 4 or len(items) != 4:
    raise SystemExit("create variation count mismatch")

expected = {
    ("3-5 лет","Разовое занятие"): ("monday","10:00","11:00",60,2,"09:00","12:00"),
    ("3-5 лет","Абонемент 4 занятия"): ("monday","10:00","11:00",60,2,"09:00","12:00"),
    ("6-9 лет","Разовое занятие"): ("tuesday","12:00","13:30",90,3,"11:00","15:00"),
    ("6-9 лет","Абонемент 4 занятия"): ("tuesday","12:00","13:30",90,3,"11:00","15:00"),
}
seen={}
for item in items:
    key=(item.get("age_group"),item.get("purchase_option"))
    schedule=item.get("schedule") or []
    if key not in expected or len(schedule) != 1:
        raise SystemExit("create booking-window item mismatch")
    row=schedule[0]
    bw=row.get("booking_window") or {}
    actual=(row.get("day"),row.get("start"),row.get("end"),int(row.get("duration") or 0),
            int(row.get("capacity") or 0),bw.get("start"),bw.get("end"))
    if actual != expected[key]:
        raise SystemExit("create booking-window readback mismatch")
    seen[key]=actual
if seen != expected:
    raise SystemExit("create booking-window set mismatch")

product_id=int(d.get("product_id") or 0)
if product_id <= 0:
    raise SystemExit("missing product id")

print("CREATE=PASS")
print("PRODUCT_ID="+str(product_id))
print("STATUS=H")
print("DURATION_60=PASS")
print("DURATION_90=PASS")
print("BOOKING_WINDOW=PASS")
print("CAPACITY_ACTUAL_SLOT=PASS")
print("VARIATIONS=4")
PY
