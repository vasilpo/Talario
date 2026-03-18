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
use Tygh\BlockManager\Block;
use Tygh\BlockManager\RenderManager;
use Tygh\Enum\Addons\Ab_stickers\StickerPlaces;
use Tygh\Enum\Addons\Ab_stickers\StickerSizes;
use Tygh\Enum\Addons\Ab_stickers\StickerStyles;
use Tygh\Enum\Addons\Discussion\DiscussionObjectTypes;
use Tygh\Enum\ImagePairTypes;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\YesNo;
use Tygh\Languages\Languages;
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');
foreach (glob(Registry::get('config.dir.addons') . '/ab__stickers/functions/fn.*.php') as $functions) {
require_once $functions;
}

function fn_ab__stickers_get_enum_list($enum = 'StickerStyles', $function = 'getAll')
{
$enum = 'Tygh\Enum\Addons\Ab_stickers\\' . $enum;
if (class_exists($enum) && method_exists($enum, $function)) {
return $enum::$function();
}
throw new Exception('Method does not exist');
}

function fn_ab__stickers_update_sticker($sticker_id, $params, $lang_code = DESCR_SL, $is_demo = false)
{
$default_params = [];
$params = array_merge($default_params, $params);

$repository = Tygh::$app['addons.ab__stickers.repository'];
$sticker_data = $params['sticker_data'];
foreach (['name_for_admin', 'name_for_desktop', 'name_for_mobile'] as $clearing_field) {
if (isset($sticker_data[$clearing_field])) {
$sticker_data[$clearing_field] = strip_tags($sticker_data[$clearing_field], implode('', fn_ab__stickers_get_available_tags()));
}
}
foreach ($repository->getEncodedFieldsList(['conditions']) as $field) {
if (isset($sticker_data[$field])) {
$sticker_data[$field] = fn_ab__stickers_encode_data($sticker_data[$field]);
}
}
if (isset($sticker_data['conditions'])) {
fn_ab__stickers_check_group_conditions($sticker_data['conditions']);
$sticker_data['conditions'] = fn_ab__stickers_encode_data($sticker_data['conditions']);
}
$sticker_data['from_date'] = empty($sticker_data['from_date']) ? 0 : fn_parse_date($sticker_data['from_date']);
$sticker_data['to_date'] = empty($sticker_data['to_date']) ? 0 : fn_parse_date($sticker_data['to_date'], true);
if (isset($sticker_data['image_id'])) {
unset($sticker_data['image_id']);
}
if (empty($sticker_data['storefront_ids'])) {
if (empty(Tygh::$app['session']['auth']['company_id'])) {
$company_id = fn_get_runtime_company_id();
} else {
$company_id = Tygh::$app['session']['auth']['company_id'];
}
$storefronts = Tygh::$app['storefront.repository']->findByCompanyId($company_id, false);
if (empty($storefronts)) {
list($storefronts) = Tygh::$app['storefront.repository']->find();
}
$sticker_data['storefront_ids'] = implode(',', array_keys($storefronts));
}
$attach_image_name = 'ab__sticker_img';
$attach_image_type = 'ab__stickers';
if ($sticker_id == 0) {
$sticker_data['sticker_id'] = $sticker_id = db_query('INSERT INTO ?:ab__stickers ?e', $sticker_data);
$prev_image_id = 0;
foreach (Languages::getSimpleLanguages() as $sticker_data['lang_code'] => $v) {
db_query('INSERT INTO ?:ab__sticker_descriptions ?e', $sticker_data);
$sticker_image_id = db_query('INSERT INTO ?:ab__sticker_images ?e', [
'image_id' => '',
'sticker_id' => $sticker_id,
'lang_code' => $sticker_data['lang_code'],
]);
if ($prev_image_id == 0) {
fn_attach_image_pairs($attach_image_name, $attach_image_type, $sticker_image_id, $sticker_data['lang_code']);
$prev_image_id = $sticker_image_id;
} else {
fn_clone_image_pairs($sticker_image_id, $prev_image_id, $attach_image_type, $sticker_data['lang_code']);
}
}
} else {
db_query('UPDATE ?:ab__stickers SET ?u WHERE sticker_id = ?i', $sticker_data, $sticker_id);
$descr_cond = db_quote('WHERE sticker_id = ?i AND lang_code = ?s', $sticker_id, $lang_code);
if (db_get_field('SELECT sticker_id FROM ?:ab__sticker_descriptions ?p', $descr_cond)) {
db_query('UPDATE ?:ab__sticker_descriptions SET ?u ?p', $sticker_data, $descr_cond);
} else {
$sticker_data['sticker_id'] = $sticker_id;
$sticker_data['lang_code'] = $lang_code;
db_query('INSERT INTO ?:ab__sticker_descriptions ?e', $sticker_data);
}
$sticker_image_id = db_get_field('SELECT image_id FROM ?:ab__sticker_images WHERE sticker_id = ?i AND lang_code = ?s', $sticker_id, $lang_code);
if (empty($sticker_image_id)) {
$sticker_image_id = db_query('INSERT INTO ?:ab__sticker_images ?e', [
'image_id' => '',
'sticker_id' => $sticker_id,
'lang_code' => $lang_code,
]);
}
fn_attach_image_pairs($attach_image_name, $attach_image_type, $sticker_image_id, $lang_code);
}
if (isset($sticker_data['style']) && $sticker_data['style'] == StickerStyles::PICTOGRAM && !$is_demo) {
fn_ab__stickers_pictograms_generate([$sticker_id], [], 0, true);
}
return $sticker_id;
}

