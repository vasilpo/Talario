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
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return [];
}
if ($mode == 'ut2_select_variation') {
if (empty($_REQUEST['product_id'])) {
return [CONTROLLER_STATUS_NO_CONTENT];
}

$view = Tygh::$app['view'];
list($product) = fn_get_products([
'pid' => $_REQUEST['product_id'],
]);
if (!empty($_REQUEST['combination'])) {
$product[$_REQUEST['product_id']]['combination'] = $_REQUEST['combination'];
}
fn_gather_additional_products_data($product, [
'get_features' => true,
'get_icon' => true,
'get_detailed' => true,
]);
$product = $product[$_REQUEST['product_id']];
$product['detailed_params']['info_type'] = 'D';

fn_set_hook('abt__ut2_select_variation_pre_render', $_REQUEST, $product);
$view->assign([
'product' => $product,
'show_select_variations_button' => false,
]);
if (!empty($_REQUEST['prev_url'])) {
$view->assign('redirect_url', $_REQUEST['prev_url']);
}
if (!isset($_REQUEST['result_ids']) && isset($_REQUEST['get_view_content'])) {

$ajax = Tygh::$app['ajax'];
$ajax->assignHtml($_REQUEST['get_view_content'], $view->fetch("views/{$controller}/{$mode}.tpl"));
return [CONTROLLER_STATUS_NO_CONTENT];
}
return [CONTROLLER_STATUS_OK];
}
