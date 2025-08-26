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
defined('BOOTSTRAP') or die('Access denied');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if ($mode === 'get_stickers') {
if (!empty($_REQUEST['sticker_ids']) && !empty($_REQUEST['controller_mode'])) {
$stickers = $stickers_output = $stickers_ids = $products_ids = [];
$cache_name = 'ab__stickers_html';
Registry::registerCache(
['ab__stickers', $cache_name],
['ab__stickers', 'ab__sticker_images', 'ab__sticker_descriptions'],
Registry::cacheLevel(['static', 'user', 'lang'])
);
$no_cached_stickers = [];
$placeholders = fn_ab__stickers_get_placeholders();
foreach ($_REQUEST['sticker_ids'] as $sticker_html_id) {
list($sticker_id, $product_id, $template_id, $cache_key) = fn_ab__stickers_get_sticker_frontend_information($sticker_html_id);
$stickers_ids[] = $sticker_id;
if (Registry::isExist($cache_key)) {
$stickers_output[$sticker_html_id] = Registry::get($cache_key);
if (fn_ab__stickers_check_placeholders($stickers_output[$sticker_html_id], $placeholders)) {
$products_ids[] = $product_id;
}
} else {
$no_cached_stickers[] = $sticker_html_id;
}
}

$repository = Tygh::$app['addons.ab__stickers.repository'];
list($stickers) = $repository->find([
'item_ids' => array_unique($stickers_ids)
]);
foreach ($no_cached_stickers as $sticker_html_id) {
list($sticker_id, $product_id, $template_id, $cache_key) = fn_ab__stickers_get_sticker_frontend_information($sticker_html_id);
if ($sticker_data = $stickers[$sticker_id] ?? []) {
$sticker_data = array_merge($sticker_data, ['html_id' => $sticker_html_id, 'product_id' => $product_id, 'template_id' => $template_id]);
$stickers_output[$sticker_html_id] = fn_ab__stickers_prepare_sticker_to_frontend($sticker_data);
Registry::set($cache_key, $stickers_output[$sticker_html_id]);
if (fn_ab__stickers_check_placeholders($stickers_output[$sticker_html_id], $placeholders)) {
$products_ids[] = $product_id;
}
}
}
if ($products_ids) {
list($products) = fn_get_products(['pid' => array_unique($products_ids)]);
fn_gather_additional_products_data($products, ['get_options' => false]);
}
foreach ($stickers_output as $sticker_html_id => $sticker_html) {
list($sticker_id, $product_id, $template_id) = fn_ab__stickers_get_sticker_frontend_information($sticker_html_id);
$sticker_placeholders = [];
if (isset($products[$product_id], $stickers[$sticker_id])) {
$sticker_placeholders = fn_ab__stickers_parse_placeholders($placeholders, $stickers[$sticker_id], $products[$product_id]);
}
$stickers_output[$sticker_html_id] = fn_ab__stickers_get_sticker_string_value($sticker_html, $sticker_placeholders);
}
Tygh::$app['ajax']->assign('stickers_html', $stickers_output);
}
}
return [CONTROLLER_STATUS_NO_CONTENT];
}