function fn_ab__stickers_check_group_conditions(&$conditions)
{
if (!empty($conditions['conditions'])) {
$conditions['conditions'] = array_filter($conditions['conditions'], function ($value) {
return (isset($value['operator'])) && (($value['operator'] === 'not_empty') || ($value['operator'] === 'exist') || (isset($value['value']) && !is_null($value['value']) && fn_string_not_empty($value['value'])));
});
}
}

function fn_ab_stickers_clone_sticker($item_id)
{

$repository = Tygh::$app['addons.ab__stickers.repository'];
$old_data = $repository->findById($item_id);
if (empty($old_data)) {
return false;
}
$old_data['status'] = ObjectStatuses::DISABLED;
$old_data['lang_code'] = DESCR_SL;
$old_data['name_for_admin'] .= ' ' . __('ab__stickers.cloned', [], DESCR_SL);
$sticker_main_pair = $old_data['main_pair'] ?? [];
unset($old_data['sticker_id'], $old_data['image_id'], $old_data['pictogram_data'], $old_data['main_pair'], $old_data['main_pair_p']);
$new_id = fn_ab__stickers_update_sticker(0, ['sticker_data' => $old_data]);
if (!empty($sticker_main_pair)) {
fn_ab__stickers_clone_image($new_id, $item_id, 'ab__stickers', DESCR_SL);
}
$pictogram_data = fn_ab__stickers_pictograms_get_texts($item_id);
fn_ab__stickers_pictograms_update_texts($new_id, $pictogram_data);
foreach (array_keys(Languages::getSimpleLanguages()) as $lang_code) {
if ($lang_code != DESCR_SL) {
$old_data = $repository->findById($item_id, $lang_code);
$old_data['status'] = 'D';
$old_data['lang_code'] = $lang_code;
$old_data['name_for_admin'] .= ' ' . __('ab__stickers.cloned', [], DESCR_SL);
unset($old_data['sticker_id'], $old_data['image_id'], $old_data['pictogram_data'], $old_data['main_pair'], $old_data['main_pair_p']);
fn_ab__stickers_update_sticker($new_id, ['sticker_data' => $old_data], $lang_code);
if (!empty($sticker_main_pair)) {
fn_ab__stickers_clone_image($new_id, $item_id, 'ab__stickers', $lang_code);
}
$pictogram_data = fn_ab__stickers_pictograms_get_texts($item_id, null, $lang_code);
fn_ab__stickers_pictograms_update_texts($new_id, $pictogram_data, $lang_code);
}
}
foreach (['manual', 'generated'] as $filling) {
$product_ids = db_get_field("SELECT CONCAT(product_id) FROM ?:products WHERE FIND_IN_SET(?i, ab__stickers_{$filling}_ids)", $item_id);
fn_ab__stickers_attach_sticker_to_products($new_id, $product_ids, $filling);
}
if ($old_data['style'] === StickerStyles::PICTOGRAM) {
fn_ab__stickers_pictograms_generate([$new_id], [], 0, true, false);
}
return $new_id;
}

