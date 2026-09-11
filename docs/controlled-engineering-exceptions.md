# Controlled engineering exceptions

This register records deliberate deviations from the Talario rule that product logic must live in a Talario add-on and CS-Cart/core or third-party files must remain untouched.

An exception is not a precedent for further edits. Every change to an exception requires a dedicated review, the checks listed below, a rollback path, and an explicit decision to retain or remove it.

## `ec_table_booking_system`: two Talario call-outs

### Scope

`app/addons/ec_table_booking_system/func.php` contains exactly two thin calls into the owned `talario_schedule_resources` add-on:

1. expand a product to the other products that use the same physical resource;
2. replace a dated slot's displayed capacity with the resource occurrence's remaining capacity.

All scheduling, company-boundary, hold, booking, and capacity logic must remain in `talario_schedule_resources`. No additional Talario business logic may be added to the Ecarter add-on.

### Why the exception exists

The upstream add-on does not expose hooks at the two required calculation points. Removing either call-out would make multiple commercial products oversell one physical class or show weekly template capacity instead of the dated occurrence capacity.

### Safety controls

- `ScheduleResourceService` is the application boundary for mutations.
- Runtime `company_id` takes precedence. `admin_company_id` is accepted only in the admin area.
- Resource, location, product, rule, and occurrence ownership is checked before persistence.
- Shared-product and slot-projection queries require the product and resource to belong to the same company.
- Reservation capacity is calculated by occurrence, not by product, under `SELECT ... FOR UPDATE`.
- Automated contract tests cover company isolation, same-resource product expansion, dated capacity projection, and rejection when another product has consumed the shared capacity.

PR #160 added the company/resource consistency controls after the review of the earlier documentation-only PR #159.

### Upgrade and rollback

Before updating `ec_table_booking_system`:

1. compare both call-out locations with the new upstream implementation;
2. check whether native hooks now exist and migrate to them when possible;
3. run `tests/talario_schedule_resources/run.sh`;
4. verify two products of one company sharing one occurrence on `dev_copy`;
5. verify that a product/resource pair from another company cannot be linked or returned;
6. deploy through the controlled `development` → `dev_copy` → `prod` process.

Rollback means restoring the reviewed upstream file and disabling `talario_schedule_resources`. This also disables shared physical capacity and must therefore be paired with suspending affected booking products until capacity handling is restored.

## `app/functions/fn.common.php`: PHP 8.1 null guards

### Scope

Commit `8b27f02479875e20a0000afac523aa930f22a41e` casts the URL/URI input to string at the start of:

- `fn_url()`;
- `fn_get_company_id_from_uri()`;
- `fn_get_storefront_id_from_uri()`.

The casts prevent PHP 8.1 deprecations from `strpos()`/`preg_match()` from being promoted to exceptions when an omitted redirect target reaches these functions.

### Evidence and current decision

On 2026-09-11 a temporary `fn_url(null)` stack diagnostic was installed only on `dev_copy`. The vendor flow from the test admin panel through Dashboard, Center, Classes, and Profile was exercised. No null event was recorded. A CLI PHP 8.1 write test confirmed that the private diagnostic path was writable by the account. The diagnostic was then removed, the original guarded file restored, PHP 8.1 lint passed, and the `development` worktree was clean.

The originating caller therefore remains unproven. The guards stay temporarily because removing them without a reproducible path could restore the PHP 8.1 failure. They must not be used to justify any additional core edits.

### Removal criteria

Remove the guards only in a dedicated reviewed exception PR when all of the following are true:

1. the redirect/authentication flows that can omit a target URL have automated coverage;
2. tests define the expected behavior for null, empty URL, storefront resolution, and company resolution;
3. removal is deployed to `dev_copy` under PHP 8.1 and the relevant admin, vendor, authentication, and storefront redirects are exercised;
4. no null-to-string deprecation, routing regression, company leak, or storefront leak is observed;
5. the PR includes an immediate rollback commit or patch.

Until those criteria are met, this entry is a compatibility boundary, not a completed root-cause fix.
