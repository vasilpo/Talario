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
use Tygh\Enum\Addons\Ab_stickers\OutputPositions;
use Tygh\Enum\Addons\Ab_stickers\StickerPlaces;
use Tygh\Enum\Addons\Ab_stickers\StickerStyles;
use Tygh\Enum\Addons\Ab_stickers\StickerTypes;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\SiteArea;
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');

function fn_ab__stickers_update_product_pre(&$product_data, $product_id, $lang_code, $can_update)
{
if (!empty($product_data['ab__stickers_manual_ids'])) {
$product_data['ab__stickers_manual_ids'] = implode(',', $product_data['ab__stickers_manual_ids']);
}
}

function fn_ab__stickers_get_product_data_post(&$product_data, $auth, $preview, $lang_code)
{
if (!empty($product_data['ab__stickers_manual_ids'])) {
$product_data['ab__stickers_manual_ids'] = explode(',', $product_data['ab__stickers_manual_ids']);
}
if (!empty($product_data['ab__stickers_generated_ids'])) {
$product_data['ab__stickers_generated_ids'] = explode(',', $product_data['ab__stickers_generated_ids']);
}
}

function fn_ab__stickers_get_products($params, $fields, $sortings, &$condition, &$join, $sorting, $group_by, $lang_code, &$having)
{
if ($params['area'] == SiteArea::ADMIN_PANEL) {
if (!empty($params['ab__sticker_conditions'])) {
Registry::set('runtime.ab__stickers.cond_lang_code', $lang_code);
list($where, $joins, $stickers_having) = fn_ab__stickers_build_sticker_conditions_query($params['ab__sticker_conditions']);
$join .= ' ' . implode(' ', $joins);
$condition .= ' AND (' . $where . ')';
if (!empty($stickers_having)) {
$having[] = $stickers_having;
}
}
if (!empty($params['ab__stickers_manual_ids']) || !empty($params['ab__stickers_generated_ids'])) {
if (!empty($params['ab__stickers_manual_ids'])) {
foreach ($params['ab__stickers_manual_ids'] as $sticker_id) {
$condition .= db_quote(' AND FIND_IN_SET(?i, ab__stickers_manual_ids)', $sticker_id);
}
}
if (!empty($params['ab__stickers_generated_ids'])) {
foreach ($params['ab__stickers_generated_ids'] as $sticker_id) {
$condition .= db_quote(' AND FIND_IN_SET(?i, ab__stickers_generated_ids)', $sticker_id);
}
}

$repository = Tygh::$app['addons.ab__stickers.repository'];
list($s) = $repository->find();
Tygh::$app['view']->assign('ab__stickers', $s);
}
}
}

