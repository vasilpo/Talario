# Dev Copy Ops

This directory defines the request surface for allowlisted operational checks against the Talario `dev_copy` environment.

## Execution model

- The workflow is trusted code merged into the protected `development` branch.
- It runs only when `.github/dev-copy-ops/request.json` changes on `development`.
- Operational requests must therefore go through the normal PR/review path before merge.
- The request contains exactly two fields: `operation` and an auditable `request_id`; unexpected fields or malformed IDs fail validation.
- The workflow connects with the existing Beget development SSH secrets, but the corresponding public key on Beget must be server-side restricted with a forced-command dispatcher.
- Every remote command starts in `/home/t/tyman5tb/talario.ru/public_html/dev_copy` and refuses to run unless the checkout is on the `development` branch.
- There is no arbitrary shell input and no production path.

## Allowlisted operations

- `status`: branch, HEAD, worktree state, PHP version, and presence-only Partner Sync local config checks.
- `git-status`: read-only Git status and HEAD.
- `php-lint`: PHP syntax check for the `talario_analytics` add-on.

## Security boundaries

- No arbitrary commands: the Beget key is bound in `authorized_keys` to `ops/beget/talario-dev-github-dispatcher.sh`, which accepts only exact named operations.
- No sudo.
- No production directory.
- Dev Copy Ops operations are read-only. The separate standard `talario-dev-deploy` command may clear generated `var/cache` content after a successful fast-forward deploy, with symlink protection.
- No HTTP probes that place storefront access keys in URLs or logs.
- No raw secrets in request files or logs.
- `config.local.php` remains non-versioned.
- SSH host verification must use the pinned `BEGET_KNOWN_HOSTS` repository secret; runtime `ssh-keyscan` is not used.
- Both ops and deploy workflows require the dispatcher marker `DISPATCHER=talario-dev-github-v1`; an unrestricted SSH shell will not satisfy the workflow contract.
- Production changes still require a separate explicit decision.

## Required repository controls

- `development` must remain protected and review-gated.
- Do not allow direct pushes that bypass the normal PR checks.
- Keep the Beget SSH secrets scoped to this repository and rotate them if write access or repository ownership changes.

## Beget forced-command requirement

Before merging this workflow, install a copy of `ops/beget/talario-dev-github-dispatcher.sh` outside the Git checkout (for example under `~/.local/bin`) and bind the existing GitHub Actions public key in `~/.ssh/authorized_keys` with `command="...dispatcher...",no-agent-forwarding,no-port-forwarding,no-X11-forwarding,no-pty`. The dispatcher must be owned by the Beget account and writable only by that account. Verify the binding out-of-band before merge. If the GitHub Actions key was previously usable for unrestricted shell access, rotate it after the forced-command migration.

The same forced command must permit exactly `talario-dev-deploy`, `talario-dev-ops status`, `talario-dev-ops git-status`, and `talario-dev-ops php-lint`. Any other `SSH_ORIGINAL_COMMAND` must fail closed.
