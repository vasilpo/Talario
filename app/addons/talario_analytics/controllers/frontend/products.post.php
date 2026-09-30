<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

$preview = Tygh::$app['session']['talario_partner_sync_preview'] ?? null;
if (!is_array($preview)) {
    return;
}

$is_exact_preview = isset($_REQUEST['product_id'])
    && (int) ($preview['product_id'] ?? 0) === 1158
    && (string) ($preview['purpose'] ?? '') === 'visual_acceptance'
    && (int) $_REQUEST['product_id'] === 1158;

// The maintenance bypass is required only while products.view resolves the exact
// hidden acceptance card. Remove it before the session is persisted so subsequent
// requests in the same browser cannot reuse closed-storefront access.
unset(Tygh::$app['session']['store_access_key']);
unset(Tygh::$app['session']['talario_partner_sync_preview']);

if (!$is_exact_preview) {
    return [CONTROLLER_STATUS_NO_PAGE];
}
