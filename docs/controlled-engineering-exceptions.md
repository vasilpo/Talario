# Controlled engineering exceptions

This document records direct modifications that currently cannot be moved cleanly into Talario-owned add-ons/hooks without changing behaviour.

## ec_table_booking_system

File: `app/addons/ec_table_booking_system/func.php`

Talario currently keeps two intentional integration points in the third-party Ecarter booking add-on:

1. After `Fn_Ec_Table_Booking_System_Get_Single_Day_All_slots()` computes its slots, Talario calls
   `fn_talario_schedule_resources_override_single_day_slots()`.
   This projects dated shared-resource capacity into the slot list.

2. `Fn_Ec_Table_Booking_System_Get_Booked_info()` expands a product to
   `fn_talario_schedule_resources_get_shared_product_ids()` before querying booking rows.
   This is required so linked Talario products share booking capacity/history correctly.

The upstream functions do not expose a usable CS-Cart hook at these points. Until Ecarter exposes suitable hooks or the integration is redesigned, these two edits are controlled exceptions.

Rules:
- do not add further Talario logic directly to this file;
- any change to these integration points requires explicit review of shared-capacity behaviour;
- after an Ecarter update, re-check both integration points before deploying;
- preserve the Talario-owned logic in `talario_schedule_resources`; the third-party file should remain only a thin call-out layer.

## CS-Cart core: fn.common.php

File: `app/functions/fn.common.php`

Development currently contains a compatibility guard that casts URL/URI inputs to string in:
- `fn_url()`;
- `fn_get_company_id_from_uri()`;
- `fn_get_storefront_id_from_uri()`.

Reason: PHP 8.1 deprecates passing `null` to string functions such as `strpos()` and `preg_match()`, and the development environment promotes those deprecations to exceptions.

This is a temporary compatibility exception, not the preferred long-term fix.

Rules:
- no additional Talario behaviour may be added to CS-Cart core here;
- future work should identify the concrete caller that supplies `null` and fix it outside core when practical;
- removing this guard requires reproducing the original vendor-cabinet flow on dev_copy without PHP 8.1 deprecation failures.
