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
use Tygh\BlockManager\SchemesManager;
use Tygh\Enum\YesNo;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
function fn_abt__ut2_prepare_variation_features_variants($variation_features, $features, $product_id = 0)
{
$current_product_id = $product_id;
$product_id = !empty($_REQUEST['base_variant']) ? $_REQUEST['base_variant'] : $product_id;
if (empty($variation_features)){
return [];
}
$variation_features = fn_array_value_to_key($variation_features, 'feature_id');
$variation_feature_styles = db_get_hash_array(
'SELECT feature_id, filter_style
FROM ?:product_features
WHERE feature_id IN (?n)',
'feature_id',
array_keys($variation_features)
);
$variation_features = fn_array_merge($variation_feature_styles, $variation_features);
foreach ($variation_features as $f_id => &$f) {
if (!empty($f['filter_style']) && $f['filter_style'] == 'color') {
$variant_color = db_get_hash_array(
'SELECT variant_id, color,abt__ut2_color_style,abt__ut2_multicolor
FROM ?:product_feature_variants
WHERE feature_id = ?i AND variant_id IN (?n)',
'variant_id',
$f_id,
array_keys($f['variants'])
);
if (!empty($variant_color)) {
$variation_features[$f_id]['variants'] = fn_array_merge($variant_color, $f['variants']);
}
$thumbnail_colors = [];
foreach ($f['variants'] as &$variant) {
if (isset($variants[$variant['variant_id']])) {
$variant = array_merge($variants[$variant['variant_id']], $variant);
}
if ($variant['abt__ut2_color_style'] == 'thumbnail'){
$thumbnail_colors[] = $variant['variant_id'];
}
}
$image_pairs = fn_get_image_pairs($thumbnail_colors, 'feature_variant', 'V');
foreach ($image_pairs as $variant_id => $image_pair) {
$f['variants'][$variant_id]['image_pair'] = array_pop($image_pair);
}
}
}
uksort(
$variation_features,
static function ($a, $b) use ($variation_features) {
return $variation_features[$a]['position'] - $variation_features[$b]['position'];
});
if (!empty($product_id)) {
foreach ($variation_features as &$variation_feature) {
if (empty($variation_feature['filter_style']) || $variation_feature['filter_style'] != 'color'){
foreach ($variation_feature['variants'] as &$variant) {
if ($variant['product_id'] == $current_product_id) {
$variant['active'] = true;
}
}
} else {
$variation_products = array_column($variation_feature['variants'], 'product');
if (!in_array($product_id, array_column($variation_products, 'product_id'))) {
$search_p_ids = db_get_fields('SELECT product_id FROM ?:product_variation_group_products WHERE parent_product_id = ?i', $product_id);
$search_p_variants = db_get_fields('
SELECT
p.product_id
FROM ?:products as p
LEFT JOIN ?:product_features_values as fv ON fv.product_id = p.product_id
WHERE
fv.feature_id = ?i
AND p.product_id IN (?n)
AND fv.lang_code = ?s
AND fv.variant_id = (
SELECT
variant_id
FROM ?:product_features_values
WHERE feature_id = ?i AND product_id = ?i AND lang_code = ?s)'
, $variation_feature['feature_id']
, $search_p_ids
, CART_LANGUAGE
, $variation_feature['feature_id']
, $product_id
, CART_LANGUAGE);
if (!empty($search_p_variants)){
$search_p_ids = $search_p_variants;
}
} else {
$search_p_ids = [$product_id];
}
foreach ($variation_feature['variants'] as &$variant){
if ($variant['product']['product_id'] == $current_product_id){
$variant['active'] = true;
$current_variant_active = true;
}
}
uksort(
$variation_feature['variants'],
static function ($a, $b) use ($variation_feature, $search_p_ids) {
return in_array($variation_feature['variants'][$a]['product']['product_id'], $search_p_ids)?-1:1;
});
if (empty($current_variant_active)){
$variation_feature['variants'][array_key_first($variation_feature['variants'])]['active'] = true;
}
}
}
}
return $variation_features;
}
function fn_abt__ut2_get_block_unique_id($block_id)
{
static $block_ids = [];
if (!isset($block_ids[$block_id])) {
$block_data = Block::instance()->getById($block_id);
$block_ids[$block_id] = Block::getUniqueIdByData($block_data);
}
return $block_ids[$block_id];
}
function fn_abt__ut2_get_current_template_name($block = [])
{
static $blocks = [];
$block_id = $block['block_id'] ?? 0;
if (!isset($blocks[$block_id])) {
if (($block['type'] ?? '') === 'products') {
$template = $block['template'];
} else {
$selected_layout = fn_get_products_layout($_REQUEST);
if ($selected_layout == 'products_multicolumns') {
$current_tmpl = 'blocks/products/products_multicolumns.tpl';
} elseif ($selected_layout == 'products_without_options') {
$current_tmpl = 'blocks/products/products.tpl';
} elseif ($selected_layout == 'short_list') {
$current_tmpl = 'blocks/products/short_list.tpl';
}
$template = $current_tmpl ?? '';
}
$blocks[$block_id] = fn_abt__ut2_get_product_templates('path')[$template] ?? '';
}
return $blocks[$block_id];
}
function fn_abt__ut2_get_product_templates($key = 'name')
{
static $templates = null;
if (is_null($templates)) {
$_templates = SchemesManager::getBlockScheme('products', [], true)['templates'];
$templates['name'] = array_combine(array_column($_templates, 'name'), array_keys($_templates));
$templates['path'] = array_combine(array_keys($_templates), array_column($_templates, 'name'));
}
return $templates[$key] ?? [];
}
function fn_abt__ut2_get_product_parent_id($product)
{
$parent_id = $product['product_id'];
if (!empty($product['parent_product_id'])) {
$parent_id = $product['parent_product_id'];
}
return $parent_id;
}
function fn_abt__unitheme2_render_block_register_cache($block, $cache_prefix, $block_schema, $cache_this_block, $display_this_block)
{
if (YesNo::isTrue($block['abt__ut2_variation'] ?? YesNo::NO)) {
$block['properties'] = [];

$smarty = \Tygh::$app['view'];
$smarty->assign('block', $block);
}
}