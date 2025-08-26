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
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\YesNo;
$schema['abt__ut2_advanced_subcategories_menu'] = [
'templates' => 'addons/abt__unitheme2/blocks/abt__ut2_advanced_subcategories_menu.tpl',
'content' => [
'abt__ut2_subcategories' => [
'type' => 'function',
'function' => ['fn_abt__ut2_get_advanced_sub_or_parent_categories'],
],
],
'settings' => [
'abt__ut2_show_parents' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'abt__ut2_show_siblings' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'abt__ut2_show_children' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
],
'abt__ut2_show_products_count' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'abt__ut2_category_count_level_2' => [
'type' => 'input',
'default_value' => 10,
],
'abt__ut2_category_count_level_3' => [
'type' => 'input',
'default_value' => 10,
],
],
'wrappers' => 'blocks/wrappers',
'cache' => [
'update_handlers' => [
'categories',
'category_descriptions',
],
'request_handlers' => ['current_category_id' => '%CATEGORY_ID%'],
],
];
if (!empty($schema['banners'])) {
$schema['banners']['templates']['addons/abt__unitheme2/blocks/abt__ut2_banner_carousel_combined.tpl'] = [
'settings' => [
'margin' => [
'option_name' => 'abt__ut2.option.margin',
'type' => 'input',
'default_value' => '0',
],
'height' => [
'option_name' => 'abt__ut2.option.height',
'type' => 'input',
'default_value' => '200px',
],
'height_mobile' => [
'option_name' => 'abt__ut2.option.height_mobile',
'type' => 'input',
'default_value' => '200px',
],
'delay' => [
'type' => 'input',
'default_value' => '3',
],
'navigation' => [
'option_name' => 'abt__ut2.option.navigation',
'type' => 'selectbox',
'values' => [
'N' => 'none',
'D' => 'dots',
'P' => 'pages',
'A' => 'arrows',
],
'default_value' => 'D',
],
],
];
$schema['banners']['templates']['addons/abt__unitheme2/blocks/abt__ut2_banner_multiple.tpl'] = [
'settings' => [
'descr' => [
'type' => 'Attention',
'items_type' => 'text',
'option_name' => 'abt__ut2.option.attention',
],
'margin' => [
'option_name' => 'abt__ut2.option.margin',
'type' => 'input',
'default_value' => '0',
],
'height' => [
'option_name' => 'abt__ut2.option.height',
'type' => 'input',
'default_value' => '200px',
],
'height_mobile' => [
'option_name' => 'abt__ut2.option.height_mobile',
'type' => 'input',
'default_value' => '200px',
],
'minimal_width' => [
'option_name' => 'abt__ut2.option.minimal_width',
'type' => 'input',
'default_value' => '300px',
],
'multiple_mode_items' => [
'option_name' => 'abt__ut2.option.multiple_mode_items',
'type' => 'selectbox',
'values' => [
'1' => 'abt__ut2.option.multiple_mode_items.variants.1',
'2' => 'abt__ut2.option.multiple_mode_items.variants.2',
'3' => 'abt__ut2.option.multiple_mode_items.variants.3',
'4' => 'abt__ut2.option.multiple_mode_items.variants.4',
'5' => 'abt__ut2.option.multiple_mode_items.variants.5',
],
'default_value' => 'one',
],
'navigation' => [
'option_name' => 'abt__ut2.option.navigation',
'type' => 'selectbox',
'values' => [
'N' => 'none',
'D' => 'dots',
'P' => 'pages',
'A' => 'arrows',
'L' => 'abt__ut2.option.navigation.variants.native-scroller',
],
'default_value' => 'D',
],
'delay' => [
'type' => 'input',
'default_value' => '3',
],
],
];
$schema['banners']['templates']['addons/abt__unitheme2/blocks/abt__ut2_banner_combined.tpl'] = [
'settings' => [
'margin' => [
'option_name' => 'abt__ut2.option.margin',
'type' => 'input',
'default_value' => '0',
],
'height' => [
'option_name' => 'abt__ut2.option.height',
'type' => 'input',
'default_value' => 'auto',
],
'height_mobile' => [
'option_name' => 'abt__ut2.option.height_mobile',
'type' => 'input',
'default_value' => 'auto',
],
],
];
$schema['banners']['cache']['callable_handlers']['date'] = ['date', ['Y-m-d']];
}
$schema['abt__ut2_fly_menu'] = [
'content' => [
'items' => [
'type' => 'function',
'function' => ['fn_abt__ut2_get_fly_menu_content'],
],
'settings_link' => [
'type' => 'template',
'template' => 'addons/abt__unitheme2/views/abt__ut2/components/fly_menu_settings_link.tpl',
'remove_indent' => true,
'hide_label' => true,
],
],
'templates' => [
'addons/abt__unitheme2/blocks/abt__ut2_fly_menu.tpl' => [
'settings' => [
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
'abt__ut2_show_title' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
],
],
],
'multilanguage' => false,
'single_for_location' => true,
'settings' => [
'open_on_sticky_panel_button' => [
'type' => 'checkbox',
'default_value' => 'N',
'option_name' => 'abt__ut2.block.open_on_sticky_panel_button',
'tooltip' => __('abt__ut2.block.open_on_sticky_panel_button.tooltip'),
],
],
];
$schema['menu']['settings'] = [
'open_on_sticky_panel_button' => [
'type' => 'checkbox',
'default_value' => 'N',
'option_name' => 'abt__ut2.block.open_on_sticky_panel_button',
'tooltip' => __('abt__ut2.block.open_on_sticky_panel_button.tooltip'),
],
];
$schema['menu']['cache']['callable_handlers']['request'] = ['fn_abt__ut2_blocks_menu_get_request_hash', ['$block_data', '$_REQUEST', '$_SERVER']];
if (is_string($schema['lite_checkout_shipping_methods']['templates'])) {
$schema['lite_checkout_shipping_methods']['templates'] = [
$schema['lite_checkout_shipping_methods']['templates'] => [],
'addons/abt__unitheme2/blocks/lite_checkout/abt__ut2_simple_shipping_methods.tpl' => [],
];
}
if (is_string($schema['lite_checkout_payment_methods']['templates'])) {
$schema['lite_checkout_payment_methods']['templates'] = [
$schema['lite_checkout_payment_methods']['templates'] => [],
'addons/abt__unitheme2/blocks/lite_checkout/abt__ut2_simple_payment_methods.tpl' => [],
];
}

