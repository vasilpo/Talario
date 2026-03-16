<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Addons\PartnerSites\Repository\PartnerSiteClickRepository;
use Tygh\Registry;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

$repository = PartnerSiteClickRepository::create();

if ($mode === 'partner_site_clicks') {
    $page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
    $items_per_page = (int) Registry::get('settings.Appearance.admin_elements_per_page');

    if ($items_per_page <= 0) {
        $items_per_page = 20;
    }

    $total_items = $repository->getTotalCount();
    $limit = db_paginate($page, $items_per_page, $total_items);

    Tygh::$app['view']->assign([
        'clicks' => $repository->findAll($limit),
        'search' => [
            'page'           => $page,
            'items_per_page' => $items_per_page,
            'total_items'    => $total_items,
        ],
    ]);
}

if ($mode === 'partner_site_clicks_export') {
    $clicks = $repository->findAll();
    $filename = 'partner_site_clicks_' . date('Ymd') . '.csv';

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $output = fopen('php://output', 'w');
    if ($output === false) {
        exit;
    }

    fputcsv($output, [
        __('partner_sites.partner_site_clicks_user_id'),
        __('partner_sites.partner_site_clicks_email'),
        __('partner_sites.partner_site_clicks_product_id'),
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
