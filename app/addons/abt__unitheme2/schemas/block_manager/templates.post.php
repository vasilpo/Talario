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
use Tygh\Enum\YesNo;
use Tygh\Registry;
$menu_settings = [
'abt__ut2_filling_type' => [
'type' => 'template',
'default_value' => 'column_filling',
'template' => 'addons/abt__unitheme2/views/abt__ut2/components/block_manager/menu_filling_type.tpl',
'values' => [
'column_filling' => [
'show_fields' => [
'abt__ut2_columns_count',
],
],
'row_filling' => [
'show_fields' => [
'abt__ut2_columns_count',
],
],
],
],
'abt__ut2_columns_count' => [
'type' => 'selectbox',
'default_value' => '4',
'values' => [
'1' => 'abt__ut2_columns_count.1',
'2' => 'abt__ut2_columns_count.2',
'3' => 'abt__ut2_columns_count.3',
'4' => 'abt__ut2_columns_count.4',
'5' => 'abt__ut2_columns_count.5',
'6' => 'abt__ut2_columns_count.6',
],
'tooltip' => __('abt__ut2_columns_count.tooltip'),
],
'dropdown_second_level_elements' => [
'type' => 'input',
'default_value' => '30',
'tooltip' => __('abt__ut2_dropdown_second_level_elements.tooltip'),
],
'dropdown_third_level_elements' => [
'type' => 'input',
'default_value' => '30',
'tooltip' => __('abt__ut2_dropdown_third_level_elements.tooltip'),
],
'abt__no_hidden_elements_third_level_view' => [
'type' => 'input',
'default_value' => '5',
'tooltip' => __('abt__no_hidden_elements_third_level_view.tooltip'),
],
'abt__ut2_view_more_btn_behavior' => [
'type' => 'selectbox',
'values' => [
'view_items' => 'abt__ut2_view_more_btn_behavior_view_items',
'trigger_parent_link' => 'abt__ut2_view_more_btn_behavior_trigger_parent_link',
],
'tooltip' => __('abt__ut2_view_more_btn_behavior.tooltip'),
'default_value' => 'view_items',
],
'abt_menu_icon_items' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
'tooltip' => __('abt_menu_icon_items.tooltip'),
],
'abt__menu_compact_view' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'abt__ut2_menu_min_height' => [
'option_name' => 'abt__ut2.settings.general.menu_min_height',
'tooltip' => __('abt__ut2.settings.general.menu_min_height.tooltip'),
'type' => 'input',
'default_value' => Registry::ifGet('settings.abt__ut2.general.menu_min_height', 490),
],
];
$schema['blocks/menu/abt__ut2_dropdown_horizontal_mwi.tpl'] = [
'settings' => array_merge([
'abt_menu_long_names' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
'tooltip' => __('abt_menu_long_names.tooltip'),
],
'abt_menu_long_names_max_width' => [
'type' => 'input',
'default_value' => '100',
'tooltip' => __('abt_menu_long_names_max_width.tooltip'),
],
'abt__menu_add_horizontal_scroll_sections' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
'tooltip' => __('abt__menu_add_horizontal_scroll_sections.tooltip'),
],
], $menu_settings),
];
$schema['blocks/menu/abt__ut2_dropdown_vertical_mwi.tpl'] = [
'settings' => array_merge([], $menu_settings),
];

$schema['blocks/products/products_scroller_advanced.tpl'] = [
'settings' => [
'show_price' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
],
'enable_quick_view' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'not_scroll_automatically' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'scroll_per_page' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'thumbnail_width' => [
'type' => 'input',
],
'abt__ut2_thumbnail_height' => [
'type' => 'input',
],
'speed' => [
'type' => 'input',
'default_value' => 400,
],
'delay' => [
'type' => 'input',
'default_value' => 3,
],
'item_quantity' => [
'type' => 'input',
'default_value' => 5,
],
'outside_navigation' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
],
],
'bulk_modifier' => [
'fn_gather_additional_products_data' => [
'products' => '#this',
'params' => [
'get_icon' => true,
'get_detailed' => true,
'get_options' => true,
'get_additional' => true,
],
],
],
];

$schema['blocks/static_templates/abt__ut2__title_block.tpl']['settings']['name'] = [
'option_name' => 'abt__ut2.block.name',
'type' => 'description',
];
$schema['blocks/static_templates/abt__ut2__title_block.tpl']['settings']['abt__ut_center_title'] = [
'option_name' => 'abt__ut2.block.abt__ut_center_title',
'type' => 'checkbox',
'default_value' => YesNo::NO,
];
$schema['blocks/static_templates/abt__ut2__title_block.tpl']['settings']['abt__ut_title_line_decoration'] = [
'option_name' => 'abt__ut2.block.abt__ut_title_line_decoration',
'type' => 'checkbox',
'default_value' => YesNo::NO,
];
$schema['blocks/static_templates/abt__ut2__title_block.tpl']['settings']['abt__ut_big_size_title'] = [
'option_name' => 'abt__ut2.block.abt__ut_big_size_title',
'type' => 'selectbox',
'default_value' => 'normal',
'values' => [
'normal' => 'abt__ut2.block.abt__ut_big_size_title.normal',
'big' => 'abt__ut2.block.abt__ut_big_size_title.big',
'biggest' => 'abt__ut2.block.abt__ut_big_size_title.biggest',
],
];
$schema['blocks/static_templates/abt__ut2__title_block.tpl']['settings']['abt__ut_title_opacity'] = [
'option_name' => 'abt__ut2.block.abt__ut_title_opacity',
'type' => 'checkbox',
'default_value' => YesNo::NO,
];

