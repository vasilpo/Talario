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
use Tygh\Registry;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

const LR_HOMEPAGE_SEARCH_FILTERS_MAX_PRODUCTS_DROPDOWN_LIMIT = 100;

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
    $filters = fn_lr_design_changes_sort_homepage_catalog_filters($filters, $block);

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

/**
 * Moves the category filter to the first position in homepage catalog filters.
 *
 * @param array<int|string, array<string, mixed>> $filters Homepage catalog filters.
 * @param array<string, mixed>                    $block   Block configuration.
 *
 * @return array<int|string, array<string, mixed>>
 */
function fn_lr_design_changes_sort_homepage_catalog_filters(array $filters, array $block): array
{
    $properties = !empty($block['properties']) && is_array($block['properties'])
        ? $block['properties']
        : [];
    $category_filter_id = !empty($properties['category_filter_id'])
        ? (int) $properties['category_filter_id']
        : 0;

    if ($category_filter_id <= 0 || empty($filters[$category_filter_id])) {
        return $filters;
    }

    $category_filter = [$category_filter_id => $filters[$category_filter_id]];
    unset($filters[$category_filter_id]);

    return $category_filter + $filters;
}

/**
 * Builds standalone homepage search filters block data.
 *
 * @param mixed $value        Unused block content value.
 * @param array $block        Block configuration.
 * @param array $block_schema Block schema.
 *
 * @return array<string, mixed>
 */
function fn_lr_design_changes_get_homepage_search_filters_data($value, array $block, array $block_schema): array
{
    $properties = !empty($block['properties']) && is_array($block['properties'])
        ? $block['properties']
        : [];
    $filter_configs = fn_lr_design_changes_get_homepage_search_filter_configs($block);
    $filters = fn_lr_design_changes_get_homepage_search_filters($filter_configs);
    $category_filter_id = 0;

    foreach ($filters as $filter) {
        if ($filter['key'] === 'category') {
            $category_filter_id = (int) $filter['filter_id'];
            break;
        }
    }

    return [
        'block_id'                => (int) ($block['block_id'] ?? 0),
        'category_filter_id'      => $category_filter_id,
        'filters'                 => $filters,
        'products_dropdown_limit' => fn_lr_design_changes_get_homepage_search_products_dropdown_limit($properties),
        'products_endpoint'       => fn_url('lr_homepage_search_filters.get_products'),
        'search_url'              => fn_url('products.search'),
    ];
}

/**
 * Resolves maximum number of lessons shown in the dependent dropdown.
 *
 * @param array<string, mixed> $properties Block properties.
 *
 * @return int
 */
function fn_lr_design_changes_get_homepage_search_products_dropdown_limit(array $properties): int
{
    $products_dropdown_limit = !empty($properties['products_dropdown_limit'])
        ? (int) $properties['products_dropdown_limit']
        : 10;

    return $products_dropdown_limit > 0 ? $products_dropdown_limit : 10;
}

/**
 * Builds AJAX response for category-based lesson dropdown items.
 *
 * @param array<string, mixed> $request Request data.
 *
 * @return array<string, mixed>
 */
