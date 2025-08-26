<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2020   *
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
function fn_ab__search_motivation_update_category_post($category_data, $category_id, $lang_code)
{
if (isset($category_data['search_phrases']) && fn_check_view_permissions('ab__search_motivation.update', 'POST')) {
$company_id = fn_get_runtime_company_id();
fn_ab__search_motivation_update_phrases($category_data['search_phrases'], $company_id, $category_id, $lang_code);
}
}
function fn_ab__search_motivation_get_category_data_post($category_id, $field_list, $get_main_pair, $skip_company_condition, $lang_code, &$category_data)
{
$category_data['search_phrases'] = db_get_field('SELECT search_phrases FROM ?:ab__search_motivation WHERE category_id = ?i AND lang_code = ?s', $category_id, $lang_code);
}
function fn_ab__search_motivation_delete_category_after($category_id)
{
db_query('DELETE FROM ?:ab__search_motivation WHERE category_id = ?i', $category_id);
}
function fn_ab__search_motivation_get_phrases_by_cid($category_id, $lang_code = CART_LANGUAGE)
{
static $default_phrases_added = false;
static $init_cache = false;
$cache_name = 'ab__sm_categories';
$cache_key = $lang_code . '_' . $category_id;
if (!$init_cache) {
Registry::registerCache($cache_name, ['ab__search_motivation'], Registry::cacheLevel('static'), true);
$init_cache = true;
}
$search_phrases = Registry::get($cache_name . '.' . $cache_key);
if (empty($search_phrases)) {
$categories_data = db_get_array('SELECT c.parent_id, sm.search_phrases, c.category_id
FROM ?:categories AS c
LEFT JOIN ?:ab__search_motivation AS sm ON sm.category_id = c.category_id AND sm.lang_code = ?s
WHERE c.id_path LIKE "%?i%"
AND (sm.search_phrases != "NULL" OR c.level = (SELECT level FROM ?:categories WHERE category_id = ?i))
AND c.company_id = ?i
AND c.status = "A"', $lang_code, $category_id, $category_id, fn_get_runtime_company_id());
$count = count($categories_data);
if (!empty($categories_data[0]['search_phrases']) || $count > 1) {
$iteration = 1;
foreach ($categories_data as $category) {
$iteration++;
if (!empty($category['search_phrases'])) {
$search_phrases .= trim($category['search_phrases']) . PHP_EOL;
}
}
} elseif (!empty($categories_data[0]) && !empty($categories_data[0]['parent_id'])) {
$search_phrases = fn_ab__search_motivation_get_phrases_by_cid($categories_data[0]['parent_id']);
}
if (Registry::ifGet('addons.ab__search_motivation.add_default', 'N') == 'Y' && !empty($search_phrases) && !$default_phrases_added) {
$search_phrases .= PHP_EOL . fn_ab__search_motivation_get_default_phrases();
$default_phrases_added = true;
}
Registry::set($cache_name . '.' . $cache_key, $search_phrases);
}
return empty($search_phrases) ? fn_ab__search_motivation_get_default_phrases() : $search_phrases;
}
function fn_ab__search_motivation_update_phrases($search_phrases, $company_id = 0, $category_id = 0, $lang_code = CART_LANGUAGE)
{
db_query('REPLACE INTO ?:ab__search_motivation ?e', [
'category_id' => $category_id,
'company_id' => $company_id,
'lang_code' => $lang_code,
'search_phrases' => $search_phrases,
]);
}
function fn_ab__search_motivation_get_default_phrases($lang_code = CART_LANGUAGE)
{
static $init_cache = false;
$cache_name = 'ab__sm_default';
$cache_key = $lang_code;
if (!$init_cache) {
Registry::registerCache($cache_name, ['ab__search_motivation'], Registry::cacheLevel('static'), true);
$init_cache = true;
}
$search_phrases = Registry::get($cache_name . '.' . $cache_key);
if (empty($search_phrases)) {
$company_id = fn_get_runtime_company_id();
$search_phrases = db_get_field('SELECT search_phrases FROM ?:ab__search_motivation WHERE category_id = 0 AND company_id = ?i AND lang_code = ?s AND TRIM(search_phrases) > ""', $company_id, $lang_code);
Registry::set($cache_name . '.' . $cache_key, $search_phrases);
}
return $search_phrases;
}
function fn_ab__search_motivation_get_all_phrases($lang_code = CART_LANGUAGE)
{
static $init_cache = false;
$cache_name = 'ab__sm_all';
$cache_key = $lang_code;
if (!$init_cache) {
Registry::registerCache($cache_name, ['ab__search_motivation'], Registry::cacheLevel('static'), true);
$init_cache = true;
}
$search_phrases = Registry::get($cache_name . '.' . $cache_key);
if (empty($search_phrases)) {
$company_id = fn_get_runtime_company_id();
$search_phrases = db_get_fields('SELECT search_phrases FROM ?:ab__search_motivation WHERE company_id = ?i AND lang_code = ?s AND TRIM(search_phrases) > ""', $company_id, $lang_code);
Registry::set($cache_name . '.' . $cache_key, $search_phrases);
}
return empty($search_phrases) ? '' : implode(PHP_EOL, $search_phrases);
}
function fn_ab__search_motivation_get_phrases()
{
static $phrases = [];
$controller = Registry::get('runtime.controller');
$mode = Registry::get('runtime.mode');
$controller_mode = "{$controller}.{$mode}";
if (empty($phrases[$controller_mode])) {
if ($controller == 'categories' && $mode == 'view' && Registry::get('addons.ab__search_motivation.show_on_category_page') == 'Y') {
$category = Tygh::$app['view']->getTemplateVars('category_data');
$search_phrases = fn_ab__search_motivation_get_phrases_by_cid($category['category_id']);
} elseif ($controller == 'products' && $mode == 'view' && Registry::get('addons.ab__search_motivation.show_on_product_page') == 'Y') {
$product = Tygh::$app['view']->getTemplateVars('product');
$search_phrases = fn_ab__search_motivation_get_phrases_by_cid($product['main_category']);
} elseif (($controller == 'index' && $mode == 'index' && Registry::get('addons.ab__search_motivation.show_on_homepage') == 'Y') ||
($controller == 'checkout' && $mode == 'cart' && Registry::get('addons.ab__search_motivation.show_on_cart_page') == 'Y') ||
($controller == 'checkout' && $mode == 'checkout' && Registry::get('addons.ab__search_motivation.show_on_checkout_page') == 'Y')) {
$search_phrases = fn_ab__search_motivation_get_all_phrases();
} elseif (!in_array($controller_mode, fn_ab__sm_get_configurable_dispatches())) {
$search_phrases = fn_ab__search_motivation_get_default_phrases();
}
if (empty($search_phrases)) {
$phrases[$controller_mode] = [];
} else {
$search_phrases = explode(PHP_EOL, $search_phrases);
if (Registry::get('addons.ab__search_motivation.shuffle') == 'Y') {
shuffle($search_phrases);
}
$phrases[$controller_mode] = array_slice($search_phrases, 0, 10);
}
}
return $phrases[$controller_mode];
}

function fn_ab__sm_get_configurable_dispatches()
{
return [
'index.index',
'categories.view',
'products.view',
'checkout.cart',
'checkout.checkout'
];
}
