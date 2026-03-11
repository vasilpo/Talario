<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Registry;
use Tygh\Tygh;

defined('BOOTSTRAP') or exit;

/** @var string $mode */
if ($mode === 'get_products') {
    $products = [];
    $features_hash = isset($_REQUEST['features_hash']) ? (string) $_REQUEST['features_hash'] : '';
    $products_dropdown_limit = (int) Registry::get('addons.sd_home_filters.products_dropdown_limit');
    $products_dropdown_limit = $products_dropdown_limit > 0 ? $products_dropdown_limit : 10;
    $has_more_products = false;
    $search_url = '';

    if ($features_hash !== '') {
        // CART_LANGUAGE is a CS-Cart bootstrap constant available in controller runtime.
        /** @phpstan-ignore constant.notFound */
        $lang_code = CART_LANGUAGE;

        list($product_list, $search) = fn_get_products([
            'features_hash'  => $features_hash,
            'items_per_page' => $products_dropdown_limit,
            'sort_by'        => 'product',
            'sort_order'     => 'asc',
            'extend'         => ['description'],
        ], $products_dropdown_limit, $lang_code);

        foreach ($product_list as $product) {
            if (empty($product['product_id']) || empty($product['product'])) {
                continue;
            }

            // Return only fields needed by the products dropdown.
            $products[] = [
                'product_id'  => (int) $product['product_id'],
                'product'     => $product['product'],
                'product_url' => fn_url('products.view?product_id=' . (int) $product['product_id']),
            ];
        }

        $has_more_products = !empty($search['total_items']) && (int) $search['total_items'] > $products_dropdown_limit;
        $search_url = fn_url('products.search?features_hash=' . $features_hash . '&search_performed=Y');
    }

    Tygh::$app['ajax']->assign('sd_home_filters_products', [
        'items'      => $products,
        'has_more'   => $has_more_products,
        'search_url' => $search_url,
    ]);

    // CONTROLLER_STATUS_NO_CONTENT is provided by the CS-Cart controller bootstrap.
    /** @phpstan-ignore constant.notFound */
    return [CONTROLLER_STATUS_NO_CONTENT];
}
