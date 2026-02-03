<?php

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'partner_site_clicks') {
    $page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
    $items_per_page = (int) Registry::get('settings.Appearance.admin_elements_per_page');
    if ($items_per_page <= 0) {
        $items_per_page = 20;
    }

    $total_items = (int) db_get_field('SELECT COUNT(*) FROM ?:exikane_partner_site_clicks');
    $limit = db_paginate($page, $items_per_page, $total_items);

    $clicks = db_get_array(
        'SELECT click_id, user_id, email, product_id, timestamp FROM ?:exikane_partner_site_clicks ORDER BY click_id DESC ?p',
        $limit
    );

    $search = [
        'page'           => $page,
        'items_per_page' => $items_per_page,
        'total_items'    => $total_items,
    ];

    Tygh::$app['view']->assign([
        'clicks' => $clicks,
        'search' => $search,
    ]);
}

if ($mode === 'partner_site_clicks_export') {
    $clicks = db_get_array(
        'SELECT user_id, email, product_id, timestamp FROM ?:exikane_partner_site_clicks ORDER BY click_id DESC'
    );

    $filename = 'partner_site_clicks_' . date('Ymd') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $output = fopen('php://output', 'w');
    if ($output === false) {
        exit;
    }

    fputcsv($output, [
        __('exikane_changes.partner_site_clicks_user_id'),
        __('exikane_changes.partner_site_clicks_email'),
        __('exikane_changes.partner_site_clicks_product_id'),
        __('date'),
    ]);

    foreach ($clicks as $click) {
        fputcsv($output, [
            $click['user_id'],
            $click['email'],
            $click['product_id'],
            $click['timestamp'] ? fn_date_format($click['timestamp']) : '',
        ]);
    }

    fclose($output);
    exit;
}
