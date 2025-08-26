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
use Tygh\Addons\Ab_stickers\ServiceProvider;
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');
Tygh::$app->register(new ServiceProvider());
fn_register_hooks(// implode sticker_ids by comma
'update_product_pre',
'get_product_data_post',
['get_products', Registry::ifGet('addons.warehouses.priority', 2021) - 1],
'gather_additional_products_data_post',
'generate_thumbnail_pre',
'generate_thumbnail_post',
'update_language_post',
'delete_languages_post'
);
if (fn_allowed_for('MULTIVENDOR')) {
fn_register_hooks(// On delete Vendor
'delete_company'
);
}
