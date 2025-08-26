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
use Tygh\Enum\Addons\Ab_stickers\StickerPlaces;
defined('BOOTSTRAP') or die('Access denied');
$schema = [
StickerPlaces::PRODUCT_IMAGE => [
'id' => StickerPlaces::PRODUCT_IMAGE,
'position' => 'replace',
'hook_themes' => [
'responsive' => [
'detailed_page' => [
'blocks/product_templates/bigpicture_template.tpl'
]
],
'bright_theme' => [
'detailed_page' => [
'blocks/product_templates/bigpicture_template.tpl'
]
],
]
],
StickerPlaces::PRICE_BEFORE => [
'id' => StickerPlaces::PRICE_BEFORE,
'position' => 'before',
'hook_themes' => [
'abt__unitheme2' => [
'detailed_page',
'quick_view',
'list' => [
'addons/ab__deal_of_the_day/blocks/ab__deal_of_the_day.tpl'
]
],
'abt__youpitheme' => ['detailed_page'],
'responsive' => [
'list' => [
'blocks/products/products_multicolumns.tpl'
]
],
'bright_theme' => [
'list' => [
'blocks/products/products_multicolumns.tpl',
'blocks/products/products_scroller.tpl'
]
]
]
],
StickerPlaces::PRICE_AFTER => [
'id' => StickerPlaces::PRICE_AFTER,
'position' => 'after',
'hook_themes' => [
'abt__unitheme2' => [
'detailed_page',
'quick_view',
'list' => [
'blocks/products/products.tpl',
'blocks/products/products_multicolumns.tpl'
]
],
'responsive' => [
'list' => [
'blocks/products/products_multicolumns.tpl'
]
],
'bright_theme' => [
'list' => [
'blocks/products/products_multicolumns.tpl'
]
]
]
]
];
return $schema;