function fn_ab__stickers_clone_image($sticker_id, $old_sticker_id, $type, $lang_code)
{
$table_name = 'ab__sticker_images';
$image_data = [
'sticker_id' => $sticker_id,
'lang_code' => $lang_code
];
if ($type === 'ab__pictograms') {
$image_data['product_id'] = 0;
$table_name = 'ab__sticker_pictograms';
}
$sticker_image_id = fn_ab__stickers_get_sticker_image_id($sticker_id, $type, $lang_code);
if (!$sticker_image_id) {
$sticker_image_id = db_query("INSERT INTO ?:{$table_name} ?e", $image_data);
}
$old_sticker_image_id = fn_ab__stickers_get_sticker_image_id($old_sticker_id, $type, $lang_code);
fn_clone_image_pairs($sticker_image_id, $old_sticker_image_id, $type, DESCR_SL);
}

function fn_ab__stickers_get_sticker_image_id($sticker_id, $type, $lang_code)
{
$table_name = 'ab__sticker_images';
if ($type === 'ab__pictograms') {
$table_name = 'ab__sticker_pictograms';
}
$condition = db_quote(' AND sticker_id = ?i AND lang_code = ?s', $sticker_id, $lang_code);
if ($type === 'ab__pictograms') {
$condition .= db_quote(' AND product_id = 0');
}
$image_id = db_get_field("SELECT image_id FROM ?:{$table_name} WHERE 1 {$condition} LIMIT 1");
return $image_id;
}

function fn_ab__stickers_attach_sticker_to_products($sticker_id, $product_ids = [], $filling = 'manual')
{
$query = "SELECT product_id, ab__stickers_{$filling}_ids FROM ?:products WHERE 1";
$old_products_with_current_sticker = db_get_array($query . " AND FIND_IN_SET(?i, ab__stickers_{$filling}_ids) AND product_id NOT IN (?n)", $sticker_id, $product_ids);
if (!empty($old_products_with_current_sticker)) {
for ($i = 0; $i < count($old_products_with_current_sticker); $i++) {
$sticker_ids = array_flip(explode(',', $old_products_with_current_sticker[$i]['ab__stickers_' . $filling . '_ids']));
unset($sticker_ids[$sticker_id]);
$old_products_with_current_sticker[$i]['ab__stickers_' . $filling . '_ids'] = implode(',', array_keys($sticker_ids));
}
db_query("INSERT INTO ?:products ?m ON DUPLICATE KEY UPDATE ab__stickers_{$filling}_ids=VALUES(ab__stickers_{$filling}_ids)", $old_products_with_current_sticker);
}
$new_products = db_get_array($query . " AND (NOT FIND_IN_SET(?i, ab__stickers_{$filling}_ids) OR ab__stickers_{$filling}_ids IS NULL) AND product_id IN (?n)", $sticker_id, $product_ids);
if (!empty($new_products)) {
for ($i = 0; $i < count($new_products); $i++) {
$temp = [];
if (strlen(trim($new_products[$i]['ab__stickers_' . $filling . '_ids'])) !== 0) {
$temp = explode(',', $new_products[$i]['ab__stickers_' . $filling . '_ids']);
}
$temp[] = $sticker_id;
$new_products[$i]['ab__stickers_' . $filling . '_ids'] = implode(',', $temp);
}
db_query("INSERT INTO ?:products ?m ON DUPLICATE KEY UPDATE ab__stickers_{$filling}_ids=VALUES(ab__stickers_{$filling}_ids)", $new_products);
}
if ($filling !== 'manual') {
db_query('UPDATE ?:ab__stickers SET last_update_time = ?i WHERE sticker_id = ?i', TIME, $sticker_id);
}
}

function fn_ab__stickers_get_sticker_condition_variants($func_and_params = [])
{
$ret = [];
$f = array_shift($func_and_params);
if (function_exists($f)) {
$ret = call_user_func_array($f, $func_and_params);
}
return $ret;
}

