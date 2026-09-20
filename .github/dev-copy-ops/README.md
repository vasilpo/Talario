# Dev Copy Ops

This directory defines the request surface for allowlisted operational checks against the Talario `dev_copy` environment.

## Execution model

- The workflow runs only on pushes to the dedicated `ops/dev-copy` branch.
- Only changes to `.github/dev-copy-ops/request.json` trigger it.
- The request selects one exact allowlisted operation.
- The workflow connects with the existing Beget development SSH secrets.
- Every remote command starts in `/home/t/tyman5tb/talario.ru/public_html/dev_copy` and refuses to run unless the checkout is on the `development` branch.
- There is no arbitrary shell input and no production path.

## Allowlisted operations

- `status`: branch, HEAD, worktree state, PHP version, and presence-only Partner Sync local config checks.
- `git-status`: read-only Git status and HEAD.
- `clear-cache`: clears generated `var/cache` content in dev_copy.
- `php-lint`: PHP syntax check for the `talario_analytics` add-on.
- `partner-sync-probe`: unauthenticated reachability probe for the dev-only Partner Sync endpoint. It reports only HTTP status and a sanitized API error/schema marker; it does not print access keys or bearer tokens.

## Security boundaries

- No arbitrary commands.
- No sudo.
- No production directory.
- No raw secrets in request files or logs.
- `config.local.php` remains non-versioned.
- Production changes still require a separate explicit decision.
