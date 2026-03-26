<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

function fn_lr_design_changes_get_search_recommended_products(string $product_ids_raw): array
{
    if ($product_ids_raw === '') {
        return [];
    }

    $product_ids = explode(',', $product_ids_raw);
    $product_ids = array_filter($product_ids, static function (int $product_id): bool {
        return $product_id > 0;
    });
    $product_ids = array_values(array_unique($product_ids));
    $product_ids = array_slice($product_ids, 0, 3);

    if (empty($product_ids)) {
        return [];
    }

    [$products, $search] = fn_get_products([
        'pid' => $product_ids,
        'extend' => ['description'],
        'force_get_by_ids' => true,
        'apply_limit' => true,
    ], count($product_ids), CART_LANGUAGE);

    if (empty($products) || empty($search['total_items'])) {
        return [];
    }

    $products = fn_sort_by_ids($products, $product_ids);
    $products = array_slice($products, 0, 3);

    fn_gather_additional_products_data($products, [
        'get_icon' => true,
        'get_detailed' => true,
        'get_additional' => true,
        'get_options' => true,
    ]);

    return $products;
}