function fn_ab__stickers_get_simple_tags()
{
return db_get_hash_single_array('SELECT tag_id, tag FROM ?:tags WHERE status = ?s AND company_id = ?i', ['tag_id', 'tag'], ObjectStatuses::ACTIVE, fn_get_runtime_company_id());
}

function fn_ab__stickers_get_simple_promotions()
{
return db_get_hash_single_array('SELECT p.promotion_id, name FROM ?:promotions AS p LEFT JOIN ?:promotion_descriptions AS pd ON p.promotion_id = pd.promotion_id AND lang_code = ?s WHERE company_id = ?i AND zone = "catalog"', ['promotion_id', 'name'], DESCR_SL, fn_get_runtime_company_id());
}

function fn_ab__stickers_get_simple_vendor_plans()
{
return db_get_hash_single_array('SELECT v.plan_id, plan FROM ?:vendor_plans AS v LEFT JOIN ?:vendor_plan_descriptions AS vd ON v.plan_id = vd.plan_id AND lang_code = ?s WHERE status = ?s', ['plan_id', 'plan'], DESCR_SL, ObjectStatuses::ACTIVE);
}

function fn_ab__stickers_get_category_page_layout_template()
{
$selected_layout = fn_get_products_layout($_REQUEST);
$current_tmpl = '';
if ($selected_layout == 'products_multicolumns') {
$current_tmpl = 'blocks/products/products_multicolumns.tpl';
} elseif ($selected_layout == 'products_without_options') {
$current_tmpl = 'blocks/products/products.tpl';
} elseif ($selected_layout == 'short_list') {
$current_tmpl = 'blocks/products/short_list.tpl';
} else {

fn_set_hook('ab__stickers_get_custom_list_template', $selected_layout, $current_tmpl);
}

fn_set_hook('ab__stickers_get_category_page_layout_template', $selected_layout, $current_tmpl);
return $current_tmpl;
}

function fn_ab__stickers_prepare_sticker_to_frontend($sticker_data = [])
{
static $pictograms_images = [];
$product_id = $sticker_data['product_id'];
$sticker_id = $sticker_data['sticker_id'];
$template_id = $sticker_data['template_id'];
if ($sticker_data['style'] === StickerStyles::PICTOGRAM) {
$pictogram_icon = $sticker_data['main_pair']['icon']['absolute_path'] ?? null;
$pictogram_size = max($sticker_data['appearance']['full_size_image_size'], $sticker_data['appearance']['small_size_image_size']) * 2;
$pictogram_hash = fn_ab__stickers_pictograms_calculate_hash(
$sticker_data['appearance'],
$sticker_data['pictogram_data'],
$pictogram_icon,
$pictogram_size,
$product_id,
CART_LANGUAGE
);
if ($pictogram_hash && isset($pictograms_images[$pictogram_hash])) {
$sticker_data['main_pair_p'] = $pictograms_images[$pictogram_hash];
} else {
$pictograms_images[$pictogram_hash] = $sticker_data['main_pair_p'] = fn_get_image_pairs(
fn_ab__stickers_pictograms_get_image_id(
$sticker_id,
$product_id,
CART_LANGUAGE
),
'ab__pictograms',
ImagePairTypes::MAIN,
true,
true,
CART_LANGUAGE
);
}
}
$displays = fn_ab__stickers_get_templates_displays();
$template = $displays[$template_id]['tpl'] ?? 'detailed_page';
$html = Tygh::$app['view']
->assign('sticker', $sticker_data)
->assign('product_id', $product_id)
->assign('sticker_id', $sticker_id)
->assign('template', $template)
->fetch('addons/ab__stickers/views/ab__stickers/components/sticker.tpl');
return $html;
}

function fn_ab__stickers_change_stickers_status($sticker_ids = [], $status = ObjectStatuses::ACTIVE)
{
return db_query('UPDATE ?:ab__stickers SET status = ?s WHERE sticker_id IN (?n)', $status, $sticker_ids);
}

function fn_ab__stickers_sticker_is_cache_allowed()
{
return RenderManager::allowCache();
}

