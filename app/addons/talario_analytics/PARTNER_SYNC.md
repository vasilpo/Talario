# Partner Sync runtime configuration

Partner Sync catalog access is disabled by default in every environment.

## Development / dev_copy

Define the following constants in the non-versioned local CS-Cart configuration for dev_copy:

```php
define('TALARIO_PARTNER_SYNC_DEV_COPY', true);
define('TALARIO_PARTNER_SYNC_TOKEN_HASH', 'sha256:<64 hex characters>');
// Optional and dev_copy-only. Enables approved internal CLI apply after dry-run.
define('TALARIO_PARTNER_SYNC_DEV_WRITE', true);
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

The write engine is the internal CLI runner:

`php ops/partner-sync-apply.php < payload.json`

The signed dev_copy `partner_apply` controller verifies the trusted signature, timestamp and per-run company binding before launching this isolated runner. Direct CLI use remains restricted to the authenticated maintenance path.

Safety properties:

- no public `catalog_apply` HTTP route exists;
- the runner exits unless `PHP_SAPI === 'cli'`;
- the runner resolves the actual repository root and exits unless it ends in `/talario.ru/dev_copy`;
- the runtime must report `fn_is_development() === true`;
- `TALARIO_PARTNER_SYNC_DEV_COPY=true` must be present;
- dry-run is the default;
- an actual apply additionally requires `TALARIO_PARTNER_SYNC_DEV_WRITE=true`;
- the requested partner is resolved per explicit operator run; there is no permanent partner/company allowlist in dev_copy configuration;
- for the signed apply path, the verified request binds `approved_company_id` to the exact target `company_id`; only after signature verification is that one company ID passed into the isolated child runtime as the per-run server-side allowlist;
- the target company must exist and be active; a payload cannot reassign an existing product to another company;
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
  "approved_company_id": 43,
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

<!-- PART-SYNC dev_copy deployment trigger: PR #296 visual acceptance -->


## Scalable approved-card pipeline

Approved dev_copy card creation uses one generic, serialized pipeline instead of partner-specific workflows.

Trust and key handling:

- the private signing key remains only in the GitHub Actions secret store;
- public verification keys live in the reviewed `app/addons/talario_analytics/config/partner_sync_signers.json` manifest; public keys are not secrets;
- the verifier accepts only bounded `ssh-ed25519` entries with status `active` or `next`, maximum four unique signers;
- the workflow derives the public half from the actual GitHub secret and requires exactly one match in the reviewed manifest before any Partner Sync request;
- every run separately performs the existing SSH forced-command preflight with the same private key, so the credential must pass both the reviewed HTTP-signature allowlist and the server SSH trust boundary;
- raw private key material is never committed or printed; logs expose only the public fingerprint;
- key rotation uses overlap: add the next public key to the manifest after/beside server SSH authorization, switch the GitHub secret, prove both preflights and signed dry-run, then remove the retired public key.

Card requests:

- one approved request is stored under `.github/part-sync/requests/*.json`;
- the request must be `operation=create`, `dry_run=false`, and default to product status `H`;
- the first workflow attempt performs source/image validation plus a signed dry-run and then stops;
- only an explicit authenticated rerun may perform the CREATE;
- the apply payload is rebuilt explicitly with `dry_run=false` and a non-empty `approval_id`, so a dry-run payload can never accidentally be reused as an apply payload;
- dev_copy writes are globally serialized with GitHub Actions concurrency to prevent simultaneous card writes;
- CREATE is followed by readback comparison against the dry-run plan and the approved source payload;
- hidden-card storefront QA uses a product/company-bound one-use signed preview, browser rendering, session-state verification, and a short-lived screenshot artifact.

Adding another card therefore requires a new approved request JSON, not a new signing key, workflow, company allowlist, or partner-specific code path.

PROD write remains disabled and is not affected by this dev_copy pipeline.


## Human-in-the-loop resolution

Partner Sync distinguishes a technical failure from a business ambiguity.

A signed dry-run may return a resolvable state instead of a hard failure. The generic runner converts supported ambiguities into a machine-readable checkpoint:

- `state=NEEDS_INPUT`;
- exact stage and reason;
- a bounded operator question;
- candidate IDs and labels when the server can discover them safely;
- the immutable request SHA-256;
- a resume contract.

The workflow exits successfully without CREATE and uploads `needs-input.json` plus a short summary artifact. No partial product is written.

### Category resolver

Category resolution follows this order:

1. explicit reviewed `product.category_ids`;
2. a recorded human decision at `human_decisions.category.category_id`;
3. one exact active/hidden category match under the requested parent;
4. one normalized exact match for harmless spelling-format differences (case, spacing, punctuation, `ё/е`);
5. otherwise `NEEDS_INPUT` with up to ten bounded candidates.

Fuzzy candidates are suggestions only and are never auto-selected. This prevents a spelling variant from silently assigning a card to the wrong taxonomy branch.

To resume after a human answer, update the same approved request with:

```json
{
  "human_decisions": {
    "category": {
      "category_id": 123,
      "selected_by": "vasiliy",
      "selected_at": "2026-10-01T18:20:00Z"
    }
  }
}
```

The runner injects the selected category ID into the signed payload, removes the audit-only `human_decisions` object from transport, and repeats the signed dry-run. The server revalidates that the selected category still exists and is active/hidden before any write.

After a successful resumed dry-run, the existing explicit authenticated rerun gate remains mandatory before CREATE. Therefore a human answer resolves ambiguity without bypassing signature verification, dry-run, readback or storefront QA.


### Chat/session termination rule

`NEEDS_INPUT` is a normal terminal state for the current agent session, not a background wait and not a technical failure.

When the runner emits:

- `PARTNER_SYNC_STATE=NEEDS_INPUT`;
- `SESSION_TERMINAL=YES`;
- `ASSISTANT_ACTION=ASK_USER`;

the orchestrating agent must stop all further tool calls for that card in the current session and immediately return the checkpoint question to the operator. It must not keep polling GitHub, retrying the card, changing taxonomy, or choosing a candidate on the operator's behalf.

The next session starts only after the operator answers. That answer is recorded as an audited `human_decisions` entry and the flow resumes from signed dry-run.

This rule exists so the operator can always distinguish three outcomes in chat:

1. `DONE` — card created and verified;
2. `NEEDS_INPUT` — current session ended and a concrete decision is required;
3. `FAIL` — technical failure requiring investigation.


### Intentional writer normalization

The approved-card runner distinguishes source drift from deterministic server normalization. In particular, Partner Sync intentionally prefixes the product short description with the minimum age derived from the variation plan (for example, `с 1го года`). Dry-run validation mirrors that deterministic rule instead of comparing the raw source string byte-for-byte. CREATE readback must then match the dry-run normalized value exactly.

This prevents a legitimate normalization from being reported as a technical failure while still making the dry-run plan the write contract.

### Idempotent CREATE recovery

The approved-card flow performs a signed read-only lookup after every successful dry-run and before any CREATE rerun.

The lookup is bounded to `dev_copy`, an approved company, an exact product name, and Hidden status. Candidate matching then requires the same:

- company and status;
- category IDs from the dry-run plan;
- address, short-description and full-description SHA-256 values;
- image count;
- variation count and the complete variation-price multiset.

If there is exactly one full match, Partner Sync treats it as the result of a previously successful CREATE whose local runner failed afterwards. Attempt 1 reports `READY_FOR_RECOVERY`; the explicit authenticated rerun reuses that product ID and continues directly to signed preview and storefront QA. It never creates a duplicate.

If several full matches exist, the state becomes `NEEDS_INPUT` and the current agent session terminates. The agent may not select or delete a duplicate on its own.

For variation products, the base-product price is not used as the source of truth because CS-Cart may turn the base product into the first variation. Readback verifies the price of every variation against the approved variation plan instead. For products without variations, the base price remains an exact readback requirement.

The workflow also retains bounded recovery evidence and the raw CREATE result for 14 days so a post-write validator failure can be diagnosed without repeating the write.