function fn_ab__stickers_gather_additional_products_data_post($product_ids, $params, &$products, $auth, $lang_code)
{
if (AREA == SiteArea::STOREFRONT && !empty(Tygh::$app['view'])) {
static $static_product_stickers = [
'detailed_page' => [],
'lists' => [],
];
$all_stickers_on_page = [];
foreach ($products as &$product) {
if (!empty($product['ab__stickers_generated_ids']) || !empty($product['ab__stickers_manual_ids'])) {
$ab__stickers_generated_ids = empty($product['ab__stickers_generated_ids']) ? [] : (is_array($product['ab__stickers_generated_ids']) ? $product['ab__stickers_generated_ids'] : explode(',', $product['ab__stickers_generated_ids']));
$ab__stickers_manual_ids = empty($product['ab__stickers_manual_ids']) ? [] : (is_array($product['ab__stickers_manual_ids']) ? $product['ab__stickers_manual_ids'] : explode(',', $product['ab__stickers_manual_ids']));
$all_stickers_on_page[] = $product['ab__sticker_ids'] = implode(',', array_unique(array_merge($ab__stickers_generated_ids, $ab__stickers_manual_ids)));
}
}
$all_stickers_on_page = array_unique(explode(',', implode(',', $all_stickers_on_page)));
$block = Tygh::$app['view']->getTemplateVars('block');
if (fn_ab__stickers_get_view_type($_REQUEST, $block) === 'detailed_page') {
$display_on = $current_tmpl = 'detailed_page';
Registry::del('ab__stickers.detailed_page');
} else {
$display_on = 'lists';
if (!empty($block) && !empty($block['properties']['template'])) {
$current_tmpl = $block['properties']['template'];
} else {
$current_tmpl = fn_ab__stickers_get_category_page_layout_template();
}
Tygh::$app['view']->assign('ab__stickers_current_tmpl', $current_tmpl);
}

$repository = Tygh::$app['addons.ab__stickers.repository'];
static $s_stickers = [];
$_tmp_str = implode(',', $all_stickers_on_page);
if (!empty($all_stickers_on_page) && empty($s_stickers[$_tmp_str])) {
list($s_stickers[$_tmp_str]) = $repository->find([
'item_ids' => $all_stickers_on_page,
'display_place' => $current_tmpl,
]);
}
static $d_stickers = [];
if (empty($d_stickers)) {
list($d_stickers) = $repository->find([
'display_place' => $current_tmpl,
'forced_type' => StickerTypes::DYNAMIC,
]);
}
$stickers = (empty($s_stickers[$_tmp_str]) ? [] : $s_stickers[$_tmp_str]) + $d_stickers;
if (!empty($stickers)) {
foreach ($products as &$product) {
if (!empty($static_product_stickers[$display_on][$product['product_id']])) {
$product['ab__stickers'] = $static_product_stickers[$display_on][$product['product_id']];
} else {
$temp = [];
if (!empty($product['ab__sticker_ids'])) {
$product_stickers = explode(',', $product['ab__sticker_ids']);
foreach ($product_stickers as $sticker_id) {
if (!empty($stickers[$sticker_id])) {
if (!empty($stickers[$sticker_id]['vendors_data'])) {
if (!empty($product['company_id']) && !empty($stickers[$sticker_id]['vendors_data'][$product['company_id']])) {
$sticker_vendors_data = $stickers[$sticker_id]['vendors_data'][$product['company_id']];
if ($sticker_vendors_data['vendor_status'] == ObjectStatuses::DISABLED) {
continue;
}
}
}
$temp[$sticker_id] = $stickers[$sticker_id];
}
}
}
foreach ($stickers as $sticker) {
if ($sticker['type'] == StickerTypes::DYNAMIC) {
if (!empty($sticker['vendors_data']) && !empty($product['company_id']) && !empty($sticker['vendors_data'][$product['company_id']])) {
$sticker_vendors_data = $sticker['vendors_data'][$product['company_id']];
if ($sticker_vendors_data['vendor_status'] == ObjectStatuses::DISABLED) {
continue;
}
}
$res = fn_ab__stickers_validate_dynamic_sticker($sticker['conditions'], $product, $product_ids, $auth);
if (!empty($sticker['conditions']['conditions']) && $res['result'] === true) {
$temp[$sticker['sticker_id']] = $sticker;
}
}
}
uasort($temp, function ($a, $b) {
return ($a['position'] < $b['position']) ? -1 : 1;
});
$static_product_stickers[$display_on][$product['product_id']] = $product['ab__stickers'] = $temp;
}
}
foreach ($products as &$product) {
if (!empty($product['ab__sticker_ids'])) {
$stickers_ids = explode(',', $product['ab__sticker_ids']);
foreach ($stickers_ids as $sticker_id) {
if (isset($stickers[$sticker_id]) && $stickers[$sticker_id]['style'] === StickerStyles::PICTOGRAM) {
unset($stickers_ids[$sticker_id]);
}
}
$product['ab__sticker_ids'] = implode(',', $stickers_ids);
foreach ($product['ab__stickers'] as $sticker_id => $sticker) {
if ($sticker['style'] === StickerStyles::PICTOGRAM) {
if (!empty(db_get_field('SELECT image_id FROM ?:ab__sticker_pictograms WHERE sticker_id=?i AND product_id=?i AND lang_code=?s', $sticker['sticker_id'], $product['product_id'], $lang_code))) {
$product['ab__s_pictograms'][$sticker_id] = $sticker;
}
unset($product['ab__stickers'][$sticker_id]);
}
}
}
}
$display_places = fn_ab__stickers_get_display_places();
$output_positions = OutputPositions::getList();
$prohibited_output_positions = fn_ab__stickers_get_prohibited_list_positions();
$prohibited_display_places = fn_ab__stickers_get_prohibited_template_display_places();
foreach ($products as &$product) {
$product_stickers = $product['ab__stickers'] ?? [];
$product['ab__stickers'] = [];
$product_sticker_ids = [];
if (isset($product['ab__sticker_ids'])) {
$product['ab__sticker_ids'] = [];
}
foreach ($product_stickers as $sticker_id => $sticker) {
$sticker_display_place = $sticker['display_place'][$current_tmpl] ?? false;
if (
empty($sticker_display_place) ||
!isset($display_places[$sticker_display_place]) ||
$sticker_display_place === StickerPlaces::NOT_DISPLAY ||
in_array($sticker_display_place, $prohibited_display_places[$current_tmpl] ?? [])
) {
continue;
}
if ($sticker_display_place === StickerPlaces::PRODUCT_IMAGE) {
$sticker_output_position = $sticker['output_position'][$current_tmpl] ?? false;
if (
empty($sticker_output_position) ||
!isset($output_positions[$sticker_output_position]) ||
in_array($sticker_output_position, $prohibited_output_positions[$current_tmpl] ?? [])
) {
continue;
}
$product_sticker_ids[] = $sticker_id;
$product['ab__stickers'][$sticker_display_place][$sticker_output_position][$sticker_id] = $sticker;
} else {
$product_sticker_ids[] = $sticker_id;
$product['ab__stickers'][$sticker_display_place][OutputPositions::TOP][$sticker_id] = $sticker;
}
}
if (isset($product['ab__sticker_ids'])) {
$product['ab__sticker_ids'] = $product_sticker_ids;
}
}
}
}
}

