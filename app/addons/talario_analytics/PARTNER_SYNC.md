# Partner Sync runtime configuration

Partner Sync catalog access is disabled by default in every environment.

## Development / dev_copy

Define the following constants in the non-versioned local CS-Cart configuration for dev_copy:

```php
define('TALARIO_PARTNER_SYNC_DEV_COPY', true);
define('TALARIO_PARTNER_SYNC_TOKEN_HASH', 'sha256:<64 hex characters>');
// Optional and dev_copy-only. Enables approved internal CLI apply after dry-run.
define('TALARIO_PARTNER_SYNC_DEV_WRITE', true);
define('TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS', '43');
```

## Production read-only mode

Production catalog reads are permitted only after a separate production rollout decision. The code path remains disabled unless production local configuration explicitly contains:

```php
define('TALARIO_PARTNER_SYNC_PROD_READ', true);
define('TALARIO_PARTNER_SYNC_TOKEN_HASH', 'sha256:<64 hex characters>');
```

This production gate enables only the existing GET-only, PII-free catalog snapshot. It does not add create/update/delete operations.

Generate a dedicated Partner Sync bearer token outside the repository and store only its SHA-256 hash in `TALARIO_PARTNER_SYNC_TOKEN_HASH`.

Requirements:

- do not commit the raw token or its local configuration file;
- restrict filesystem permissions on the local configuration;
- do not reuse the Analytics API credential;
- production read access requires a separate explicit rollout decision before the constant is defined or code is deployed to PROD;
- a missing or malformed hash returns `partner_sync_api_not_configured`;
- a Partner Sync hash matching the Analytics API credential returns `partner_sync_api_misconfigured` and is logged without either credential;
- production write operations remain out of scope and require a separate approval-gated production decision.

The raw token belongs in the authorized caller's secret store/runtime environment, not in Git or CS-Cart settings.


## Development write capability

Write is not exposed through the storefront/controller API.

The only supported apply entrypoint is the internal CLI runner:

`php ops/partner-sync-apply.php < payload.json`

It is intended to be invoked only through the existing authenticated dev_copy forced-command/maintenance channel.

Safety properties:

- no public `catalog_apply` HTTP route exists;
- the runner exits unless `PHP_SAPI === 'cli'`;
- the runner resolves the actual repository root and exits unless it ends in `/talario.ru/dev_copy`;
- the runtime must report `fn_is_development() === true`;
- `TALARIO_PARTNER_SYNC_DEV_COPY=true` must be present;
- dry-run is the default;
- an actual apply additionally requires `TALARIO_PARTNER_SYNC_DEV_WRITE=true`;
- writes are restricted to server-side allow-listed partner IDs from `TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS`;
- an actual apply requires a non-empty `approval_id`; only its SHA-256 hash is logged/returned;
- new products default to status `H` unless the caller explicitly supplies `A`;
- partner reassignment on update is rejected;
- product writes use `fn_update_product()`;
- recurring schedule writes use the existing Ecarter `booking_data` hook;
- images are accepted only as bounded JPEG/PNG/WebP binary payloads, validated server-side and attached through the standard CS-Cart product image flow;
- existing images are removed only after the new product/images have been saved successfully;
- the result includes readback of the saved product, price, image counts and booking data.

Example dry-run payload:

```json
{
  "operation": "create",
  "dry_run": true,
  "product": {
    "company_id": 43,
    "name": "Тестовое занятие",
    "price": 750,
    "category_ids": [1],
    "status": "H",
    "full_description": "Описание"
  },
  "booking": {
    "from": "2026-09-21",
    "to": "2027-09-21",
    "slot_time": 90,
    "free_time": 0,
    "days": {
      "monday": {"enabled": true, "start": "17:30", "end": "19:30"},
      "wednesday": {"enabled": true, "start": "17:30", "end": "19:30"},
      "friday": {"enabled": true, "start": "17:30", "end": "19:30"}
    }
  }
}
```

For an actual dev_copy apply, send the same normalized payload with `"dry_run": false` and an `approval_id`. Production write remains disabled and requires a separate explicit decision.
