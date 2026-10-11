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


## Theme meta templates: PHP 8.1 null compatibility

### Scope

Talario currently carries a narrow compatibility patch in four theme/template files and their matching `var/themes_repository` copies:

- `design/themes/abt__unitheme2/templates/addons/abt__unitheme2/hooks/index/meta.post.tpl`;
- `design/themes/abt__unitheme2/templates/meta.tpl`;
- `design/themes/responsive/templates/addons/seo/hooks/index/meta_description.override.tpl`;
- `design/themes/responsive/templates/meta.tpl`;
- and the corresponding four files under `var/themes_repository/`.

The patch changes only the evaluation order/defaulting around `html_entity_decode`: a missing meta description is converted to an empty string before decode, preventing PHP 8.1 null deprecation warnings. It does not add Talario product logic, routing, authorization, data access, or storefront behavior.

### Why the exception remains

Moving this safeguard into a Talario add-on would require overriding complete theme meta hooks/templates merely to preserve a one-expression PHP compatibility guard. That would create a larger maintenance surface than the existing patch and increase the chance of drifting from future UniTheme/CS-Cart template updates.

The direct patch therefore remains a controlled compatibility exception until the upstream theme/core templates become null-safe.

### Safety controls

- No Talario business logic may be added to these files.
- Allowed changes are limited to null/default handling required for PHP compatibility.
- Every CS-Cart, UniTheme, or SEO add-on upgrade must compare these templates against upstream before deployment.
- The paired files under `design/themes` and `var/themes_repository` must remain functionally identical for this guard.
- Any broader metadata/SEO behavior change must be implemented through normal theme hooks/add-ons, not by expanding this exception.

### Removal criteria

Remove the patch when any of the following is true:

1. the upstream CS-Cart/UniTheme/SEO templates default nullable meta values before `html_entity_decode`;
2. the application stack no longer emits the relevant null-to-string deprecation under the supported PHP version;
3. a smaller supported hook/extension point becomes available that avoids replacing whole vendor templates.

Removal must be verified on `dev_copy` for product, category, page, search/SEO, and pages with an empty meta description before release to PROD.


## VK ID compatibility in `hybrid_auth` / `rus_hybrid_auth`

### Scope

Talario currently retains the existing VK ID OAuth/PKCE compatibility changes inside the installed `hybrid_auth` and `rus_hybrid_auth` add-ons. These changes provide the working `/auth/vkontakte` callback and VK ID adapter behaviour already used by the application.

A separate Talario-owned `talario_vk_auth` add-on was evaluated during A-13 but deliberately abandoned before any PROD release. It was removed from `dev_copy` and is being removed from `development`.

### Current decision

Do not migrate or further refactor the working VK login solely to eliminate these direct vendor edits. The current login works, and the migration cost/risk is disproportionate to the present benefit.

This exception is not permission to add new Talario logic to these vendor add-ons. Any future VK authentication feature or behavioural change must first reassess whether a supported extension point is available.

### Upgrade controls

Before updating CS-Cart, `hybrid_auth`, or `rus_hybrid_auth`:

1. compare the VK-related files with upstream;
2. confirm the `/auth/vkontakte` route and callback still exist;
3. confirm VK ID token exchange, PKCE, `device_id`, and profile mapping remain supported;
4. test VK login end-to-end on `dev_copy`;
5. only then release through the normal controlled process.

### Removal criteria

Remove this exception only when upstream provides equivalent VK ID behaviour or when a future product/security requirement justifies a reviewed migration to a Talario-owned extension.

PROD must not receive the abandoned `talario_vk_auth` experiment.

## `newsletters`: CRM-01 recipient personalization

### Scope

`app/addons/newsletters/func.php` contains Talario changes introduced for CRM-01 recipient personalization. The change carries sanitized customer `firstname` and reward-point payload into the existing newsletter rendering path, merges explicitly selected user and mailing-list recipient metadata by email, and keeps the existing mailing-list consent/unsubscribe path intact.

The behavior is covered by `app/addons/newsletters/tests/NewsletterFirstnameContractTest.php`.

### Current decision

Keep this customization temporarily, but do not treat the `newsletters` add-on as an approved home for further Talario CRM logic. The current implementation is a known direct third-party/add-on modification and must be reconsidered before the next material CRM/newsletter change or before upgrading the newsletters add-on.

No additional personalization, segmentation, points logic, or Talario business rules may be added to `app/addons/newsletters/func.php` without first checking for a supported hook or a Talario-owned extension boundary.

### Safety and removal criteria

Before modifying or upgrading newsletters:

1. run `NewsletterFirstnameContractTest`;
2. verify direct-user personalization, mailing-list consent filtering, deduplication by email and `%UNSUBSCRIBE_LINK` behavior;
3. confirm first name output remains control-character stripped and HTML-escaped;
4. inspect upstream hooks around recipient preparation and rendering;
5. move the customization into a Talario-owned add-on when an equivalent supported extension point can preserve these invariants without copying a larger upstream function.

Until then this is a temporary controlled exception, not a precedent for more CRM code in the vendor add-on.

## `dev_copy` product 1238 storefront template patches

### Scope

The `development` branch currently contains three product-specific template conditions for the hidden Partner Sync acceptance card `product_id=1238`:

- `design/themes/abt__unitheme2/templates/addons/geo_maps/hooks/products/geo_maps.pre.tpl` suppresses the stock geo-maps block for product 1238;
- `design/themes/abt__unitheme2/templates/addons/sd_design_changes/overrides/addons/abt__unitheme2_mv/hooks/products/ab__vendor_block.pre.tpl` suppresses the standalone vendor-block address for product 1238;
- the Talario-owned `design/themes/abt__unitheme2/templates/addons/talario_analytics/hooks/products/bottom_product_layer.post.tpl` suppresses only the duplicate address line while preserving the dedicated lesson-map canvas for product 1238.

These conditions came from the bounded dev_copy visual acceptance work for the Partner Sync pilot. They are not general product behavior and are not an approved pattern for future product IDs.

### Current decision

Treat the two edits inside foreign/theme-owned files as temporary dev_copy acceptance debt. Do not copy this pattern to any other product. Do not promote these product-ID conditions to PROD as a generic Talario architecture decision.

The Talario-owned lesson-map hook remains the preferred extension boundary. Before removing the two foreign-file conditions, dev_copy acceptance must prove that the card still renders exactly one intended lesson-map experience, without reintroducing duplicate address/map output.

### Removal criteria

Remove the foreign-file product-1238 conditions when one of the following is true:

1. the Partner Sync acceptance card is retired and its special visual behavior is no longer required; or
2. equivalent suppression is implemented through a Talario-owned supported hook/override without copying a full third-party template; or
3. the storefront design is changed so the stock and Talario map/address blocks no longer duplicate each other.

Removal must be verified on `dev_copy` for product 1238 and at least one normal product with an address. PROD remains out of scope until a separate release decision.
