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
use Tygh\Enum\YesNo;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}
if ($mode == 'view') {
$params = $_REQUEST;
$product = Tygh::$app['view']->getTemplateVars('product');
if (YesNo::toBool(Registry::get('addons.ab__motivation_block.use_additional_categories'))) {
$params['category_ids'] = $product['category_ids'];
} else {
$params['category_ids'] = [$product['main_category']];
}
if (fn_allowed_for('MULTIVENDOR') && !empty($product['company_id'])) {
$params['company_id'] = $product['company_id'];
}
$params['storefront_id'] = \Tygh::$app['storefront']->storefront_id;
unset($params['sort_by']);
list($motivation_items) = fn_ab__mb_get_motivation_items($params);
Tygh::$app['view']->assign('ab__motivation_items', $motivation_items);
}