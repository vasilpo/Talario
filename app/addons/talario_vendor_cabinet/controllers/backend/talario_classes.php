<?php

use Tygh\Enum\Addons\VendorDataPremoderation\ProductStatuses;
use Tygh\Enum\ObjectStatuses;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

$company_id = (int) fn_get_runtime_company_id();
if (!$company_id) {
    return [CONTROLLER_STATUS_DENIED];
}

if ($mode === 'manage') {
    $filter = isset($_REQUEST['talario_status']) ? (string) $_REQUEST['talario_status'] : 'all';
    $params = [
        'company_id' => $company_id,
        'page'       => isset($_REQUEST['page']) ? max(1, (int) $_REQUEST['page']) : 1,
        'extend'     => ['description'],
    ];

    if ($filter === 'active') {
        $params['status'] = ObjectStatuses::ACTIVE;
    } elseif ($filter === 'pending') {
        $params['status'] = ProductStatuses::REQUIRES_APPROVAL;
    } elseif ($filter === 'disabled') {
        $params['status'] = [ObjectStatuses::DISABLED, ObjectStatuses::HIDDEN, ProductStatuses::DISAPPROVED];
    } else {
        $filter = 'all';
    }

    [$products, $search] = fn_get_products($params, 24, DESCR_SL);
    // Defense in depth: never render an item whose owner differs from the runtime vendor.
    $products = array_filter($products, static function (array $product) use ($company_id) {
        return (int) $product['company_id'] === $company_id;
    });
    fn_gather_additional_products_data($products, [
        'get_icon'      => true,
        'get_detailed'  => true,
        'get_features'  => true,
        'get_options'   => false,
        'get_discounts' => false,
    ], DESCR_SL);

    foreach ($products as &$product) {
        $product['talario_age'] = $product['product_features'][552]['value']
            ?? $product['product_features'][552]['variant']
            ?? '';
    }
    unset($product);

    Tygh::$app['view']->assign([
        'talario_products' => $products,
        'talario_search'   => $search,
        'talario_filter'   => $filter,
    ]);
}
