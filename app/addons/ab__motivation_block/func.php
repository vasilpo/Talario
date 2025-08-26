<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2025   *
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
use Tygh\Languages\Languages;
use Tygh\Navigation\LastView;
use Tygh\Enum\SiteArea;
use Tygh\Enum\YesNo;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\ImagePairTypes;
use Tygh\Enum\Addons\Ab_motivationBlock\ObjectTypes as Ab_objectTypes;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
define('AB__MB_DATA_PATH', Registry::get('config.dir.var') . 'ab__data/ab__motivation_block/');
foreach (glob(Registry::get('config.dir.addons') . 'ab__motivation_block/functions/ab__mb.*.php') as $functions) {
require_once $functions;
}

function fn_ab__mb_install()
{
$objects = [
['t' => '?:ab__mb_motivation_items',
'i' => [
['n' => 'exclude_categories', 'p' => 'char(1) NOT NULL default \'N\''],
['n' => 'exclude_destinations', 'p' => 'char(1) NOT NULL default \'N\''],
['n' => 'template_path', 'p' => 'text CHARACTER SET utf8'],
['n' => 'template_settings', 'p' => 'text CHARACTER SET utf8 NOT NULL'],
['n' => 'usergroup_ids', 'p' => 'varchar(255) NOT NULL DEFAULT \'0\''],
],
],
];
if (!empty($objects) && is_array($objects)) {
foreach ($objects as $o) {
$fields = db_get_fields('DESCRIBE ' . $o['t']);
if (!empty($fields) && is_array($fields)) {
if (!empty($o['i']) && is_array($o['i'])) {
foreach ($o['i'] as $f) {
if (!in_array($f['n'], $fields)) {
db_query('ALTER TABLE ?p ADD ?p ?p', $o['t'], $f['n'], $f['p']);
if (!empty($f['add_sql']) && is_array($f['add_sql'])) {
foreach ($f['add_sql'] as $sql) {
db_query($sql);
}
}
}
}
}
}
}
}
fn_ab__mb_migration_from_v220_to_v230();
fn_ab__mb_migration_from_v230_to_v240();
fn_ab__mb_migration_from_v240_to_v250();
fn_ab__mb_migration_from_v271_to_v280();
fn_ab__mb_migration_from_v290_to_v2100();
fn_ab__mb_migration_from_v2110_to_v2111();
fn_ab__mb_migration_from_v2122_to_v2130();
fn_ab__mb_refresh_icons();
}

function fn_ab__mb_update_motivation_item($motivation_item_data, $motivation_item_id, $lang_code = DESCR_SL, $storefront_id = 0)
{
$exists = db_get_field('SELECT motivation_item_id FROM ?:ab__mb_motivation_items WHERE motivation_item_id = ?i', $motivation_item_id);

fn_set_hook('ab__mb_update_motivation_item_pre', $motivation_item_data, $motivation_item_id, $lang_code, $storefront_id, $exists);
if (!empty($motivation_item_data['template_settings'])) {
$motivation_item_data['template_settings'] = serialize($motivation_item_data['template_settings']);
}
if (!empty($motivation_item_data['usergroup_ids'])) {
$motivation_item_data['usergroup_ids'] = implode(',', $motivation_item_data['usergroup_ids']);
}
if (empty($exists)) {
$motivation_item_data['storefront_id'] = $storefront_id;
$motivation_item_data['motivation_item_id'] = $motivation_item_id = db_query('INSERT INTO ?:ab__mb_motivation_items ?e', $motivation_item_data);
foreach (Languages::getAll() as $motivation_item_data['lang_code'] => $v) {
db_query('INSERT INTO ?:ab__mb_motivation_item_descriptions ?e', $motivation_item_data);
}
} else {
db_query('UPDATE ?:ab__mb_motivation_items SET ?u WHERE motivation_item_id = ?i AND storefront_id = ?i', $motivation_item_data, $motivation_item_id, $storefront_id);
db_query('UPDATE ?:ab__mb_motivation_item_descriptions SET ?u WHERE motivation_item_id = ?i AND lang_code = ?s', $motivation_item_data, $motivation_item_id, $lang_code);
db_query('DELETE FROM ?:ab__mb_motivation_item_objects WHERE motivation_item_id = ?i', $motivation_item_id);
}
$categories_arr = ab__mb_update_prepare_objects_array($motivation_item_id, $motivation_item_data, Ab_objectTypes::CATEGORY);
$destinations_arr = ab__mb_update_prepare_objects_array($motivation_item_id, $motivation_item_data, Ab_objectTypes::DESTINATION);
$products_arr = ab__mb_update_prepare_objects_array($motivation_item_id, $motivation_item_data, Ab_objectTypes::PRODUCT);
db_query('INSERT INTO ?:ab__mb_motivation_item_objects ?m', array_merge($categories_arr, $destinations_arr, $products_arr));
fn_attach_image_pairs('ab__mb_img', 'motivation_item', $motivation_item_id);
return $motivation_item_id;
}

