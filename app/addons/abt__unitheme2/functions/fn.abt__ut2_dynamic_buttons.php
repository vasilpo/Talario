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
function fn_abt__ut2_get_cart_wl_compare_state(){
$data = [
'cart' => [
'added' => Tygh::$app['session']['abt__ut2_bought_products']['added'] ?? [],
'removed' => Tygh::$app['session']['abt__ut2_bought_products']['removed'] ?? [],
'products' => Tygh::$app['session']['abt__ut2_bought_products']['products'] ?? [],
'product_variations'=> Tygh::$app['session']['abt__ut2_bought_products']['product_variations'] ?? []
],
'wishlist' => [
'added' => Tygh::$app['session']['wishlist']['abt__ut2']['added'] ?? [],
'removed' => Tygh::$app['session']['wishlist']['abt__ut2']['removed'] ?? [],
'products' => Tygh::$app['session']['wishlist']['abt__ut2']['products'] ?? [],
],
'compare' => [
'added' => Tygh::$app['session']['abt__compare']['added'] ?? [],
'removed' => Tygh::$app['session']['abt__compare']['removed'] ?? [],
'products' => Tygh::$app['session']['abt__compare']['products'] ?? [],
]
];
$wishlist = Tygh::$app['session']['wishlist']['products'] ?? [];
$wishlist_lists = [&$data['wishlist']['added'], &$data['wishlist']['products']];
foreach ($wishlist_lists as &$wishlist_list) {
foreach ($wishlist as $w_product) {
$product_id = $w_product['product_id'];
if (!in_array($product_id, $wishlist_list)) {
$wishlist_list[] = $product_id;
}
}
$wishlist_list = array_unique($wishlist_list);
}
$data['wishlist']['removed'] = array_filter($data['wishlist']['removed'], function($wl_product_id) use ($wishlist) {
return !in_array($wl_product_id, $wishlist);
});
return $data;
}
function fn_abt__ut2_assign_cart_wl_compare_state(){
if(defined('AJAX_REQUEST')){
Tygh::$app['ajax']->assign('abt_state', fn_abt__ut2_get_cart_wl_compare_state());
}
}
function fn_abt__ut2_change_wl_state(){
$compare_original_products = Tygh::$app['session']['abt__compare']['products'] ?? [];
$compare = &Tygh::$app['session']['abt__compare'];
$compare['products'] = array_values(Tygh::$app['session']['comparison_list'] ?? []) ;
$compare['added'] = array_values(array_diff($compare['products'], $compare_original_products));
$compare['removed'] = array_values(array_diff($compare_original_products, $compare['products']));
}
