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
$products = Tygh::$app['session']['cart']['products'] ?? [];
if (!isset(Tygh::$app['session']['abt__ut2_bought_products'])) {
Tygh::$app['session']['abt__ut2_bought_products'] = [
'key' => '',
'products' => [],
'product_variations' => []
];
}
$abt__ut2_bought_products_original = Tygh::$app['session']['abt__ut2_bought_products'];
$abt__ut2_bought_products = &Tygh::$app['session']['abt__ut2_bought_products'];
$key = md5(serialize($products));
if ($abt__ut2_bought_products['key'] !== $key) {
$abt__ut2_bought_products['key'] = $key;
$total_products = [];
foreach ($products as $product) {
$total_products[$product['product_id']] = $total_products[$product['product_id']] ?? 0;
$total_products[$product['product_id']] += $product['amount'];
}
$abt__ut2_bought_products['products'] = $total_products;
if(Registry::ifGet('addons.product_variations.status','D') === 'A'){
if($total_products){
$total_variations = [];
list($product_variations) = fn_get_products([
'pid' => array_keys($total_products),
]);
fn_gather_additional_products_data($product_variations, [
'get_features' => false,
'get_icon' => false,
'get_detailed' => false,
]);
foreach ($product_variations as $variation_product) {
if(!empty($variation_product['variation_group_id'])){
$parent_product_id = !empty($variation_product['parent_product_id']) ? $variation_product['parent_product_id'] : $variation_product['product_id'];
$total_variations[$parent_product_id] = $total_variations[$parent_product_id] ?? 0;
$total_variations[$parent_product_id] += $abt__ut2_bought_products['products'][$variation_product['product_id']] ?? 0;
}
}
}
$abt__ut2_bought_products['product_variations'] = $total_variations ?? [];
}
foreach (['products', 'product_variations'] as $items_type) {
$abt__ut2_bought_products['added'][$items_type] = array_diff_assoc($abt__ut2_bought_products[$items_type], $abt__ut2_bought_products_original[$items_type]);
$abt__ut2_bought_products['removed'][$items_type] = array_diff_key($abt__ut2_bought_products_original[$items_type], $abt__ut2_bought_products[$items_type]);;
}
fn_abt__ut2_assign_cart_wl_compare_state();
}
