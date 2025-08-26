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

include_once(Registry::get('config.dir.addons') . 'ab__motivation_block/schemas/ab__mb/item_templates.functions.php');
$schema = [
'addons/ab__motivation_block/blocks/components/item_templates/custom_content.tpl' => [
'name' => __('ab__mb.template_path.variants.custom'),
'tooltip' => __('ab__mb.template_path.variants.custom.tooltip'),
'settings' => 'custom_content',
],
'templates' => [
'subitems' => [
'addons/ab__motivation_block/blocks/components/item_templates/geo_maps.tpl' => [
'name' => __('ab__mb.template_path.templates.geo_maps'),
'tooltip' => __('ab__mb.template_path.templates.geo_maps.tooltip'),
'condition_func' => ['fn_ab__mb_templates_geo_maps'],
],
'addons/ab__motivation_block/blocks/components/item_templates/payment_methods.tpl' => [
'name' => __('ab__mb.template_path.templates.payment_methods'),
'tooltip' => __('ab__mb.template_path.templates.payment_methods.tooltip'),
],
'addons/ab__motivation_block/blocks/components/item_templates/product_categories_list.tpl' => [
'name' => __('ab__mb.template_path.templates.categories_list'),
'tooltip' => __('ab__mb.template_path.templates.categories_list.tooltip'),
'settings' => 'product_categories_list',
'condition_func' => ['fn_ab__mb_templates_categories_list'],
],
'addons/ab__motivation_block/blocks/components/item_templates/product_tags_list.tpl' => [
'name' => __('ab__mb.template_path.templates.tags_list'),
'tooltip' => __('ab__mb.template_path.templates.tags_list.tooltip'),
'settings' => 'product_tags_list',
'condition_func' => ['fn_ab__mb_templates_tags'],
],
],
],
];
return $schema;