function fn_ab__stickers_generate_thumbnail_pre($image_path, $width, $height, $make_box)
{
if (strpos($image_path, 'ab__stickers') !== false) {
$old_value = Registry::get('settings.Thumbnails.thumbnail_background_color');
if ($old_value !== '') {
Registry::set('settings.Thumbnails.thumbnail_background_color', '');
Registry::set('addons.ab__stickers.thumbnail_background_color_old_value', $old_value);
}
$old_value = Registry::get('settings.Thumbnails.convert_to');
if ($old_value !== 'original' || $old_value !== 'png') {
Registry::set('settings.Thumbnails.convert_to', 'original');
Registry::set('addons.ab__stickers.convert_to_old_value', $old_value);
}
}
}

function fn_ab__stickers_generate_thumbnail_post($th_filename, $lazy, $image_path, $width, $height, $image)
{
if (strpos($image_path, 'ab__stickers') !== false) {
$old_value = Registry::ifGet('addons.ab__stickers.thumbnail_background_color_old_value', '');
if ($old_value) {
Registry::set('settings.Thumbnails.thumbnail_background_color', $old_value);
Registry::del('addons.ab__stickers.thumbnail_background_color_old_value');
}
$old_value = Registry::ifGet('addons.ab__stickers.convert_to_old_value', '');
if ($old_value) {
Registry::set('settings.Thumbnails.convert_to', $old_value);
Registry::del('addons.ab__stickers.convert_to_old_value');
}
}
}

function fn_ab__stickers_update_language_post($language_data, $lang_id, $action)
{
if ($action == 'add') {

$repository = Tygh::$app['addons.ab__stickers.repository'];
$repository->duplicateImagesForLanguage($repository->getStickerIds(), $language_data['lang_code']);
}
}

function fn_ab__stickers_delete_languages_post($lang_ids, $lang_codes, $deleted_lang_codes)
{

$repository = Tygh::$app['addons.ab__stickers.repository'];
$sticker_ids = $repository->getStickerIds();
foreach ($deleted_lang_codes as $lang_code) {
foreach ($sticker_ids as $sticker_id) {
$image_id = db_get_field('SELECT image_id FROM ?:ab__sticker_images WHERE sticker_id = ?i AND lang_code = ?s', $sticker_id, $lang_code);
fn_delete_image_pairs($image_id, 'ab__stickers');
}
db_query('DELETE FROM ?:ab__sticker_images WHERE lang_code = ?s', $lang_code);
}
}

function fn_ab__stickers_delete_company($company_id, $result, $storefronts)
{
if ($result) {

$vendors_repository = Tygh::$app['addons.ab__stickers.vendors_repository'];
$vendors_repository->deleteStickersVendorDataByCompanyIds([$company_id]);
}
}
