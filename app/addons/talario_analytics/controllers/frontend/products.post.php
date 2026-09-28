<?php

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

$preview = Tygh::$app['session']['talario_partner_sync_preview'] ?? null;
if (!is_array($preview)) {
    return;
}

$is_exact_preview = isset($_REQUEST['product_id'], $_REQUEST['action'])
    && (int) $preview['product_id'] === 1158
    && (int) $_REQUEST['product_id'] === 1158
    && (string) $_REQUEST['action'] === 'preview';

if (!$is_exact_preview) {
    Tygh::$app['session']['auth']['area'] = 'C';
    unset(Tygh::$app['session']['talario_partner_sync_preview']);
    return;
}

// The hidden product has already been resolved/render data assigned by products.php.
// Remove preview capability before the session is persisted, so this browser session
// cannot reuse area=A for another hidden product or a second preview request.
Tygh::$app['session']['auth']['area'] = 'C';
unset(Tygh::$app['session']['talario_partner_sync_preview']);
