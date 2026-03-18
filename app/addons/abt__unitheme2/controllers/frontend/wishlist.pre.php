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
if ($mode === 'delete') {
if (!isset($_REQUEST['cart_id']) && !empty($dispatch_extra)) {
$wl = &Tygh::$app['session']['wishlist'];
$force = $action === 'force';
$product_id = (int)$dispatch_extra;
$pids = [$product_id];
if ($force && Registry::get('addons.product_variations.status') === 'A') {
$pids = Tygh\Addons\ProductVariations\ServiceProvider::getProductIdMap()->getProductChildrenIds($product_id);
$pids[$product_id] = $product_id;
}
if (isset($wl['products'])) {
$cart_ids = [];
foreach ($pids as $pid) {
foreach ($wl['products'] as $k => $product) {
if ($product['product_id'] == $pid) {
$cart_ids[] = $k;
}
}
}
foreach ($cart_ids as $cart_id) {
fn_delete_wishlist_product($wl, $cart_id);
}
fn_set_notification('W','',__('abt__ut2.notifications.product_removed_from_wishlist'));
fn_save_cart_content($wl, $auth['user_id'], 'W');
}
}
}