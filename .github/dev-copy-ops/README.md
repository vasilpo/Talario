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

The Dev Copy Ops request allowlist is:

- `status`
- `git-status`
- `php-lint`
- `worktree-repair`: moves only untracked `config.local.php.bak-partner-sync-*` files out of the checkout into a private Beget state directory. It refuses to run if any other worktree change exists.
- `partner-sync-dry-run`: accepts at most 1 MiB of JSON over stdin and invokes only `php8.2 ops/partner-sync-apply.php`; the workflow smoke payload is forced to `dry_run=true` and therefore cannot write product data.

No arbitrary file paths, shell fragments, HTTP probes, or generic cleanup operations are accepted. `clear-cache` and `partner-sync-probe` remain outside this interface. The separate standard deploy command `talario-dev-deploy` may clear generated cache after a successful fast-forward deploy.

## Security boundaries

- No arbitrary commands: the Beget key is bound in `authorized_keys` to `ops/beget/talario-dev-github-dispatcher.sh`, which accepts only exact named operations.
- No sudo.
- No production directory.
- Mutating Dev Copy Ops operations are narrowly scoped: `worktree-repair` can only relocate the known Partner Sync backup artifact pattern,. The separate standard `talario-dev-deploy` command may clear generated `var/cache` content after a successful fast-forward deploy, with symlink protection.
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

The same forced command must permit exactly `talario-dev-deploy`, `talario-dev-ops status`, `talario-dev-ops git-status`, `talario-dev-ops php-lint`, `talario-dev-ops worktree-repair`, and `talario-dev-ops partner-sync-dry-run`. Any other `SSH_ORIGINAL_COMMAND` must fail closed.

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

## Verified server binding

The active `authorized_keys` entry on Beget points to the out-of-repository dispatcher path:

`/home/t/tyman5tb/.local/bin/talario-dev-github-dispatcher`

This exact binding was installed and verified during bootstrap on 2026-09-21. The repository copy under `ops/beget/` is only the reviewed source used to prepare updates; it is not the path referenced by `authorized_keys`.

## Beget host-key rotation

If Beget rotates the SSH host key, do not fall back to runtime `ssh-keyscan`. Verify the replacement ED25519 fingerprint out-of-band from Beget, update the pinned `salvage.beget.com` entry in both workflows through a reviewed PR, then run Quality & Safety and the Talario Review Agent before merge.


## Self-maintenance bootstrap

The live dispatcher remains an out-of-repository security boundary and is not self-updatable through GitHub Actions. This PR adds only the narrowly scoped `worktree-repair` operation. After its one-time installation on Beget, the current known dirty state can be repaired remotely without exposing arbitrary shell access.

The current known dirty dev_copy state is caused by the untracked file `config.local.php.bak-partner-sync-20260920-015402`. The approved `worktree-repair` operation preserves it by moving it to `~/.local/state/talario/dev-copy-backups/`; it does not delete or print the file contents.