function fn_ab__stickers_sticker_get_ts_appearance_styles()
{
$appearance_styles = [
'rectangular' => __('ab__stickers.appearance_style.rectangular'),
'rounded' => __('ab__stickers.appearance_style.rounded'),
'teardrop' => __('ab__stickers.appearance_style.teardrop'),
'beveled_angle' => __('ab__stickers.appearance_style.beveled_angle'),
'circle' => __('ab__stickers.appearance_style.circle'),
];

fn_set_hook('ab__stickers_get_appearance_styles', $appearance_styles);
return $appearance_styles;
}

function fn_ab__stickers_form_conditions_optgroups($conditions = [])
{
$conditions_optgroups = [];
foreach ($conditions as $name => $condition) {
$conditions_optgroups[$condition['group'] ?? 'ab__stickers.condition_groups.default'][$name] = $condition;
}
return $conditions_optgroups;
}

function fn_ab__stickers_get_templates_displays()
{
$displays = [];
static $display_schema;
if (is_null($display_schema)) {
$display_schema = fn_get_schema('ab__stickers', 'display');
}
static $default_display_data = [
'display_size' => StickerSizes::SMALL,
'display_place' => StickerPlaces::NOT_DISPLAY,
'prohibited_display_places' => [],
'prohibited_list_positions' => [],
'pictograms_allowed' => YesNo::NO
];
$undefined_id_iterator = 1;
foreach ($display_schema as $display_id => $display) {
$display['old_id'] = $display_id;
$display = array_merge($default_display_data, $display);
if (empty($display['tpl']) && !empty($display['display_on'])) {
$display['tpl'] = fn_ab__stickers_get_template_path_from_display($display['display_on']);
}
if (isset($display['default_value']) && in_array($display['default_value'], [StickerSizes::SMALL, StickerSizes::FULL])) {
$display['display_size'] = $display['default_value'];
}
if (empty($display['id'])) {
if (!empty($display['tpl'])) {
$display['id'] = str_replace(['/', '\\', '.'], '_', $display['tpl']);
} else {
$display['id'] = 'id_' . $undefined_id_iterator++;
}
}
if (empty($display['tpl'])) {
$display['tpl'] = 'undefined';
}
$displays[$display['id']] = $display;
}
return $displays;
}

function fn_ab__stickers_get_prohibited_list_positions()
{
static $prohibited_list_positions;
if (is_null($prohibited_list_positions)) {
$prohibited_list_positions = [];
foreach (fn_ab__stickers_get_templates_displays() as $display) {
if (!empty($display['prohibited_list_positions'])) {
$prohibited_list_positions[$display['tpl']] = $display['prohibited_list_positions'];
}
}

fn_set_hook('ab__stickers_get_prohibited_list_positions', $prohibited_list_positions);
}
return $prohibited_list_positions;
}

function fn_ab__stickers_get_prohibited_template_display_places()
{
static $prohibited_display_places;
if (is_null($prohibited_display_places)) {
$prohibited_display_places = [];
foreach (fn_ab__stickers_get_templates_displays() as $display) {
if (!empty($display['prohibited_display_places'])) {
$prohibited_display_places[$display['tpl']] = $display['prohibited_display_places'];
}
}

fn_set_hook('ab__stickers_get_prohibited_template_display_places', $prohibited_display_places);
}
return $prohibited_display_places;
}

function fn_ab__stickers_get_templates_display_size($key = '', array $sticker_data = [])
{
$tmp = substr(substr($key, 1), 0, strlen($key) - 2);
$tmp = explode('][', $tmp);
$val = '';
foreach ($tmp as $arr_key) {
if (!empty($sticker_data[$arr_key])) {
$sticker_data = $val = $sticker_data[$arr_key];
}
}
return $val;
}

function fn_ab__stickers_hex_to_rgba($color, $opacity = false)
{
$default = 'rgb(0,0,0)';
if (empty($color)) {
return $default;
}
if (empty($opacity)) {
$opacity = 1;
}
if (strpos($opacity, '%') !== false) {
$opacity = (int) $opacity / 100;
}
if ($color[0] == '#') {
$color = substr($color, 1);
}
if (strlen($color) == 6) {
$hex = [$color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]];
} else {
return $default;
}
$rgb = array_map('hexdec', $hex);
if ($opacity) {
if (abs($opacity) > 1) {
$opacity = 1.0;
}
$output = 'rgba(' . implode(',', $rgb) . ',' . $opacity . ')';
} else {
$output = 'rgb(' . implode(',', $rgb) . ')';
}
return $output;
}

