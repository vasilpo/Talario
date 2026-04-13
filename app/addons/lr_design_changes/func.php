<?php

// phpcs:disable PSR1.Files.SideEffects

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Registry;
use Tygh\Tygh;
use Tygh\Enum\YesNo;

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
    $params = $_REQUEST;

    $selected_layout = fn_get_products_layout($params);
    $categories_tree = fn_get_categories_tree(0, false);

    $view = Tygh::$app['view'] ?? null;
    $product_list_template = fn_lr_design_changes_get_homepage_catalog_product_list_template($selected_layout);

    if ($view && $product_list_template) {
        $products_block = $block;
        $products_block['type'] = 'products';
        $products_block['properties']['template'] = $product_list_template;
        $products_block['properties']['show_short_desc'] = YesNo::YES;
        $view->assign('block', $products_block);
    }

    [$products, $search] = fn_get_products(
        $params,
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

    $show_no_products_block = !empty($params['features_hash']) && empty($products);
    fn_filters_handle_search_result($params, $products, $search);
    [$filters] = fn_product_filters_get_filters_products_count($params);

    return [
        'categories_tree'          => $categories_tree,
        'filters'                  => $filters,
        'products'                 => $products,
        'request'                  => $params,
        'search'                   => $search,
        'selected_layout'          => $selected_layout,
        'show_no_products_block'   => $show_no_products_block,
        'target_id'                => 'lr_homepage_catalog_' . $block['block_id'],
    ];
}

/**
 * Resolves product list template path for add-ons that depend on block template context.
 *
 * @param string $selected_layout Product layout identifier.
 *
 * @return string
 */
function fn_lr_design_changes_get_homepage_catalog_product_list_template($selected_layout)
{
    $templates = [
        'grid_list' => 'blocks/products/ab__grid_list.tpl',
        'products_multicolumns' => 'blocks/products/products_multicolumns.tpl',
        'products_without_options' => 'blocks/products/products.tpl',
        'short_list' => 'blocks/products/short_list.tpl',
    ];

    return $templates[$selected_layout] ?? '';
}