function ab__mb_update_prepare_objects_array($motivation_item_id, $motivation_item_data, $object_type = Ab_objectTypes::CATEGORY)
{
$return_arr = [];
$_tmp = 'categories';
if ($object_type == Ab_objectTypes::DESTINATION) {
$_tmp = 'destinations';
} elseif ($object_type == Ab_objectTypes::PRODUCT) {
$_tmp = 'product';
if (!empty($motivation_item_data['product_ids'])) {
$motivation_item_data['product_ids'] = array_keys($motivation_item_data['product_ids']);
}
}
if (!empty($motivation_item_data["{$_tmp}_ids"])) {
$ids = is_array($motivation_item_data["{$_tmp}_ids"]) ? $motivation_item_data["{$_tmp}_ids"] : explode(',', $motivation_item_data["{$_tmp}_ids"]);
foreach ($ids as $obj_id) {
$return_arr[] = [
'motivation_item_id' => $motivation_item_id,
'object_id' => $obj_id,
'object_type' => $object_type,
];
}
} else {
$return_arr[] = [
'motivation_item_id' => $motivation_item_id,
'object_id' => 0,
'object_type' => $object_type,
];
}
return $return_arr;
}

function fn_ab__mb_delete_motivation_item($motivation_item_id)
{
if (!empty($motivation_item_id)) {
db_query('DELETE FROM ?:ab__mb_motivation_items WHERE motivation_item_id = ?i', $motivation_item_id);
db_query('DELETE FROM ?:ab__mb_motivation_item_descriptions WHERE motivation_item_id = ?i', $motivation_item_id);
db_query('DELETE FROM ?:ab__mb_motivation_item_objects WHERE motivation_item_id = ?i', $motivation_item_id);
if (fn_allowed_for('MULTIVENDOR')) {
db_query('DELETE FROM ?:ab__mb_vendors_descriptions WHERE motivation_item_id = ?i', $motivation_item_id);
}
return true;
}
return false;
}