function fn_ab__stickers_get_main_block()
{

$storefront = Tygh::$app['storefront'];
$block_id = db_get_field('SELECT block_id FROM ?:bm_blocks WHERE type=\'main\' AND storefront_id=?i', $storefront->storefront_id);
$block = Block::instance()->getById($block_id);
return $block;
}

function fn_ab__stickers_get_sticker_frontend_information($sticker_html_id, $cache_name = '')
{
$sticker_key_parts = explode('-', $sticker_html_id);
$sticker_id = $sticker_key_parts[0] ?? 0;
$product_id = $sticker_key_parts[1] ?? 0;
$template_id = $sticker_key_parts[2] ?? 'detailed_page';
$cache_key = "{$cache_name}.{$sticker_html_id}";
return [$sticker_id, $product_id, $template_id, $cache_key];
}

function fn_ab__stickers_get_products_prices($products_ids)
{
static $prices = [];
if (!is_array($products_ids)) {
$products_ids = explode(',', $products_ids);
}
$products_ids_list = [];
foreach ($products_ids as $product_id) {
if (!isset($prices[$product_id]) && $product_id) {
$products_ids_list[] = $product_id;
}
}
if ($products_ids_list) {
$prices += (array) db_get_hash_multi_array('SELECT * FROM ?:product_prices WHERE product_id IN (?p)
AND lower_limit > 1 AND usergroup_id IN (?n)', ['product_id', 'lower_limit'], implode(',', $products_ids_list), array_merge([USERGROUP_ALL], Tygh::$app['session']['auth']['usergroup_ids']));
}
return array_intersect_key($prices, array_flip($products_ids));
}

function fn_ab__stickers_get_product_rating(&$product)
{
if (empty($product['average_rating']) && is_callable('fn_get_discussion')) {
$product['discussion'] = fn_get_discussion($product['product_id'], DiscussionObjectTypes::PRODUCT, true, $_REQUEST);
if (!empty($product['discussion'])) {
$product['average_rating'] = $product['discussion']['average_rating'];
}
}
}

function fn_ab__stickers_get_conditions_schema()
{
$conditions = fn_get_schema('ab__stickers', 'conditions');
$placeholders = fn_ab__stickers_get_placeholders();
foreach ($conditions as &$_conditions) {
foreach ($_conditions['conditions'] as &$condition) {
if (array_is_list($condition['available_placeholders'] ?? [])) {
foreach ($condition['available_placeholders'] ?? [] as $placeholder_id => $placeholder_name) {
if (isset($placeholders[$placeholder_name])) {
unset($condition['available_placeholders'][$placeholder_id]);
$condition['available_placeholders'][$placeholder_name] = $placeholders[$placeholder_name];
}
}
}
}
}
return $conditions;
}

function fn_ab__stickers_get_template_path_from_display($display_on)
{
$tmpl_path = str_replace(['display_on_lists', 'display_on_', '[', ']'], '', $display_on);
return $tmpl_path;
}

function fn_ab__stickers_get_display_places($params = [])
{
$display_places = fn_get_schema('ab__stickers', 'display_places');
$params = array_merge([
'places' => [],
'hook_themes' => '',
'theme_name' => fn_get_theme_path('[theme]'),
'block' => Tygh::$app['view']->getTemplateVars('block'),
'dispatch' => $_REQUEST['dispatch'] ?? '',
'product_id' => $_REQUEST['product_id'] ?? 0,
'template' => ''
], $params);
if (!empty($params['places'])) {
$places = (array) $params['places'];
$display_places = array_filter($display_places, function ($place) use ($places) {
return in_array($place['id'], $places);
});
}
if (!empty($params['hook_themes'])) {
$hook_themes = $params['hook_themes'];
$theme_name = $params['theme_name'];
$block = $params['block'];
$template = $params['template'];
$view_type = $params['view_type'] ?? '';
if (empty($view_type)) {
$dispatch_parts = explode('.', $params['dispatch']);
@list($controller, $mode) = $dispatch_parts;
if ($controller === 'products' && $mode === 'quick_view') {
$view_type = 'quick_view';
} else {
$view_type = fn_ab__stickers_get_view_type(array_merge($_REQUEST, $params), $block);
}
}
if (empty($template)) {
$template = $block['properties']['template'] ?? '';
if ($view_type === 'detailed_page') {
$template = fn_get_product_details_view($params['product_id']);
}
}
$display_places = array_filter($display_places, function ($place) use ($hook_themes, $theme_name, $view_type, $template) {
$is_hook = false;
if (!empty($place['hook_themes'])) {
$is_hook = in_array($theme_name, $place['hook_themes']);
if (!$is_hook && isset($place['hook_themes'][$theme_name])) {
$is_hook = in_array($view_type, $place['hook_themes'][$theme_name]);
if (!$is_hook && isset($place['hook_themes'][$theme_name][$view_type])) {
$is_hook = in_array($template, $place['hook_themes'][$theme_name][$view_type]);
}
}
}
return YesNo::isTrue($hook_themes) ? $is_hook : !$is_hook;
});
}
return $display_places;
}

function fn_ab__stickers_encode_data($data, $type = 'json')
{
if ($type === 'serialized') {
$data = serialize($data);
} else {
$data = json_encode($data) ?: '';
}
return $data;
}

function fn_ab__stickers_decode_data($data, $default = [], $type = 'json')
{
if (empty($data)) {
return $default;
}
if ($type === 'serialized') {
try {
$data = unserialize($data);
} catch (Exception $e) {
$data = $default;
}
} else {
$data = json_decode($data, true);
}
return $data;
}

function fn_ab__stickers_get_view_type($request = [], $block = [])
{
$type = 'list';
$dispatch = $request['dispatch'] ?? '';
if (!is_array($block)) {
$block = [];
}
if ($dispatch) {
$dispatch_parts = explode('.', $dispatch);
@list($controller, $mode) = $dispatch_parts;
$block_type = $block['type'] ?? '';
$cond_and_1 = $controller === 'products' && $mode === 'view';
$cond_and_2 = empty($block) || $block_type === 'main';
$cond_and_3 = $block_type === 'main' || (empty($block) && empty($request['full_render']));
$cond_or_1 = $controller === 'products' && $mode === 'quick_view';
$cond_or_2 = Registry::ifGet('ab__stickers.detailed_page', false);
if ($cond_and_1 && $cond_and_2 && $cond_and_3 || $cond_or_1 || $cond_or_2) {
$type = 'detailed_page';
}
}
return $type;
}
function fn_ab__stickers_get_unitheme_less_settings($only_allowed = false)
{
static $allowed_keys = [
'product_list.use_elements_alignment'
];
static $settings;
if (is_null($settings)) {
$settings = [];
if (Registry::isExist('settings.abt__ut2_less')) {
$settings = Registry::get('settings.abt__ut2_less');
} elseif (function_exists('fn_get_abt__ut2_settings')) {
$settings = fn_get_abt__ut2_settings('less', false, Registry::get('runtime.layout.style_id') . '.less');
}
if (!is_array($settings)) {
$settings = [];
}
}
$filtered_settings = $settings;
if ($only_allowed) {
$fn_filter_settings = function (array $settings, array $allowed_keys) : array {
$filtered = [];
foreach ($allowed_keys as $key) {
$path = explode('.', $key);
$value = $settings;
foreach ($path as $part) {
if (!is_array($value) || !array_key_exists($part, $value)) {
continue 2;
}
$value = $value[$part];
}
$temp = &$filtered;
foreach ($path as $part) {
if (!isset($temp[$part])) {
$temp[$part] = [];
}
$temp = &$temp[$part];
}
$temp = $value;
}
return $filtered;
};
$filtered_settings = $fn_filter_settings($settings, $allowed_keys);
}
return $filtered_settings;
}

function fn_ab__stickers_strip_tags($string, $allowed_tags = null)
{
if (empty($string)) {
return $string;
}
return strip_tags($string, $allowed_tags);
}
if (!function_exists('array_key_first')) {
function array_key_first($arr)
{
foreach ($arr as $key => $unused) {
return $key;
}
}
}
if (!function_exists('array_is_list')) {
function array_is_list(array $arr)
{
if ($arr === []) {
return true;
}
return array_keys($arr) === range(0, count($arr) - 1);
}
}
