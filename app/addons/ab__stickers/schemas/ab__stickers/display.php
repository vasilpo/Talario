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
use Tygh\BlockManager\SchemesManager;
use Tygh\Enum\Addons\Ab_stickers\OutputPositions;
use Tygh\Enum\Addons\Ab_stickers\StickerPlaces;
use Tygh\Enum\Addons\Ab_stickers\StickerSizes;
use Tygh\Enum\YesNo;
defined('BOOTSTRAP') or die('Access denied');
$schema = [
'detailed_page' => [
'id' => 'detailed_page',
'tpl' => 'detailed_page',
'display_size' => StickerSizes::FULL,
'display_place' => StickerPlaces::PRODUCT_IMAGE,
'prohibited_display_places' => [],
'prohibited_list_positions' => [],
'pictograms_allowed' => YesNo::YES
],
];
$product_templates_scheme = SchemesManager::getBlockScheme('products', [], true)['templates'];
$small_icons_templates = [
'blocks/products/products_scroller.tpl',
];
$do_not_show_templates = [
'blocks/products/short_list.tpl',
];
$prohibited_product_labels = [
'blocks/products/products_links_thumb.tpl',
'blocks/products/products_small_items.tpl'
];
$excluded_templates = [
'blocks/products/products_text_links.tpl',
'addons/product_variations/blocks/products/variations_list.tpl',
'addons/master_products/blocks/products/vendor_products.tpl'
];
$do_not_show_templates = array_unique(array_merge($do_not_show_templates, $prohibited_product_labels));
$pictograms_allowed_templates = [
'blocks/products/products.tpl',
'blocks/products/products_multicolumns.tpl',
'blocks/products/short_list.tpl',
'blocks/products/ab__grid_list.tpl'
];
foreach ($product_templates_scheme as $tmpl_path => $product_template) {
if (!in_array($tmpl_path, $excluded_templates)) {
$def_size_val = StickerSizes::FULL;
$def_place_value = StickerPlaces::PRODUCT_IMAGE;
if (in_array($tmpl_path, $small_icons_templates)) {
$def_size_val = StickerSizes::SMALL;
} elseif (in_array($tmpl_path, $do_not_show_templates)) {
$def_place_value = StickerPlaces::NOT_DISPLAY;
}
$schema[$product_template['name']] = [
'id' => str_replace(['.', '/'], '_', $tmpl_path),
'tpl' => $tmpl_path,
'display_size' => $def_size_val,
'display_place' => $def_place_value,
'prohibited_display_places' => [],
'prohibited_list_positions' => [],
'pictograms_allowed' => YesNo::toId(in_array($tmpl_path, $pictograms_allowed_templates))
];
if ($tmpl_path == 'blocks/products/products_scroller.tpl') {
$schema[$product_template['name']]['prohibited_list_positions'] = [OutputPositions::BOTTOM];
}
if (in_array($tmpl_path, $prohibited_product_labels)) {
$schema[$product_template['name']]['prohibited_display_places'] = [StickerPlaces::PRODUCT_IMAGE];
$schema[$product_template['name']]['display_place'] = StickerPlaces::PRICE_BEFORE;
}
}
}
return $schema;
