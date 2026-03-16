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
 *
 * Response payload:
 * - items: list of subcategory links
 * - has_more: whether the full subcategory list exceeds the configured limit
 * - search_url: fallback link for the "view all" button
 *
 * @var string $mode
 */
if ($mode === 'get_products') {
    $items = [];
    $features_hash = isset($_REQUEST['features_hash']) ? (string) $_REQUEST['features_hash'] : '';
    $category_names = isset($_REQUEST['category_names']) ? (array) $_REQUEST['category_names'] : [];
    $category_filter_id = isset($_REQUEST['category_filter_id']) ? (int) $_REQUEST['category_filter_id'] : 0;
    $products_dropdown_limit = (int) Registry::get('addons.sd_home_filters.products_dropdown_limit');
    $products_dropdown_limit = $products_dropdown_limit > 0 ? $products_dropdown_limit : 10;
    $has_more_items = false;
    $search_url = '';
    $category_names = array_values(array_unique(array_filter(array_map('trim', $category_names))));

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

            $subcategories = array_values(array_reduce($subcategories, function (array $result, array $subcategory) {
                if (empty($subcategory['category_id'])) {
                    return $result;
                }

                $result[(int) $subcategory['category_id']] = $subcategory;

                return $result;
            }, []));

            $has_more_items = count($subcategories) > $products_dropdown_limit;
            $subcategories = array_slice($subcategories, 0, $products_dropdown_limit);

            foreach ($subcategories as $subcategory) {
                if (empty($subcategory['category_id']) || empty($subcategory['category'])) {
                    continue;
                }

                $item_url = 'products.search?cid=' . (int) $subcategory['category_id'] . '&search_performed=Y';

                if ($remaining_features_hash !== '') {
                    $item_url .= '&features_hash=' . urlencode($remaining_features_hash);
                }

                $items[] = [
                    'item_id'   => (int) $subcategory['category_id'],
                    'item'      => (string) $subcategory['category'],
                    'item_url'  => fn_url($item_url),
                ];
            }

            if (count($category_names) === 1) {
                $search_url = 'products.search?search_performed=Y';

                if (!empty($parent_category_ids[0])) {
                    $search_url .= '&cid=' . (int) $parent_category_ids[0];
                }

                if ($remaining_features_hash !== '') {
                    $search_url .= '&features_hash=' . urlencode($remaining_features_hash);
                }

                $search_url = fn_url($search_url);
            }
        }
    }
    Tygh::$app['ajax']->assign('sd_home_filters_products', [
        'items'      => $items,
        'has_more'   => $has_more_items,
        'search_url' => $search_url,
    ]);

    // CONTROLLER_STATUS_NO_CONTENT is provided by the CS-Cart controller bootstrap.
    /** @phpstan-ignore constant.notFound */
    return [CONTROLLER_STATUS_NO_CONTENT];
}
