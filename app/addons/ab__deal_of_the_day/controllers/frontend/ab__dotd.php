<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2024   *
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
use Tygh\Registry;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if ($mode === 'get_promos') {
$promotions = [];
if (!empty($_REQUEST['promotions_ids'])) {
list($items) = fn_get_promotions([
'promotion_id' => $_REQUEST['promotions_ids'],
'active' => true,
'extend' => ['get_images'],
'get_hidden' => false,
]);
if (!empty($items)) {
$promotions = [];
foreach ($items as $id => $promotion) {
$promotions[$id] = Tygh::$app['view']->assign('promotion', $promotion)->fetch('addons/ab__deal_of_the_day/components/promo_in_product_list.tpl');
}
}
}
Tygh::$app['ajax']->assign('promotions', $promotions);
exit();
}
if ($mode === 'get_chains') {
list($chains, $search) = fn_ab__dotd_get_chains($_REQUEST);
Tygh::$app['view']->assign('chains', $chains);
$search['text_get_more'] = __('ab__dotd.get_more_combinations', [$search['more']]);
$search['text_showed'] = __('ab__dotd.showed_combinations', ['[n]' => $search['items_per_page'] * $search['page'], '[total]' => $search['total_items']]);
Tygh::$app['ajax']->assign('html', Tygh::$app['view']->fetch('addons/buy_together/blocks/product_tabs/buy_together.tpl'));
Tygh::$app['ajax']->assign('search', $search);
}
exit;
}
