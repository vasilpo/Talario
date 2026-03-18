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
use Tygh\Enum\ProductFeatures;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\SiteArea;
use Tygh\enum\YesNo;
use Tygh\Registry;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
function fn_abt__ut2_add_products_features_list(&$products, $feature_id = 0, $return = false)
{
if (!empty($products)) {
$categories_hashs = [];
foreach ($products as $product) {
if (!empty($product['category_ids'])) {
sort($product['category_ids']);
$hash = md5(serialize($product['category_ids']));
} elseif (!empty($product['main_category'])) {
$hash = md5(serialize((array) $product['main_category']));
}
$categories_hashs[$hash][] = $product['product_id'];
}
foreach ($categories_hashs as $key => $hash_ps) {
$ps = [];
foreach ($products as $p) {
if (in_array($p['product_id'], $hash_ps)) {
$ps[$p['product_id']] = $p;
}
}
$features_per_product = fn_abt__ut2_get_products_features_list($ps, SiteArea::STOREFRONT, CART_LANGUAGE, $feature_id);
if (!empty($features_per_product) && is_array($features_per_product)) {
foreach ($products as $id => $pd) {
if (!empty($features_per_product[$pd['product_id']])) {
$products[$id]['abt__ut2_features'] = $features_per_product[$pd['product_id']];
}
}
}
}
}
if ($return) {
return $products;
}
}
function fn_abt__ut2_get_products_features_list($products, $display_on = SiteArea::STOREFRONT, $lang_code = CART_LANGUAGE, $feature_id = 0)
{
static $filters = null;

fn_set_hook('get_products_features_list_pre', $products, $display_on, $lang_code);
$features_list = [];
if ($display_on == 'H') {
$condition = ' AND f.display_on_header = \'Y\'';
} elseif ($display_on == SiteArea::STOREFRONT) {
$condition = ' AND f.display_on_catalog = \'Y\'';
} elseif ($display_on == 'CP') {
$condition = ' AND (f.display_on_catalog = \'Y\' OR f.display_on_product = \'Y\')';
} elseif ($display_on == SiteArea::ADMIN_PANEL || $display_on == 'EXIM') {
$condition = '';
} else {
$condition = ' AND f.display_on_product = \'Y\'';
}
if (!empty($feature_id)) {
$condition .= db_quote(' AND f.feature_id in (?n) ', (array) $feature_id);
}
$category_ids = [];
foreach ($products as $product) {
if (!empty($product['category_ids'])) {
$category_ids = $product['category_ids'];
} elseif (!empty($product['main_category'])) {
$category_ids = (array) $product['main_category'];
}
}
$category_ids = array_unique($category_ids);
$path = fn_get_category_ids_with_parent($category_ids);
$find_set = [
' f.categories_path = \'\' ',
];
foreach ($path as $k => $v) {
$find_set[] = db_quote(' FIND_IN_SET(?i, f.categories_path) ', $v);
}
$find_in_set = db_quote(' AND (?p)', implode('OR', $find_set));
$condition .= $find_in_set;
$fields = db_quote('v.product_id, v.feature_id, v.value, v.value_int, v.variant_id, f.feature_type, fd.description, fd.prefix, fd.suffix, vd.variant, f.parent_id, f.position, gf.position as gposition');
$join = db_quote('LEFT JOIN ?:product_features_values as v ON v.feature_id = f.feature_id '
. ' LEFT JOIN ?:product_features_descriptions as fd ON fd.feature_id = v.feature_id AND fd.lang_code = ?s'
. ' LEFT JOIN ?:product_feature_variants fv ON fv.variant_id = v.variant_id'
. ' LEFT JOIN ?:product_feature_variant_descriptions as vd ON vd.variant_id = fv.variant_id AND vd.lang_code = ?s'
. ' LEFT JOIN ?:product_features as gf ON gf.feature_id = f.parent_id AND gf.feature_type = ?s ', $lang_code, $lang_code, ProductFeatures::GROUP);
$allowed_feature_statuses = [ObjectStatuses::ACTIVE];
$allowed_parent_group_statuses = [ObjectStatuses::ACTIVE];
if ($display_on == 'EXIM') {
$allowed_feature_statuses[] = ObjectStatuses::HIDDEN;
$allowed_parent_group_statuses[] = ObjectStatuses::HIDDEN;
}
$condition = db_quote('f.status IN (?a) AND v.product_id in (?n) ?p', $allowed_feature_statuses, array_keys($products), $condition);
$condition .= db_quote(' AND IF(f.parent_id,'
. ' (SELECT status FROM ?:product_features as df WHERE df.feature_id = f.parent_id), ?s) IN (?a)', ObjectStatuses::ACTIVE, $allowed_parent_group_statuses);
$condition .= db_quote(' AND ('
. ' v.variant_id != 0'
. ' OR (f.feature_type != ?s AND v.value != \'\')'
. ' OR (f.feature_type = ?s)'
. ' OR v.value_int != \'\''
. ')'
. ' AND v.lang_code = ?s', ProductFeatures::SINGLE_CHECKBOX, ProductFeatures::SINGLE_CHECKBOX, $lang_code);

fn_set_hook('get_products_features_list_before_select', $fields, $join, $condition, $products, $display_on, $lang_code);
$_data = db_get_array("SELECT $fields FROM ?:product_features as f $join WHERE $condition ORDER BY fd.description, fv.position");
$_variant_ids = [];
if (!empty($_data)) {
if ($filters === null) {
$filter_condition = 'status = \''. ObjectStatuses::ACTIVE .'\'';
if (fn_allowed_for('ULTIMATE')) {
$filter_condition .= fn_get_company_condition('?:product_filters.company_id');
}
$filters = db_get_hash_array("SELECT filter_id, feature_id FROM ?:product_filters WHERE {$filter_condition}", 'feature_id');
}
foreach ($_data as $k => $feature) {
if ($feature['feature_type'] == ProductFeatures::SINGLE_CHECKBOX) {
if ($feature['value'] != YesNo::YES && $display_on != SiteArea::ADMIN_PANEL) {
unset($_data[$k]);
continue;
}
}
if (empty($features_list[$feature['product_id']][$feature['feature_id']])) {
$features_list[$feature['product_id']][$feature['feature_id']] = $feature;
}
if (!empty($feature['variant_id'])) {
if (isset($filters[$feature['feature_id']])) {
$features_list[$feature['product_id']][$feature['feature_id']]['features_hash'] = fn_add_filter_to_hash('', $filters[$feature['feature_id']]['filter_id'], $feature['variant_id']);
}
$features_list[$feature['product_id']][$feature['feature_id']]['variants'][$feature['variant_id']] = [
'value' => $feature['value'],
'value_int' => $feature['value_int'],
'variant_id' => $feature['variant_id'],
'variant' => $feature['variant'],
];
$_variant_ids[] = $feature['variant_id'];
}
}
if (!empty($_variant_ids)) {
$images = fn_get_image_pairs($_variant_ids, 'feature_variant', 'V', true, true, $lang_code);
foreach ($features_list as $p_id => $fs) {
foreach ($fs as $feature_id => $feature) {
if (isset($images[$feature['variant_id']])) {
$features_list[$p_id][$feature_id]['variants'][$feature['variant_id']]['image_pairs'] = reset($images[$feature['variant_id']]);
}
}
}
}
}
$products_features_list = [];
$list = $features_list;
foreach ($list as $product_id => $features_list) {
$groups = [];
foreach ($features_list as $f_id => $data) {
$groups[$data['parent_id']]['features'][$f_id] = $data;
$groups[$data['parent_id']]['position'] = empty($data['parent_id']) ? $data['position'] : $data['gposition'];
}
$features_list = [];
if (!empty($groups)) {
$groups = fn_sort_array_by_key($groups, 'position');
foreach ($groups as $g) {
$g['features'] = fn_sort_array_by_key($g['features'], 'position');
$features_list = fn_array_merge($features_list, $g['features']);
}
}
unset($groups);
foreach ($features_list as $f_id => $data) {
unset($features_list[$f_id]['position'], $features_list[$f_id]['gposition']);
}
$products_features_list[$product_id] = $features_list;
}

fn_set_hook('get_products_features_list_post', $products_features_list, $products, $display_on, $lang_code);
return $products_features_list;
}
function fn_abt__unitheme2_get_product_features(&$fields, &$join, $condition, &$params)
{
if (AREA === SiteArea::STOREFRONT && !empty($params['variants_selected_only'])) {
$device = Registry::get('settings.ab__device');
$settings = Registry::get('settings.abt__ut2.products');
if (
($settings['view']['brand_link_behavior'] === 'to_category_with_filter' && $settings['view']['show_brand_format'][$device] != "none")
||
$settings['search_similar_in_category'][$device] === YesNo::YES
) {
$fields[] = 'abt_filters.filter_id';
$join .= db_quote(' LEFT JOIN ?:product_filters AS abt_filters ON abt_filters.feature_id = pf.feature_id AND abt_filters.company_id = pf.company_id');
$params['abt__ut2_filters_loaded'] = true;
}
}
}
function fn_abt__unitheme2_get_product_features_post(&$data, $params, $has_ungroupped)
{
if (!empty($params['abt__ut2_filters_loaded'])) {
foreach ($data as $feature_id => &$feature) {
if (!empty($feature['filter_id']) && !empty($feature['variant_id'])) {
$feature['features_hash'] = fn_add_filter_to_hash('', $feature['filter_id'], $feature['variant_id']);
}
if (!empty($feature['yml2_variants_unit'])){
$feature['suffix'] = str_replace($feature['yml2_variants_unit'], '', $feature['suffix']);
}
if (!empty($feature['subfeatures'])) {
fn_abt__unitheme2_get_product_features_post($feature['subfeatures'], $params, $has_ungroupped);
}
}
}
}

