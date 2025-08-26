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
if (!defined('BOOTSTRAP')) { die('Access denied'); }
if (($_SERVER['REQUEST_METHOD'] == 'POST' && $mode === 'add') || in_array($mode, ['delete', 'clear'])) {
$wl = &Tygh::$app['session']['wishlist'];
$wl_original_products = $wl['abt__ut2']['products'] ?? [];
$wl['abt__ut2']['products'] = !empty($wl['products']) ? array_column($wl['products'], 'product_id') : [];
$wl['abt__ut2']['added'] = array_values(array_diff($wl['abt__ut2']['products'], $wl_original_products));
$wl['abt__ut2']['removed'] = array_values(array_diff($wl_original_products, $wl['abt__ut2']['products']));
return;
}
if ($mode == 'view') {

$view = Tygh::$app['view'];
$products = $view->getTemplateVars('products');
if (!empty($products)) {
$params = [
'extend' => [],
'area' => AREA,
];
$products = fn_load_products_extra_data($products, $params, CART_LANGUAGE);
$view->assign('products', $products);
}
}
