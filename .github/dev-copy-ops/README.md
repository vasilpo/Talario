# Dev Copy Ops

This directory defines the request surface for allowlisted operational checks against the Talario `dev_copy` environment.

## Execution model

- The workflow is trusted code merged into the protected `development` branch.
- It runs only when `.github/dev-copy-ops/request.json` changes on `development`.
- Operational requests must therefore go through the normal PR/review path before merge.
- The request selects one exact allowlisted operation.
- The workflow connects with the existing Beget development SSH secrets.
- Every remote command starts in `/home/t/tyman5tb/talario.ru/public_html/dev_copy` and refuses to run unless the checkout is on the `development` branch.
- There is no arbitrary shell input and no production path.

## Allowlisted operations

- `status`: branch, HEAD, worktree state, PHP version, and presence-only Partner Sync local config checks.
- `git-status`: read-only Git status and HEAD.
- `php-lint`: PHP syntax check for the `talario_analytics` add-on.

## Security boundaries

- No arbitrary commands.
- No sudo.
- No production directory.
- No cache deletion or other destructive filesystem operation.
- No HTTP probes that place storefront access keys in URLs or logs.
- No raw secrets in request files or logs.
- `config.local.php` remains non-versioned.
- SSH host verification must use the pinned `BEGET_KNOWN_HOSTS` repository secret; runtime `ssh-keyscan` is not used.
- Production changes still require a separate explicit decision.

## Required repository controls

- `development` must remain protected and review-gated.
- Do not allow direct pushes that bypass the normal PR checks.
- Keep the Beget SSH secrets scoped to this repository and rotate them if write access or repository ownership changes.
