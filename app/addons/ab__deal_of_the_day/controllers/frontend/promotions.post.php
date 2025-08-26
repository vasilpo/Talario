<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2024   *
* / /_\ | | _____  _| |_/ /_ __ __ _ _ __   __| |_ _ __   __ _   | |_ ___  __ _ _ __ ___   *
* |  _  | |/ _ \ \/ / ___ \ '__/ _` | '_ \ / _` | | '_ \ / _` |  | __/ _ \/ _` | '_ ` _ \  *
* | | | | |  __/>  <| |_/ / | | (_| | | | | (_| | | | | | (_| |  | ||  __/ (_| | | | | | | *
* \_| |_/_|\___/_/\_\____/|_|  \__,_|_| |_|\__,_|_|_| |_|\__, |  \___\___|\__,_|_| |_| |_| *
*                                                         __/ |                            *
*                                                        |___/                             *
* ---------------------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license and accept    *
* to the terms of the License Agreement can install and use this program.                  *
* ---------------------------------------------------------------------------------------- *
* website: https://cs-cart.alexbranding.com                                                *
*   email: info@alexbranding.com                                                           *
*******************************************************************************************/
use Tygh\Registry;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}
if ($mode == 'list') {
list($chains, $chains_search) = fn_ab__dotd_get_chains();
Tygh::$app['view']->assign('chains', $chains);
Tygh::$app['view']->assign('show_chains', true);
Tygh::$app['view']->assign('chains_search', $chains_search);
} elseif ($mode == 'view') {
$promotion = fn_ab__dotd_get_cached_promotion_data($_REQUEST['promotion_id']);
if (empty($promotion['status']) || $promotion['status'] == 'D') {
return [CONTROLLER_STATUS_NO_PAGE];
}
$promotion['all_categories_icon'] = fn_get_image_pairs($promotion['promotion_id'], 'ab__dotd_m_cat_filter', 'M', true, false, DESCR_SL);
Tygh::$app['view']->assign('promotion', $promotion);
if (!empty($promotion['meta_description']) || !empty($promotion['meta_keywords'])) {
Tygh::$app['view']->assign('meta_description', $promotion['meta_description']);
Tygh::$app['view']->assign('meta_keywords', $promotion['meta_keywords']);
}
if (!empty($promotion['page_title'])) {
Tygh::$app['view']->assign('page_title', $promotion['page_title']);
}
fn_add_breadcrumb(__('promotions'), 'promotions.list');
fn_add_breadcrumb($promotion['name']);
if (empty($promotion['hide_products_block']) || $promotion['hide_products_block'] != 'Y') {
$selected_category_id = empty($_REQUEST['cid']) ? 0 : $_REQUEST['cid'];
Tygh::$app['view']->assign('selected_category_id', $selected_category_id);
list($where, $joins) = fn_ab__dotd_build_promotion_conditions_query($promotion['conditions']);

$params = $_REQUEST;
$params['extend'] = ['description'];
$params['ab__dotd_promotion_id'] = $promotion['promotion_id'];
$params['include_child_variations'] = true;
$params['group_child_variations'] = true;
if (!empty($selected_category_id)) {
$params['cid'] = $selected_category_id;
$params['subcats'] = 'Y';
}
if ($items_per_page = fn_change_session_param(Tygh::$app['session'], $_REQUEST, 'items_per_page')) {
$params['items_per_page'] = $items_per_page;
}
if ($sort_by = fn_change_session_param(Tygh::$app['session'], $_REQUEST, 'sort_by')) {
$params['sort_by'] = $sort_by;
}
if ($sort_order = fn_change_session_param(Tygh::$app['session'], $_REQUEST, 'sort_order')) {
$params['sort_order'] = $sort_order;
}
if (isset($promotion['group_by_category']) && $promotion['group_by_category'] === 'Y' && empty($selected_category_id)){
$selected_layout = 'products_multicolumns';
$columns = Registry::get('settings.Appearance.columns_in_products_list');
$products_per_group = $columns * 2 - 1;
list(,$category_groups) = fn_ab__dotd_get_categories($promotion['promotion_id']);
foreach ($category_groups as &$root_category) {
$params['cid'] = $root_category['category_id'];
$params['subcats'] = 'Y';
list($root_category['products']) = fn_get_products($params, $products_per_group);
if(!empty($root_category['products'])){
fn_gather_additional_products_data($root_category['products'], [
'get_icon' => true,
'get_detailed' => true,
'get_options' => true,
'get_discounts' => true,
'get_features' => false,
'get_additional' => true
]);
}
}
Tygh::$app['view']->assign('category_groups', $category_groups)
->assign('no_pagination', true)
->assign('show_empty', true)
->assign('no_sorting', true);
}else{
$selected_layout = fn_get_products_layout($_REQUEST);
list($products, $search) = fn_get_products($params, Registry::get('settings.Appearance.products_per_page'));
if (!empty($products)) {
fn_gather_additional_products_data($products, [
'get_icon' => true,
'get_detailed' => true,
'get_options' => true,
'get_discounts' => true,
'get_features' => false,
'get_additional' => true
]);
}
Tygh::$app['view']->assign('products', $products);
Tygh::$app['view']->assign('search', $search);
}
Tygh::$app['view']->assign('selected_layout', $selected_layout);
}
}
