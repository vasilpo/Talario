# Partner Sync runtime configuration

Partner Sync catalog access is disabled by default in every environment.

## Development / dev_copy

Define the following constants in the non-versioned local CS-Cart configuration for dev_copy:

```php
define('TALARIO_PARTNER_SYNC_DEV_COPY', true);
define('TALARIO_PARTNER_SYNC_TOKEN_HASH', 'sha256:<64 hex characters>');
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
- write operations remain out of scope and require a separate approval-gated implementation and production decision.

The raw token belongs in the authorized caller's secret store/runtime environment, not in Git or CS-Cart settings.

Production hardening:

- catalog requests are separately limited to 10 requests/minute per authenticated Partner Sync credential and 60/minute globally;
- production catalog pages are capped at 100 products per request;
- returned schedule entries are capped at 500 per response;
- legacy Ecarter serialized schedule data is bounded to 8 KiB, scalar weekday fields only, no classes, and max depth 2;
- production does not install a closed-storefront bypass schema; the normal storefront gate remains in force.

The stricter catalog limiter reuses `?:talario_analytics_rate_limits`, which is already created by the add-on install DDL in `addon.xml` and is also used by the existing production Analytics API limiter.
