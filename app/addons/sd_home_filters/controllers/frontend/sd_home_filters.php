<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Registry;
use Tygh\Tygh;

if (!defined('BOOTSTRAP')) {
    /** @phpstan-ignore constant.notFound */
    return [CONTROLLER_STATUS_DENIED];
}

/**
 * Handles AJAX loading of category-based dropdown items for the home filters block.
 *
 * Expected request params:
 * - features_hash: currently selected filters hash
 * - category_names: selected parent category names from the category filter UI
 * - category_filter_id: filter identifier that should be removed from the final search hash
 * - selected_category_ids: selected subcategory identifiers for the final search URL
 * - search_params: active search form params that should be preserved in the result URL
 *
 * Response payload:
 * - items: list of subcategories for the custom dropdown
 * - search_url: final search URL for the selected subcategories
 *
 * @var string $mode
 */
if ($mode === 'get_products') {
    $items = [];
    $features_hash = isset($_REQUEST['features_hash']) ? (string) $_REQUEST['features_hash'] : '';
    $category_names = isset($_REQUEST['category_names']) ? (array) $_REQUEST['category_names'] : [];
    $category_filter_id = isset($_REQUEST['category_filter_id']) ? (int) $_REQUEST['category_filter_id'] : 0;
    $selected_category_ids = isset($_REQUEST['selected_category_ids'])
        ? (array) $_REQUEST['selected_category_ids']
        : [];
    $search_params = isset($_REQUEST['search_params']) ? (array) $_REQUEST['search_params'] : [];
    $products_dropdown_limit = (int) Registry::get('addons.sd_home_filters.products_dropdown_limit');
    $products_dropdown_limit = $products_dropdown_limit > 0 ? $products_dropdown_limit : 10;
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
        // CART_LANGUAGE is a CS-Cart bootstrap constant available in controller runtime.
        /** @phpstan-ignore constant.notFound */
        $lang_code = CART_LANGUAGE;
        $selected_filters = fn_parse_filters_hash($features_hash);

        // The real target category is passed via cid/category_id, so the category filter
        // must be removed from the remaining search hash to avoid conflicting conditions.
        unset($selected_filters[$category_filter_id]);

        $remaining_features_hash = $selected_filters
            ? fn_generate_filter_hash($selected_filters)
            : '';

        $parent_category_ids = db_get_fields(
            'SELECT categories.category_id'
            . ' FROM ?:categories AS categories'
            . ' INNER JOIN ?:category_descriptions AS descriptions'
            . ' ON descriptions.category_id = categories.category_id'
            . ' WHERE descriptions.category IN (?a)'
            . ' AND descriptions.lang_code = ?s'
            . ' AND categories.status = ?s',
            $category_names,
            $lang_code,
            'A'
        );

        if ($parent_category_ids) {
            $subcategories = db_get_array(
                'SELECT categories.category_id, descriptions.category'
                . ' FROM ?:categories AS categories'
                . ' INNER JOIN ?:category_descriptions AS descriptions'
                . ' ON descriptions.category_id = categories.category_id'
                . ' WHERE categories.parent_id IN (?n)'
                . ' AND descriptions.lang_code = ?s'
                . ' AND categories.status = ?s'
                . ' ORDER BY categories.position ASC, descriptions.category ASC',
                $parent_category_ids,
                $lang_code,
                'A'
            );

            $unique_subcategories = [];

            foreach ($subcategories as $subcategory) {
                if (empty($subcategory['category_id'])) {
                    continue;
                }

                $unique_subcategories[(int) $subcategory['category_id']] = $subcategory;
            }

            $subcategories = array_values($unique_subcategories);
            $subcategories = array_slice($subcategories, 0, $products_dropdown_limit);

            foreach ($subcategories as $subcategory) {
                if (empty($subcategory['category_id']) || empty($subcategory['category'])) {
                    continue;
                }

                $items[] = [
                    'item_id'   => (int) $subcategory['category_id'],
                    'item'      => (string) $subcategory['category'],
                ];
            }

            if ($selected_category_ids) {
                $search_url = 'products.search?search_performed=Y&cid=' . implode(',', $selected_category_ids);
            } elseif (count($category_names) === 1 && !empty($parent_category_ids[0])) {
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
    }
    Tygh::$app['ajax']->assign('sd_home_filters_products', [
        'items'      => $items,
        'search_url' => $search_url,
    ]);

    // CONTROLLER_STATUS_NO_CONTENT is provided by the CS-Cart controller bootstrap.
    /** @phpstan-ignore constant.notFound */
    return [CONTROLLER_STATUS_NO_CONTENT];
}