function fn_lr_design_changes_get_homepage_search_filters_products_response(array $request): array
{
    $features_hash = isset($request['features_hash']) ? (string) $request['features_hash'] : '';
    $category_names = isset($request['category_names']) ? (array) $request['category_names'] : [];
    $category_filter_id = isset($request['category_filter_id']) ? (int) $request['category_filter_id'] : 0;
    $selected_category_ids = isset($request['selected_category_ids'])
        ? (array) $request['selected_category_ids']
        : [];
    $search_params = isset($request['search_params']) ? (array) $request['search_params'] : [];
    $products_dropdown_limit = isset($request['products_dropdown_limit'])
        ? (int) $request['products_dropdown_limit']
        : 10;
    $products_dropdown_limit = max(
        1,
        min($products_dropdown_limit, LR_HOMEPAGE_SEARCH_FILTERS_MAX_PRODUCTS_DROPDOWN_LIMIT)
    );
    $items = [];
    $search_url = '';

    $category_names = array_values(array_unique(array_filter(array_map('trim', $category_names))));
    $selected_category_ids = array_values(array_unique(array_filter(array_map('intval', $selected_category_ids))));
    $search_params = array_filter($search_params, static function ($value, $key) {
        if (in_array($key, ['dispatch', 'features_hash', 'cid', 'category_id', 'search_performed'], true)) {
            return false;
        }

        return $value !== '' && $value !== null;
    }, ARRAY_FILTER_USE_BOTH);

    if ($features_hash !== '' && $category_names && $category_filter_id > 0) {
        $selected_filters = fn_parse_filters_hash($features_hash);

        // The selected lesson is passed via cid, so remove the parent category feature filter.
        unset($selected_filters[$category_filter_id]);

        $remaining_features_hash = $selected_filters
            ? fn_generate_filter_hash($selected_filters)
            : '';

        $category_data = fn_lr_design_changes_get_homepage_search_filters_subcategories(
            $category_names,
            $products_dropdown_limit
        );

        $items = $category_data['items'];
        $parent_category_ids = $category_data['parent_category_ids'];

        if ($selected_category_ids) {
            $search_url = 'products.search?search_performed=Y&cid=' . implode(',', $selected_category_ids);
        } elseif (count($category_names) === 1 && !empty($parent_category_ids[0])) {
            // Multiple parent categories stay in features_hash on the client side.
            $search_url = 'products.search?search_performed=Y&cid=' . (int) $parent_category_ids[0];
        }

        if ($search_url !== '') {
            if ($remaining_features_hash !== '') {
                $search_url .= '&features_hash=' . urlencode($remaining_features_hash);
            }

            if ($search_params) {
                $search_url .= '&' . http_build_query($search_params);
            }

            $search_url = fn_url($search_url);
        }
    }

    return [
        'items'      => $items,
        'search_url' => $search_url,
    ];
}

/**
 * Loads selected parent category IDs and active child categories for the lessons dropdown.
 *
 * @param array<int, string> $category_names          Parent category names.
 * @param int               $products_dropdown_limit Maximum dropdown items count.
 *
 * @return array{items: array<int, array<string, int|string>>, parent_category_ids: array<int, int>}
 */
function fn_lr_design_changes_get_homepage_search_filters_subcategories(
    array $category_names,
    int $products_dropdown_limit
): array {
    $items = [];
    $parent_category_ids = [];
    $subcategories = db_get_array(
        'SELECT parent.category_id AS parent_category_id, sub.category_id, sub_desc.category'
        . ' FROM ?:categories AS parent'
        . ' INNER JOIN ?:category_descriptions AS parent_desc'
        . ' ON parent_desc.category_id = parent.category_id'
        . ' AND parent_desc.lang_code = ?s'
        . ' LEFT JOIN ?:categories AS sub'
        . ' ON sub.parent_id = parent.category_id'
        . ' AND sub.status = ?s'
        . ' LEFT JOIN ?:category_descriptions AS sub_desc'
        . ' ON sub_desc.category_id = sub.category_id'
        . ' AND sub_desc.lang_code = ?s'
        . ' WHERE parent_desc.category IN (?a)'
        . ' AND parent.status = ?s'
        . ' ORDER BY sub.position ASC, sub_desc.category ASC'
        . ' LIMIT ?i',
        CART_LANGUAGE,
        ObjectStatuses::ACTIVE,
        CART_LANGUAGE,
        $category_names,
        ObjectStatuses::ACTIVE,
        $products_dropdown_limit
    );

    foreach ($subcategories as $subcategory) {
        if (!empty($subcategory['parent_category_id'])) {
            $parent_category_id = (int) $subcategory['parent_category_id'];
            $parent_category_ids[$parent_category_id] = $parent_category_id;
        }

        if (empty($subcategory['category_id']) || empty($subcategory['category'])) {
            continue;
        }

        $items[] = [
            'item_id' => (int) $subcategory['category_id'],
            'item'    => (string) $subcategory['category'],
        ];
    }

    return [
        'items'               => $items,
        'parent_category_ids' => array_values($parent_category_ids),
    ];
}

