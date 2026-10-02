<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

$preview = Tygh::$app['session']['talario_partner_sync_preview'] ?? null;
if (!is_array($preview)) {
    return;
}

$preview_product_id = (int) ($preview['product_id'] ?? 0);
$preview_company_id = (int) ($preview['company_id'] ?? 0);
$is_exact_preview = isset($_REQUEST['product_id'])
    && $preview_product_id > 0
    && $preview_company_id > 0
    && (string) ($preview['purpose'] ?? '') === 'visual_acceptance'
    && (int) $_REQUEST['product_id'] === $preview_product_id;

// Capture only bounded proof that the exact one-use preview was consumed.
// Never persist the storefront access key itself after products.view resolves.
if ($is_exact_preview) {
    $runtime_storefront = Tygh::$app['storefront'];
    $session_store_key = (string) (Tygh::$app['session']['store_access_key'] ?? '');
    $runtime_store_key = trim((string) $runtime_storefront->access_key);

    Tygh::$app['session']['talario_partner_sync_preview_consumed'] = [
        'product_id' => $preview_product_id,
        'company_id' => $preview_company_id,
        'purpose' => 'visual_acceptance',
        'store_access_key_present' => $session_store_key !== '',
        'store_access_key_matches_runtime' => $session_store_key !== ''
            && $runtime_store_key !== ''
            && hash_equals($runtime_store_key, $session_store_key),
        'consumed_at' => time(),
    ];
}

// The maintenance bypass is required only while products.view resolves the exact
// hidden acceptance card. Remove it before the session is persisted so subsequent
// requests in the same browser cannot reuse closed-storefront access.
unset(Tygh::$app['session']['store_access_key']);
unset(Tygh::$app['session']['talario_partner_sync_preview']);

if (!$is_exact_preview) {
    return [CONTROLLER_STATUS_NO_PAGE];
}
