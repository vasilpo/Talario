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

The Dev Copy Ops request allowlist is exactly three read/check operations:

- `status`
- `git-status`
- `php-lint`

No other Dev Copy Ops operation is supported. `clear-cache` and `partner-sync-probe` are not part of this interface. The separate standard deploy command `talario-dev-deploy` may clear generated cache after a successful fast-forward deploy.

## Security boundaries

- No arbitrary commands: the Beget key is bound in `authorized_keys` to `ops/beget/talario-dev-github-dispatcher.sh`, which accepts only exact named operations.
- No sudo.
- No production directory.
- Dev Copy Ops operations are read-only. The separate standard `talario-dev-deploy` command may clear generated `var/cache` content after a successful fast-forward deploy, with symlink protection.
- No HTTP probes that place storefront access keys in URLs or logs.
- No raw secrets in request files or logs.
- `config.local.php` remains non-versioned.
- SSH host verification uses the pinned public Beget ED25519 host key committed in the workflow; runtime `ssh-keyscan` and an extra repository secret are not used.
- The Beget GitHub Actions key has been verified out-of-band as forced-command bound; both ops and deploy workflows require the dispatcher marker `DISPATCHER=talario-dev-github-v1`; an unrestricted SSH shell will not satisfy the workflow contract.
- Production changes still require a separate explicit decision.

## Required repository controls

- `development` must remain protected and review-gated.
- Do not allow direct pushes that bypass the normal PR checks.
- Keep the Beget SSH secrets scoped to this repository and rotate them if write access or repository ownership changes.

## Beget forced-command requirement

Install a copy of `ops/beget/talario-dev-github-dispatcher.sh` outside the Git checkout and bind the GitHub Actions public key in `~/.ssh/authorized_keys` with `command="...dispatcher...",no-agent-forwarding,no-port-forwarding,no-X11-forwarding,no-pty`. The dispatcher must be owned by the Beget account and writable only by that account. Verify the binding out-of-band before merge.

The same forced command must permit exactly `talario-dev-deploy`, `talario-dev-ops status`, `talario-dev-ops git-status`, and `talario-dev-ops php-lint`. Any other `SSH_ORIGINAL_COMMAND` must fail closed.

## Verified bootstrap state

The GitHub Actions SSH key was rotated during bootstrap. The replacement key was bound to the forced-command dispatcher and verified out-of-band: `talario-dev-ops status` returned `DISPATCHER=talario-dev-github-v1`, while a non-allowlisted `uname -a` command was rejected. The previous GitHub Actions key was then removed from `authorized_keys` and deleted from the Beget account.

### Bootstrap verification evidence — 2026-09-21

Out-of-band Beget verification completed before merge:

- `OLD_KEY_LINES=0`
- `NEW_KEY_LINES=1`
- `OLD_KEY_REMOVED=YES`
- `NEW_KEY_ACTIVE=YES`
- allowlisted `talario-dev-ops status` returned `DISPATCHER=talario-dev-github-v1`
- non-allowlisted `uname -a` returned `ERROR: SSH command is not allowlisted`

This verifies the replacement key is the only GitHub Actions key retained for this channel and is bound to the forced-command dispatcher.
