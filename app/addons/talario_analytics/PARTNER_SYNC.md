# Partner Sync runtime configuration

Partner Sync catalog access is development-copy only.

## Required local configuration

Define the following constants in the non-versioned local CS-Cart configuration for dev_copy only:

```php
define('TALARIO_PARTNER_SYNC_DEV_COPY', true);
define('TALARIO_PARTNER_SYNC_TOKEN_HASH', 'sha256:<64 hex characters>');
```

Generate a dedicated Partner Sync bearer token outside the repository and store only its SHA-256 hash in `TALARIO_PARTNER_SYNC_TOKEN_HASH`.

Requirements:

- do not commit the raw token or its local configuration file;
- restrict filesystem permissions on the local configuration;
- do not reuse the Analytics API credential;
- do not define these constants in production unless a separate production decision and rollout are approved;
- a missing or malformed hash returns `partner_sync_api_not_configured`;
- a Partner Sync hash matching the Analytics API credential returns `partner_sync_api_misconfigured` and is logged without either credential.

The raw token belongs in the authorized caller's secret store/runtime environment, not in Git or CS-Cart settings.