/**
 * Resolves filter configuration for the standalone homepage search block.
 *
 * @param array<string, mixed> $block Block configuration.
 *
 * @return array<int, array<string, int|string>>
 */
function fn_lr_design_changes_get_homepage_search_filter_configs(array $block): array
{
    $properties = !empty($block['properties']) && is_array($block['properties'])
        ? $block['properties']
        : [];

    return [
        [
            'key'       => 'city',
            'filter_id' => fn_lr_design_changes_get_homepage_search_configured_filter_id(
                $properties,
                'city_filter_id'
            ),
        ],
        [
            'key'       => 'age',
            'filter_id' => fn_lr_design_changes_get_homepage_search_configured_filter_id(
                $properties,
                'age_filter_id'
            ),
        ],
        [
            'key'       => 'category',
            'filter_id' => fn_lr_design_changes_get_homepage_search_configured_filter_id(
                $properties,
                'category_filter_id'
            ),
        ],
        [
            'key'       => 'free_trial',
            'filter_id' => fn_lr_design_changes_get_homepage_search_configured_filter_id(
                $properties,
                'free_trial_filter_id'
            ),
        ],
    ];
}

/**
 * Resolves a block property.
 *
 * @param array<string, mixed> $properties   Block properties.
 * @param string               $property_key Block property key.
 *
 * @return int
 */
function fn_lr_design_changes_get_homepage_search_configured_filter_id(
    array $properties,
    string $property_key
): int {
    if (!empty($properties[$property_key])) {
        return (int) $properties[$property_key];
    }

    return 0;
}

/**
 * Loads active product filters and normalizes them for the standalone template.
 *
 * @param array<int, array<string, int|string>> $filter_configs Filter configs.
 *
 * @return array<int, array<string, mixed>>
 */
function fn_lr_design_changes_get_homepage_search_filters(array $filter_configs): array
{
    $candidate_filter_ids = [];

    foreach ($filter_configs as $filter_config) {
        $filter_id = (int) $filter_config['filter_id'];

        if ($filter_id > 0) {
            $candidate_filter_ids[] = $filter_id;
        }
    }

    $candidate_filter_ids = array_values(array_unique($candidate_filter_ids));

    if (empty($candidate_filter_ids)) {
        return [];
    }

    [$available_filters] = fn_get_product_filters([
        'get_variants' => true,
        'item_ids'     => $candidate_filter_ids,
        'status'       => ObjectStatuses::ACTIVE,
    ], 0, CART_LANGUAGE);

    $filters = [];

    foreach ($filter_configs as $filter_config) {
        $filter_id = (int) $filter_config['filter_id'];

        if ($filter_id <= 0 || empty($available_filters[$filter_id])) {
            continue;
        }

        $filter = $available_filters[$filter_id];
        $variants = fn_lr_design_changes_normalize_homepage_search_filter_variants(
            !empty($filter['variants']) && is_array($filter['variants']) ? $filter['variants'] : []
        );

        if (empty($variants)) {
            continue;
        }

        $filters[] = [
            'feature_id' => (int) $filter['feature_id'],
            'filter_id'  => (int) $filter['filter_id'],
            'key'        => (string) $filter_config['key'],
            'title'      => (string) $filter['filter'],
            'variants'   => $variants,
        ];
    }

    return $filters;
}

/**
 * Normalizes feature variants for the standalone homepage search filters template.
 *
 * @param array<int|string, array<string, mixed>> $variants Feature variants.
 *
 * @return array<int, array<string, int|string>>
 */
function fn_lr_design_changes_normalize_homepage_search_filter_variants(array $variants): array
{
    $normalized_variants = [];

    foreach ($variants as $variant) {
        $variant_id = (int) ($variant['variant_id'] ?? 0);
        $variant_name = trim((string) ($variant['variant'] ?? ''));

        if ($variant_id <= 0 || $variant_name === '') {
            continue;
        }

        $normalized_variants[] = [
            'variant_id' => $variant_id,
            'variant'    => $variant_name,
        ];
    }

    return $normalized_variants;
}