function fn_ab__mb_get_motivation_items($params = [], $items_per_page = 0, $area = AREA, $lang_code = CART_LANGUAGE)
{
$params = LastView::instance()->update('ab__motivation_items', $params);
$default_params = [
'page' => 1,
'items_per_page' => $items_per_page,
'area' => $area,
'sort_by' => 'position',
'sort_order' => 'asc',
'auth' => \Tygh::$app['session']['auth'],
];
$params = array_merge($default_params, $params);
$sortings = [
'name' => '?:ab__mb_motivation_item_descriptions.name',
'status' => '?:ab__mb_motivation_items.status',
'position' => '?:ab__mb_motivation_items.position',
];
$sorting = db_sort($params, $sortings, 'position', 'asc');
$fields = [
'?:ab__mb_motivation_items.*',
'?:ab__mb_motivation_item_descriptions.name',
'?:ab__mb_motivation_item_descriptions.description',
'?:ab__mb_motivation_item_descriptions.lang_code',
];
$group_by = [
'?:ab__mb_motivation_items.motivation_item_id',
];
$condition = $limit = $join = '';
if ($params['area'] == SiteArea::STOREFRONT) {
$condition .= db_quote(' AND ?:ab__mb_motivation_items.status = ?s', ObjectStatuses::ACTIVE);
$params['destination_id'] = Tygh::$app['location']->getDestinationId();
$templates_schema = fn_get_schema('ab__mb', 'item_templates');
foreach ($templates_schema as $template) {
if (!empty($template['subitems'])) {
$templates_schema = array_merge($templates_schema, $template['subitems']);
}
}
$auth = $params['auth'];
if (!empty($auth['usergroup_ids'])) {
$condition .= ' AND (' . fn_find_array_in_set($auth['usergroup_ids'], '?:ab__mb_motivation_items.usergroup_ids', true) . ')';
}
}
if (!empty($params['item_ids'])) {
$condition .= db_quote(' AND ?:ab__mb_motivation_items.motivation_item_id IN (?n)', explode(',', $params['item_ids']));
}
if (!empty($params['vendor_edit'])) {
$condition .= db_quote(' AND ?:ab__mb_motivation_items.vendor_edit = ?s', YesNo::YES);
}
if (!empty($params['destination_id'])) {
$join .= db_quote('LEFT JOIN ?:ab__mb_motivation_item_objects as destination_objects_not_exclude
ON ?:ab__mb_motivation_items.motivation_item_id = destination_objects_not_exclude.motivation_item_id
AND destination_objects_not_exclude.object_type = ?s
AND ?:ab__mb_motivation_items.exclude_destinations = "N"', Ab_objectTypes::DESTINATION, [0, $params['destination_id']]);
$join .= db_quote('LEFT JOIN ?:ab__mb_motivation_item_objects as destination_objects_exclude
ON ?:ab__mb_motivation_items.motivation_item_id = destination_objects_exclude.motivation_item_id
AND destination_objects_exclude.object_type = ?s
AND destination_objects_exclude.object_id IN (?n)
AND ?:ab__mb_motivation_items.exclude_destinations = "Y"', Ab_objectTypes::DESTINATION, [$params['destination_id']]);
$condition .= db_quote(' AND ((destination_objects_not_exclude.object_id IN (?n) OR destination_objects_not_exclude.object_id IS NULL) AND (destination_objects_exclude.motivation_item_id IS NULL))', [0, $params['destination_id']]);
}
if (!empty($params['category_ids'])) {
$join .= db_quote('LEFT JOIN ?:ab__mb_motivation_item_objects as category_objects_not_exclude
ON ?:ab__mb_motivation_items.motivation_item_id = category_objects_not_exclude.motivation_item_id
AND category_objects_not_exclude.object_type = ?s
AND ?:ab__mb_motivation_items.exclude_categories = "N"', Ab_objectTypes::CATEGORY);
$join .= db_quote('LEFT JOIN ?:ab__mb_motivation_item_objects as category_objects_exclude
ON ?:ab__mb_motivation_items.motivation_item_id = category_objects_exclude.motivation_item_id
AND category_objects_exclude.object_type = ?s
AND category_objects_exclude.object_id IN (?n)
AND ?:ab__mb_motivation_items.exclude_categories = "Y"', Ab_objectTypes::CATEGORY, $params['category_ids']);
$condition .= db_quote(' AND ((category_objects_not_exclude.object_id IN (?n) OR category_objects_not_exclude.object_id IS NULL) AND (category_objects_exclude.motivation_item_id IS NULL))', array_merge([0], $params['category_ids']));
}
if (!empty($params['product_id'])) {
$join .= db_quote('LEFT JOIN ?:ab__mb_motivation_item_objects as product_objects_not_exclude
ON ?:ab__mb_motivation_items.motivation_item_id = product_objects_not_exclude.motivation_item_id
AND product_objects_not_exclude.object_type = ?s
AND ?:ab__mb_motivation_items.exclude_products = "N"', Ab_objectTypes::PRODUCT);
$join .= db_quote('LEFT JOIN ?:ab__mb_motivation_item_objects as product_objects_exclude
ON ?:ab__mb_motivation_items.motivation_item_id = product_objects_exclude.motivation_item_id
AND product_objects_exclude.object_type = ?s
AND product_objects_exclude.object_id IN (?n)
AND ?:ab__mb_motivation_items.exclude_products = "Y"', Ab_objectTypes::PRODUCT, $params['product_id']);
$condition .= db_quote(' AND ((product_objects_not_exclude.object_id IN (?n) OR product_objects_not_exclude.object_id IS NULL) AND (product_objects_exclude.motivation_item_id IS NULL))', [0, $params['product_id']]);
}
if (isset($params['ab__mb_template']) && $params['ab__mb_template'] != 'ignore') {
$condition .= db_quote('AND ?:ab__mb_motivation_items.template_path = ?s', $params['ab__mb_template']);
if ($params['ab__mb_template'] == '') {
$condition .= 'OR ?:ab__mb_motivation_items.template_path IS NULL';
}
}
if (isset($params['name']) && fn_string_not_empty($params['name'])) {
$condition .= db_quote(' AND ?:ab__mb_motivation_item_descriptions.name LIKE ?l', '%' . trim($params['name']) . '%');
}
if (!empty($params['storefront_id'])) {
$condition .= db_quote('AND ?:ab__mb_motivation_items.storefront_id = ?i', $params['storefront_id']);
} elseif (fn_allowed_for('ULTIMATE')) {
$condition .= db_quote('AND ?:ab__mb_motivation_items.storefront_id = ?i', Tygh::$app['storefront']->storefront_id);
}
if (!empty($params['company_id']) && fn_allowed_for('MULTIVENDOR')) {
$fields[] = 'vd.description AS vendor_description';
$fields[] = 'vd.status as vendor_status';
$join .= db_quote(' LEFT JOIN ?:ab__mb_vendors_descriptions AS vd ON vd.motivation_item_id = ?:ab__mb_motivation_items.motivation_item_id AND vd.company_id = ?i AND vd.lang_code = ?s', $params['company_id'], $lang_code);
}
$join .= db_quote(' INNER JOIN ?:ab__mb_motivation_item_descriptions
ON ?:ab__mb_motivation_item_descriptions.motivation_item_id = ?:ab__mb_motivation_items.motivation_item_id
AND ?:ab__mb_motivation_item_descriptions.lang_code = ?s', $lang_code);
if (!empty($params['items_per_page'])) {
$params['total_items'] = db_get_field('SELECT COUNT(DISTINCT(?:ab__mb_motivation_items.motivation_item_id)) FROM ?:ab__mb_motivation_items ?p WHERE 1 ?p', $join, $condition);
$limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
}

fn_set_hook('ab__mb_get_motivation_items_before_select', $params, $fields, $join, $condition, $group_by, $lang_code);
$motivation_items = db_get_hash_array('SELECT ?p FROM ?:ab__mb_motivation_items ?p WHERE 1 ?p GROUP BY ?p ?p ?p', 'motivation_item_id', implode(', ', $fields), $join, $condition, implode(', ', $group_by), $sorting, $limit);
if (empty($motivation_items)) {
return [[], $params];
}
$item_ids = array_keys($motivation_items);
$image_pairs = fn_get_image_pairs($item_ids, 'motivation_item', ImagePairTypes::MAIN);
foreach ($image_pairs as $key => $image) {
$motivation_items[$key]['main_pair'] = reset($image);
}
$motivation_items_objects = fn_ab__mb_get_motivation_items_objects($item_ids, Ab_objectTypes::getAll());
foreach ($motivation_items as $item_id => &$item) {
if (fn_allowed_for('MULTIVENDOR')) {
if (!empty($item['vendor_description'])) {
if ($params['area'] == SiteArea::STOREFRONT && $item['vendor_status'] == ObjectStatuses::DISABLED) {
unset($motivation_items[$item_id]);
}
if ($item['vendor_edit'] == YesNo::YES) {
if (fn_ab__mb_prepare_description_to_compare($item['description']) != fn_ab__mb_prepare_description_to_compare($item['vendor_description'])) {
$item['is_vendor_description'] = true;
}
$item['description'] = $item['vendor_description'];
}
}
}
if ($params['area'] == SiteArea::STOREFRONT) {
if (!empty($item['template_settings'])) {
$item['template_settings'] = unserialize($item['template_settings']);
}
} else {
foreach (Ab_objectTypes::getAll() as $type) {
$_tmp = 'categories';
if ($type == Ab_objectTypes::DESTINATION) {
$_tmp = 'destinations';
} elseif ($type == Ab_objectTypes::PRODUCT) {
$_tmp = 'products';
}
if (!empty($motivation_items_objects[$item_id][$type]['object_ids'])) {
$item["{$_tmp}_ids"] = $motivation_items_objects[$item_id][$type]['object_ids'];
}
}
if (!empty($item['categories_ids'])) {
$c_ids = fn_explode(',', $item['categories_ids']);
$tr_c_ids = array_slice($c_ids, 0, 7);
$c_names = fn_get_category_name($tr_c_ids, $lang_code);
if (sizeof($tr_c_ids) < sizeof($c_ids)) {
$c_names[] = '... (' . sizeof($c_ids) . ')';
}
$item['categories'] = implode(', ', $c_names);
}
if (!empty($item['destinations_ids'])) {
$destinations = fn_ab__mb_get_destinations_list($item['destinations_ids']);
$item['destinations'] = implode(', ', $destinations);
}
if (!empty($item['products_ids'])) {
$item['products_ids'] = explode(',', $item['products_ids']);
}
}
}
return [$motivation_items, $params];
}

function fn_ab__mb_has_template($items = [], $template = '')
{
$res = false;
if (is_array($items)) {
foreach ($items as $item) {
if ($item['template_path'] === $template) {
$res = true;
break;
}
}
}
return $res;
}

function fn_ab__mb_get_motivation_item_data($motivation_item_id, $lang_code = DESCR_SL)
{
$fields = [
'?:ab__mb_motivation_items.*',
'?:ab__mb_motivation_item_descriptions.name',
'?:ab__mb_motivation_item_descriptions.description',
];
$join = db_quote('LEFT JOIN ?:ab__mb_motivation_item_descriptions
ON ?:ab__mb_motivation_item_descriptions.motivation_item_id = ?:ab__mb_motivation_items.motivation_item_id
AND ?:ab__mb_motivation_item_descriptions.lang_code = ?s', $lang_code);
$condition = db_quote('?:ab__mb_motivation_items.motivation_item_id = ?i', $motivation_item_id);

fn_set_hook('ab__mb_get_motivation_item_data_before_select', $fields, $join, $condition, $motivation_item_id, $lang_code);
$motivation_item = db_get_row('SELECT ?p FROM ?:ab__mb_motivation_items ?p WHERE ?p', implode(',', $fields), $join, $condition);
if (empty($motivation_item)) {
return [];
}
$objects = fn_ab__mb_get_motivation_items_objects([$motivation_item_id], Ab_objectTypes::getAll())[$motivation_item_id];
if (!empty($objects[Ab_objectTypes::DESTINATION])) {
$motivation_item['destinations_ids'] = $objects[Ab_objectTypes::DESTINATION]['object_ids'];
}
if (!empty($objects[Ab_objectTypes::CATEGORY])) {
$motivation_item['categories_ids'] = $objects[Ab_objectTypes::CATEGORY]['object_ids'];
}
if (!empty($objects[Ab_objectTypes::PRODUCT])) {
$motivation_item['product_ids'] = $objects[Ab_objectTypes::PRODUCT]['object_ids'];
}
$motivation_item['main_pair'] = fn_get_image_pairs($motivation_item['motivation_item_id'], 'motivation_item', ImagePairTypes::MAIN);
if (!empty($motivation_item['template_settings'])) {
$motivation_item['template_settings'] = unserialize($motivation_item['template_settings']);
}
return (array) $motivation_item;
}

function fn_ab__mb_get_motivation_items_objects($motivation_item_ids, $types)
{
$fields = [
'?:ab__mb_motivation_item_objects.motivation_item_id',
'?:ab__mb_motivation_item_objects.object_type',
'GROUP_CONCAT(?:ab__mb_motivation_item_objects.object_id) as object_ids',
];
$join = '';
$condition = db_quote('?:ab__mb_motivation_item_objects.motivation_item_id IN (?n) AND ?:ab__mb_motivation_item_objects.object_type IN (?a)', $motivation_item_ids, $types);
$group_by_fields = [
'?:ab__mb_motivation_item_objects.motivation_item_id',
'?:ab__mb_motivation_item_objects.object_type',
];
$group_by = db_quote('GROUP BY ?p', implode(', ', $group_by_fields));
return db_get_hash_multi_array('SELECT ?p FROM ?:ab__mb_motivation_item_objects ?p WHERE 1 AND ?p ?p', ['motivation_item_id', 'object_type'], implode(', ', $fields), $join, $condition, $group_by);
}

function fn_ab__mb_update_by_vendor($motivation_item_data, $motivation_item_id, $lang_code, $company_id)
{
if (empty($motivation_item_data) || empty($motivation_item_data['description'])) {
return false;
}
db_replace_into('ab__mb_vendors_descriptions', [
'motivation_item_id' => $motivation_item_id,
'company_id' => $company_id,
'lang_code' => $lang_code,
'description' => $motivation_item_data['description'],
'status' => $motivation_item_data['status'],
]);
}

function fn_ab__motivation_block_install_blocks($status = ObjectStatuses::ACTIVE)
{
$path = AB__MB_DATA_PATH . 'blocks/';
$storefront = Tygh::$app['storefront'];
$theme_name = $storefront->theme_name;
if (!is_file($path . $theme_name . '.json')) {
$theme_name = 'responsive';
}
$data = json_decode(fn_get_contents($path . $theme_name . '.json'), true);
if (!empty($data)) {
$notifications = [];
$langs = Languages::getSimpleLanguages();
foreach ($data as $block) {
$block['status'] = !empty($block['status']) ? $block['status'] : $status;
if (!empty($block['template_path'])) {
if (strpos($block['template_path'], 'product_categories_list.tpl') !== false) {
$block['template_settings'] = [
'brand_feature_id' => fn_ab__mb_get_default_brand_setting_id(),
];
}
}
$block_id = fn_ab__mb_update_motivation_item($block, 0, CART_LANGUAGE, $storefront->storefront_id);
foreach (array_keys($langs) as $lang){
if (!empty($block[$lang])){
fn_ab__mb_update_motivation_item($block[$lang], $block_id, $lang, $storefront->storefront_id);
}
}
$notifications[] = '<a href="' . fn_url('ab__motivation_block.update&motivation_item_id=' . $block_id) . '">' . (CART_LANGUAGE == 'ru' ? $block['ru']['name'] : $block['name']) . '</a>';
}
fn_set_notification(NotificationSeverity::NOTICE, __('notice'), __('ab__mb.demodata.successes.blocks', ['[blocks]' => implode(', ', $notifications)]));
return $notifications;
}
fn_set_notification(NotificationSeverity::ERROR, __('error'), __('ab__mb.demodata.errors.no_data'));
return false;
}
function fn_ab__motivation_block_prepare_block_to_cloning($block = [])
{
unset($block['motivation_item_id']);
$block['status'] = ObjectStatuses::DISABLED;
$block['name'] .= ' [CLONE]';
return $block;
}

function fn_ab__mb_clone_element($item_id)
{
$old_data = fn_ab__mb_get_motivation_item_data($item_id);
if (empty($old_data)) {
return false;
}
$storefront_id = Tygh::$app['storefront']->storefront_id;
$old_data = fn_ab__motivation_block_prepare_block_to_cloning($old_data);
$new_id = fn_ab__mb_update_motivation_item($old_data, 0, CART_LANGUAGE, $storefront_id);
foreach (array_keys(Languages::getAll()) as $lang_code) {
if ($lang_code != CART_LANGUAGE) {
$temp = fn_ab__motivation_block_prepare_block_to_cloning(fn_ab__mb_get_motivation_item_data($item_id, $lang_code));
fn_ab__mb_update_motivation_item($temp, $new_id, $lang_code, $storefront_id);
}
}
return $new_id;
}

function fn_ab__mb_change_element_status($elements, $status = ObjectStatuses::DISABLED)
{
db_query('UPDATE ?:ab__mb_motivation_items SET status = ?s WHERE motivation_item_id in (?n)', $status, (array) $elements);
}

function fn_ab__mb_get_destinations_list($destinations_ids, $lang_code = CART_LANGUAGE)
{
static $max_destinations = 7;
$d_names = [];
if (!empty($destinations_ids)) {
$d_ids = fn_explode(',', $destinations_ids);
$tr_d_ids = array_slice($d_ids, 0, $max_destinations);
foreach ($tr_d_ids as $tr_d_id) {
$d_names[] = fn_get_destination_name($tr_d_id, $lang_code);
}
if (sizeof($tr_d_ids) < sizeof($d_ids)) {
$d_names[] = '... (' . sizeof($d_ids) . ')';
}
} else {
$d_names[] = __('ab__mb_all_destinations');
}
return $d_names;
}

function fn_ab__mb_get_templates_array()
{
$templates_schema = fn_get_schema('ab__mb', 'item_templates');
$templates_arr = [];
foreach ($templates_schema as $path => $template) {
if (!empty($template['subitems'])) {
foreach ($template['subitems'] as $sub_path => $sub_template) {
$templates_arr[$path]['subitems'][$sub_path] = fn_ab__mb_form_template_for_admin($sub_path, $sub_template);
}
break;
}
$templates_arr[$path] = fn_ab__mb_form_template_for_admin($path, $template);
}
return $templates_arr;
}

function fn_ab__mb_form_template_for_admin($path, $template)
{
$disabled = false;
if (!empty($template['condition_func']) && function_exists($template['condition_func'][0]) && call_user_func($template['condition_func'][0]) === false) {
$disabled = true;
}
return [
'template_path' => $path,
'template_name' => $template['name'],
'tooltip' => !empty($template['tooltip']) ? $template['tooltip'] : '',
'disabled' => $disabled,
'settings' => isset($template['settings']) ? $template['settings'] : false,
];
}

function fn_ab__mb_prepare_description_to_compare($description)
{
$description = htmlspecialchars_decode(nl2br($description));
$description = preg_replace('/<br(\s+)?\/?>/i', "", $description);
$description = preg_replace('/\s\s+/', "", $description);
$description = trim(html_entity_decode(strip_tags($description)), " \n\r\t\v\x00");
return $description;
}