function fn_abt__unitheme2_get_product_feature_variants(&$fields, $join, $condition, $group_by, $sorting, $lang_code, $limit, $params){
if (AREA === SiteArea::STOREFRONT && isset($params['feature_id']) && is_array($params['feature_id'])) {
$fields[] = '?:product_feature_variants.color';
if(strpos($join,'?:product_features_values') !== false){
$fields[] = '?:product_features_values.value_int';
}
}
$fields[] = '?:product_feature_variants.abt__ut2_multicolor';
$fields[] = '?:product_feature_variants.abt__ut2_color_style';
}
function fn_abt__unitheme2_get_filters_products_count_post($params, $lang_code, &$filters, $selected_filters)
{
$variant_ids = [];
foreach ($filters as $filter) {
if ($filter['variants']) {
foreach ($filter['variants'] as $variant) {
$variant_ids[] = $variant['variant_id'];
}
}
}
$variant_ids = array_unique($variant_ids);
$variants = db_get_hash_array("SELECT * FROM ?:product_feature_variants WHERE variant_id IN (?n)", 'variant_id', $variant_ids);
foreach ($filters as &$filter) {
if ($filter['variants']) {
$thumbnail_colors = [];
foreach ($filter['variants'] as &$variant) {
if (isset($variants[$variant['variant_id']])) {
$variant = array_merge($variants[$variant['variant_id']], $variant);
}
if ($variant['abt__ut2_color_style'] == 'thumbnail'){
$thumbnail_colors[] = $variant['variant_id'];
}
}
$image_pairs = fn_get_image_pairs($thumbnail_colors, 'feature_variant', 'V', true, true, $lang_code);
foreach ($image_pairs as $variant_id => $image_pair) {
$filter['variants'][$variant_id]['image_pair'] = array_pop($image_pair);
}
}
}
}
function fn_abt__unitheme2_get_product_features_list_before_select(&$fields){
$fields .= ",fv.abt__ut2_color_style, fv.abt__ut2_multicolor";
}
function fn_abt__unitheme2_get_product_features_list_post(&$features_list){
foreach ($features_list as &$feature){
if (!empty($feature['variant_id'])){
$feature['variants'][$feature['variant_id']]['abt__ut2_multicolor'] = $feature['abt__ut2_multicolor'];
$feature['variants'][$feature['variant_id']]['abt__ut2_color_style'] = $feature['abt__ut2_color_style'];
}
}
}