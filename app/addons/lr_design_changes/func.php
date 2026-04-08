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

function fn_lr_design_changes_get_homepage_catalog_data($value, array $block, array $block_schema): array
{
    $default_category_id = (int) ($block['content']['default_category_id'] ?? 0);

    if ($default_category_id <= 0) {
        return [];
    }

    $auth = Tygh::$app['session']['auth'] ?? [];
    $category_data = fn_lr_design_changes_get_homepage_catalog_category_data($default_category_id, $auth);

    if (empty($category_data)) {
        return [];
    }

    $request = fn_lr_design_changes_get_homepage_catalog_request();
    $products_params = fn_lr_design_changes_get_homepage_catalog_products_params($default_category_id, $request);
    $filters_params = fn_lr_design_changes_get_homepage_catalog_filters_params($default_category_id, $request, $block);

    [$products, $search] = fn_get_products(
        $products_params,
        Registry::get('settings.Appearance.products_per_page'),
        CART_LANGUAGE
    );

    if (!empty($search['page']) && (int) $search['page'] > 1 && empty($products)) {
        $products_params['page'] = 1;
        [$products, $search] = fn_get_products(
            $products_params,
            Registry::get('settings.Appearance.products_per_page'),
            CART_LANGUAGE
        );
    }

    fn_gather_additional_products_data($products, [
        'get_icon'        => true,
        'get_detailed'    => true,
        'get_additional'  => true,
        'get_options'     => true,
        'get_discounts'   => true,
        'get_features'    => false,
    ]);

    $show_no_products_block = !empty($filters_params['features_hash']) && empty($products);

    fn_filters_handle_search_result($products_params, $products, $search);

    $categories_tree = fn_lr_design_changes_get_homepage_catalog_categories_tree($default_category_id);
    [$filters] = fn_product_filters_get_filters_products_count($filters_params, CART_LANGUAGE);

    return [
        'category_data'            => $category_data,
        'categories_tree'          => $categories_tree,
        'filters'                  => $filters,
        'products'                 => $products,
        'request'                  => $request,
        'search'                   => $search,
        'selected_layout'          => 'products_without_options',
        'show_no_products_block'   => $show_no_products_block,
        'target_id'                => 'lr_homepage_catalog_' . $block['block_id'],
    ];
}

function fn_lr_design_changes_get_homepage_catalog_request(): array
{
    $request = [
        'features_hash'  => isset($_REQUEST['features_hash']) ? (string) $_REQUEST['features_hash'] : '',
        'items_per_page' => isset($_REQUEST['items_per_page']) ? (int) $_REQUEST['items_per_page'] : 0,
        'layout'         => isset($_REQUEST['layout']) ? (string) $_REQUEST['layout'] : '',
        'page'           => isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1,
        'sort_by'        => isset($_REQUEST['sort_by']) ? (string) $_REQUEST['sort_by'] : '',
        'sort_order'     => isset($_REQUEST['sort_order']) ? (string) $_REQUEST['sort_order'] : '',
    ];

    $request['page'] = max(1, $request['page']);
    $request['items_per_page'] = max(0, $request['items_per_page']);

    return $request;
}

function fn_lr_design_changes_get_homepage_catalog_products_params(int $category_id, array $request): array
{
    $params = [
        'category_id' => $category_id,
        'cid'         => $category_id,
        'dispatch'    => 'categories.view',
        'extend'      => ['categories', 'description'],
        'subcats'     => YesNo::YES,
    ];

    if ($request['features_hash'] !== '') {
        $params['features_hash'] = $request['features_hash'];
    }

    if ($request['items_per_page'] > 0) {
        $params['items_per_page'] = $request['items_per_page'];
    }

    if ($request['page'] > 1) {
        $params['page'] = $request['page'];
    }

    if ($request['sort_by'] !== '') {
        $params['sort_by'] = $request['sort_by'];
    }

    if ($request['sort_order'] !== '') {
        $params['sort_order'] = $request['sort_order'];
    }

    return $params;
}

function fn_lr_design_changes_get_homepage_catalog_filters_params(int $category_id, array $request, array $block): array
{
    $block_data = $block;
    $block_data['type'] = 'product_filters';

    $params = [
        'block_data'      => $block_data,
        'category_id'     => $category_id,
        'check_location'  => true,
        'cid'             => $category_id,
        'dispatch'        => 'categories.view',
        'subcats'         => YesNo::YES,
    ];

    if ($request['features_hash'] !== '') {
        $params['features_hash'] = $request['features_hash'];
    }

    return $params;
}

function fn_lr_design_changes_get_homepage_catalog_category_data(int $category_id, array $auth): array
{
    $statuses = [ObjectStatuses::ACTIVE, ObjectStatuses::HIDDEN];
    $condition = fn_get_localizations_condition('localization', true);

    $condition .= ' AND (' . fn_find_array_in_set($auth['usergroup_ids'] ?? [], 'usergroup_ids', true) . ')';
    $condition .= db_quote(' AND status IN (?a)', $statuses);

    $storefront = StorefrontProvider::getStorefront();
    $condition .= db_quote(' AND ?:categories.storefront_id IN (?n)', [0, $storefront->storefront_id]);

    $category_exists = db_get_field(
        'SELECT category_id FROM ?:categories WHERE category_id = ?i ?p',
        $category_id,
        $condition
    );

    if (!$category_exists) {
        return [];
    }

    return (array) fn_get_category_data($category_id, CART_LANGUAGE, '*', true, false, false);
}

function fn_lr_design_changes_get_homepage_catalog_categories_tree(int $default_category_id): array
{
    $categories_tree = fn_get_categories_tree(0, false, CART_LANGUAGE);

    if (empty($categories_tree)) {
        return [];
    }

    fn_lr_design_changes_mark_active_category_in_tree($categories_tree, $default_category_id);

    return $categories_tree;
}

function fn_lr_design_changes_mark_active_category_in_tree(array &$categories_tree, int $default_category_id): bool
{
    $is_active_branch = false;

    foreach ($categories_tree as &$category) {
        $is_current_active = (int) ($category['category_id'] ?? 0) === $default_category_id;
        $is_child_active = false;

        if (!empty($category['subcategories'])) {
            $is_child_active = fn_lr_design_changes_mark_active_category_in_tree(
                $category['subcategories'],
                $default_category_id
            );
        }

        if ($is_current_active || $is_child_active) {
            $category['active'] = true;
            $is_active_branch = true;
        }
    }
    unset($category);

    return $is_active_branch;
}