$schema['abt__ut2_lite_checkout_place_order_button'] = [
'show_on_locations' => ['checkout'],
'templates' => 'blocks/lite_checkout/abt__ut2_place_order_button.tpl',
'wrappers' => 'blocks/lite_checkout/wrappers',
];

$schema['abt__ut2_lite_checkout_promotion_coupon'] = [
'show_on_locations' => ['checkout'],
'templates' => 'blocks/checkout/abt__ut2_promotion_coupon.tpl',
'wrappers' => 'blocks/lite_checkout/wrappers',
];
$schema['abt__ut2_block_opener'] = [
'templates' => 'blocks/abt__ut2_block_opener.tpl',
'wrappers' => 'blocks/wrappers',
'content' => array(
'items' => array(
'type' => 'function',
'function' => array('fn_abt__ut2_get_block_snappings')
),
'abt__ut2_block_id' => array(
'type' => 'template',
'template' => 'addons/abt__unitheme2/views/abt__ut2/components/block_manager/block_selector.tpl',
'hide_label' => true,
'data_function' => array('fn_abt__ut2_get_blocks'),
),
),
];
$schema['languages']['settings']['format']['values']['ab__name_without_icons'] = 'ab__name_without_icons';
$schema['abt__ut2_contacts_manually'] = [
'multilanguage' => true,
'templates' => 'addons/abt__unitheme2/blocks/abt__ut2_contacts_manually.tpl',
'content' => [
'phone_1' => [
'option_name' => 'abt__ut2.contacts.phone_1',
'type' => 'input',
'default_value' => '',
'required' => true,
],
'phone_2' => [
'option_name' => 'abt__ut2.contacts.phone_2',
'type' => 'input',
'default_value' => ''
],
'phone_3' => [
'option_name' => 'abt__ut2.contacts.phone_3',
'type' => 'input',
'default_value' => ''
],
'phone_4' => [
'option_name' => 'abt__ut2.contacts.phone_4',
'type' => 'input',
'default_value' => ''
],
'phone_5' => [
'option_name' => 'abt__ut2.contacts.phone_5',
'type' => 'input',
'default_value' => ''
],
'email' => [
'option_name' => 'abt__ut2.contacts.email',
'type' => 'input',
'default_value' => ''
],
'working_hours' => [
'option_name' => 'abt__ut2.contacts.working_hours',
'type' => 'simple_text',
'default_value' => ''
],
'address' => [
'option_name' => 'abt__ut2.contacts.address',
'type' => 'simple_text',
'default_value' => ''
],
],
'wrappers' => 'blocks/wrappers',
];
if (defined('PAGE_TYPE_BLOG')) {
$schema['blog']['content']['items']['fillings']['blog.abt_ut2_recent_posts'] = [
'params' => [
'simple' => true,
'sort_by' => 'timestamp',
'sort_order' => 'desc',
'status' => ObjectStatuses::ACTIVE,
'page_type' => PAGE_TYPE_BLOG,
'get_image' => true
],
'settings' => [
'parent_page_id' => [
'type' => 'picker',
'default_value' => '0',
'picker' => 'pickers/pages/picker.tpl',
'picker_params' => [
'multiple' => false,
'use_keys' => YesNo::NO,
'default_name' => __('root_level'),
'extra_url' => '&page_type=' . PAGE_TYPE_BLOG
],
],
'limit' => [
'type' => 'input',
'default_value' => 7
],
],
];
}
return $schema;