$schema['blocks/products/products_small_items.tpl']['settings']['thumbnail_width'] = [
'option_name' => 'abt__ut2.block.thumbnail_width',
'type' => 'input',
'default_value' => 100,
];
$schema['blocks/products/products_small_items.tpl']['settings']['thumbnail_height'] = [
'option_name' => 'abt__ut2.block.thumbnail_height',
'type' => 'input',
'default_value' => 100,
];

$scroller_settings = $schema['blocks/products/products_scroller.tpl']['settings'];
$insertAfterKey = 'item_quantity';
$device_settings = [
'item_quantity_sm_desktop'=>[
'option_name' => 'abt__ut2.block.item_quantity_sm_desktop',
'type' => 'input',
'default_value' => 5,
],
'item_quantity_tablet'=>[
'option_name' => 'abt__ut2.block.item_quantity_tablet',
'type' => 'input',
'default_value' => 4,
],
'item_quantity_mobile'=>[
'option_name' => 'abt__ut2.block.item_quantity_mobile',
'type' => 'input',
'default_value' => 2,
]
];
if (array_key_exists($insertAfterKey, $scroller_settings)) {
$firstPart = array_slice($scroller_settings, 0, array_search($insertAfterKey, array_keys($scroller_settings)) + 1, true);
$secondPart = array_slice($scroller_settings, array_search($insertAfterKey, array_keys($scroller_settings)) + 1, null, true);
$schema['blocks/products/products_scroller.tpl']['settings'] = $firstPart + $device_settings + $secondPart;
}else{
$schema['blocks/products/products_scroller.tpl']['settings'] = array_merge($schema['blocks/products/products_scroller.tpl']['settings'], $device_settings);
}

$schema['blocks/products/ab__grid_list.tpl'] = $schema['blocks/products/grid_list.tpl'];
$schema['blocks/products/ab__grid_list.tpl']['settings']['abt__ut2_loading_type'] = [
'type' => 'selectbox',
'default_value' => 'onclick',
'values' => [
'onclick' => 'abt__ut2_loading_type.onclick',
'onclick_and_scroll' => 'abt__ut2_loading_type.onclick_and_scroll',
],
];
$schema['blocks/products/ab__grid_list.tpl']['settings']['thumbnail_width'] = [
'option_name' => 'abt__ut2.block.thumbnail_width',
'type' => 'input',
];
$schema['blocks/products/ab__grid_list.tpl']['settings']['abt__ut2_thumbnail_height'] = [
'option_name' => 'abt__ut2.block.thumbnail_height',
'type' => 'input',
];
$schema['blocks/products/ab__grid_list.tpl']['bulk_modifier']['fn_gather_additional_products_data']['params']['get_additional'] = true;

$tmpls = [
'blocks/products/products_multicolumns.tpl',
'blocks/products/products_without_options.tpl',
'blocks/products/products_scroller_advanced.tpl',
'blocks/products/ab__grid_list.tpl',
];
if (!empty($tmpls)) {
foreach ($tmpls as $tmpl) {
$schema[$tmpl]['bulk_modifier']['fn_abt__ut2_add_products_features_list'] = ['products' => '#this'];
}
}

$schema['blocks/products/products_native_scroller_advanced.tpl'] = [
'settings' => [
'show_price' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
],
'enable_quick_view' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'thumbnail_width' => [
'type' => 'input',
],
'abt__ut2_thumbnail_height' => [
'type' => 'input',
],
'item_quantity' => [
'type' => 'input',
'default_value' => 5,
],
],
'bulk_modifier' => [
'fn_gather_additional_products_data' => [
'products' => '#this',
'params' => [
'get_icon' => true,
'get_detailed' => true,
'get_options' => true,
'get_additional' => true,
],
],
],
];

$schema['addons/blog/blocks/abt_ut2_recent_posts.tpl'] = [
'fillings' => array('blog.abt_ut2_recent_posts'),
];

$schema['addons/abt__unitheme2/blocks/lite_checkout/abt__ut2_simple_shipping_methods.tpl'] = [
'settings' => [
'abt__ut2_as_select' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
],
];

$schema['addons/abt__unitheme2/blocks/abt__ut2_contacts_manually.tpl'] = [
'settings' => [
'abt__ut2__block_contacts_open_right_panel' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'abt__ut2__block_contacts_show_call_request_button' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
],
'abt__ut2__block_contacts_show_social_buttons' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
],
],
];
$schema['blocks/static_templates/abt__ut2__contacts.tpl'] = [
'settings' => [
'abt__ut2__block_contacts_show_call_request_button' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
],
'abt__ut2__block_contacts_show_social_buttons' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
],
'abt__ut2__block_contacts_show_email' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
],
'abt__ut2__block_contacts_show_addres' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
],
],
];

$schema['addons/abt__unitheme2/blocks/lite_checkout/abt__ut2_simple_payment_methods.tpl'] = [
'settings' => [
'abt__ut2_as_select' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
],
];
$schema['blocks/products/products.tpl']['bulk_modifier']['fn_gather_additional_products_data']['params']['get_additional'] = true;
return $schema;
