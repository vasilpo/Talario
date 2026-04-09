<?php

// phpcs:disable PSR1.Files.SideEffects

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\YesNo;
use Tygh\Providers\StorefrontProvider;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Builds homepage catalog block data.
 *
 * @param mixed $value        Unused block content value.
 * @param array $block        Block configuration.
 * @param array $block_schema Block schema.
 *
 * @return array<string, mixed>
 */
function fn_lr_design_changes_get_homepage_catalog_data($value, array $block, array $block_schema): array
{
    $request = $_REQUEST;
    $categories_tree = fn_get_categories_tree(0, false, CART_LANGUAGE);

    [$products, $search] = fn_get_products(
        $request,
        Registry::get('settings.Appearance.products_per_page')
    );

    fn_gather_additional_products_data($products, [
        'get_icon'        => true,
        'get_detailed'    => true,
        'get_additional'  => true,
        'get_options'     => true,
        'get_discounts'   => true,
        'get_features'    => false,
    ]);

    $show_no_products_block = !empty($request['features_hash']) && empty($products);
    fn_filters_handle_search_result($request, $products, $search);
    [$filters] = fn_product_filters_get_filters_products_count($request, CART_LANGUAGE);

    return [
        'categories_tree'          => $categories_tree,
        'filters'                  => $filters,
        'products'                 => $products,
        'request'                  => $request,
        'search'                   => $search,
        'selected_layout'          => fn_get_products_layout($request),
        'show_no_products_block'   => $show_no_products_block,
        'target_id'                => 'lr_homepage_catalog_' . $block['block_id'],
    ];
}
