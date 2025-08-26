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
use Tygh\Registry;
use Tygh\Enum\YesNo;
$default_products_view = Registry::get('settings.Appearance.default_products_view');
$block_types = SchemesManager::getBlockDescriptions(SchemesManager::getBlockTypes(DESCR_SL), DESCR_SL);
$position = 10;
$skeleton_block_sizes = [
'default_height' =>[
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => $position,
'value' => [
'desktop' => 0,
'tablet' => 0,
'mobile' => 0,
],
]
];
foreach ($block_types as $block_type) {
if(fn_abt__ut2_is_block_lazy_load_allowed($block_type)){
$skeleton_block_sizes[$block_type['type']] = [
'label' => $block_type['name'],
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => ++$position,
'value' => [
'desktop' => 0,
'tablet' => 0,
'mobile' => 0,
],
];
}
}
$schema = [
'general' => [
'position' => 100,
'items' => [
'brand_feature_id' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 100,
'value' => 18,
'is_for_all_devices' => YesNo::YES,
],
'blog_page_id' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 150,
'value' => '',
'is_for_all_devices' => YesNo::YES,
],
'block_loader_additional_margin' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 190,
'value' => 1000,
'is_for_all_devices' => YesNo::YES,
],
'skeleton_sizes' => [
'is_group' => YesNo::YES,
'is_collapsed' => true,
'position' => 100,
'items' => $skeleton_block_sizes
],
'price_format' => [
'type' => 'selectbox',
'position' => 400,
'value' => 'superscript_decimals',
'class' => 'input-large',
'is_for_all_devices' => YesNo::YES,
'variants' => [
'default' => ['tooltip' => 'abt__ut2.settings.general.price_format.variants.default.tooltip'],
'superscript_decimals' => ['tooltip' => 'abt__ut2.settings.general.price_format.variants.superscript_decimals.tooltip'],
'subscript_decimals' => ['tooltip' => 'abt__ut2.settings.general.price_format.variants.subscript_decimals.tooltip'],
],
],
'bfcache' => [
'type' => 'checkbox',
'position' => 500,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'check_clone_theme' => [
'type' => 'checkbox',
'position' => 10000,
'value' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
'change_main_image_on_variation_hover' => [
'type' => 'checkbox',
'position' => 200,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
'disabled' => [
'mobile' => YesNo::YES,
'tablet' => YesNo::YES,
],
],
'top_sticky_panel' => [
'is_group' => YesNo::YES,
'position' => 100,
'items' => [
'enable' => [
'type' => 'checkbox',
'position' => 200,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
]
],
'sticky_panel' => [
'is_group' => YesNo::YES,
'position' => 200,
'enable_position_fields' => 'Y',
'items' => [
'enable_sticky_panel' => [
'type' => 'checkbox',
'position' => 10,
'exclude_position_field' => 'Y',
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::YES,
],
'disabled' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
],
],
'enable_sticky_panel_labels' => [
'type' => 'checkbox',
'position' => 20,
'exclude_position_field' => 'Y',
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::YES,
],
'disabled' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
],
],
'link_home' => [
'type' => 'checkbox',
'position' => 100,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
'disabled' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
],
],
'catalog' => [
'type' => 'checkbox',
'position' => 200,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::YES,
],
'disabled' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
],
],
'search' => [
'type' => 'checkbox',
'position' => 300,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::YES,
],
'disabled' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
],
],
'cart' => [
'type' => 'checkbox',
'position' => 400,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::YES,
],
'disabled' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
],
],
'wishlist' => [
'type' => 'checkbox',
'position' => 500,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
'disabled' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
],
],
'comparison' => [
'type' => 'checkbox',
'position' => 600,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
'disabled' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
],
],
'account' => [
'type' => 'checkbox',
'position' => 700,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::YES,
],
'disabled' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
],
],
'phones' => [
'type' => 'checkbox',
'position' => 800,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::YES,
],
'disabled' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
],
],
'sticky_panel_contacts_block_id' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 900,
'value' => '',
'is_for_all_devices' => YesNo::YES,
],
],
],
'mobile_tooltip' => [
'type' => 'checkbox',
'position' => 700,
'value' => YesNo::NO,
'is_for_all_devices' => YesNo::YES,
],
],
],
'category' => [
'position' => 150,
'items' => [
'show_subcategories' => [
'type' => 'checkbox',
'position' => 100,
'value' => YesNo::NO,
'is_for_all_devices' => YesNo::YES,
],
'description_position' => [
'type' => 'selectbox',
'position' => 200,
'class' => 'input-large',
'value' => 'bottom',
'variants' => [
'bottom',
'top',
'none',
],
'variants_as_language_variable' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
'show_sticky_panel_filters_and_categories' => [
'type' => 'checkbox',
'position' => 50,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
],
],
'features' => [
'position' => 175,
'items' => [
'description_position' => [
'type' => 'selectbox',
'position' => 200,
'class' => 'input-large',
'value' => 'bottom',
'variants' => [
'bottom',
'top',
'none',
],
'variants_as_language_variable' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
],
],
'product_list' => [
'position' => 200,
'items' => [
'decolorate_out_of_stock_products' => [
'type' => 'checkbox',
'position' => 20,
'value' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
'price_display_format' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 30,
'value' => 'col',
'variants' => [
'col',
'row',
'mix',
'row-mix',
'col-rev',
'col-rev-mix',
'col-os-fill',
'row-os-fill',
],
'is_for_all_devices' => YesNo::YES,
],
'price_position_top' => [
'type' => 'checkbox',
'position' => 40,
'value' => YesNo::NO,
'is_for_all_devices' => YesNo::YES,
],
'show_rating' => [
'type' => 'checkbox',
'position' => 50,
'value' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
'show_rating_num' => [
'type' => 'checkbox',
'position' => 51,
'value' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
'show_cart_status' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 55,
'value' => 'not-show',
'variants' => [
'not-show',
'check-icon',
'counter',
],
'is_for_all_devices' => YesNo::YES,
],
'show_favorite_compare_status' => [
'type' => 'checkbox',
'position' => 56,
'value' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
'max_features' => [
'type' => 'input',
'position' => 60,
'class' => 'input-small cm-value-integer',
'value' => [
'desktop' => 2,
'tablet' => 2,
'mobile' => 2,
],
],
'default_products_view' => [
'type' => 'selectbox',
'position' => 70,
'class' => 'input-large',
'value' => [
'desktop' => $default_products_view,
'tablet' => $default_products_view,
'mobile' => $default_products_view,
],
'variants' => array_keys(fn_get_products_views(true, true)),
],
'button_wish_list_view' => [
'type' => 'checkbox',
'position' => 71,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'button_compare_view' => [
'type' => 'checkbox',
'position' => 72,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'hover_buttons_w_c_q' => [
'type' => 'checkbox',
'position' => 73,
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'show_you_save' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 74,
'variants' => [
'none',
'full',
'short',
],
'value' => [
'desktop' => 'short',
'tablet' => 'short',
'mobile' => 'none',
],
],
'products_multicolumns' => [
'is_group' => YesNo::YES,
'position' => 100,
'items' => [
'image_width' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 103,
'value' => [
'desktop' => '243',
'tablet' => '243',
'mobile' => '200',
],
],
'image_height' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 102,
'value' => [
'desktop' => '243',
'tablet' => '243',
'mobile' => '200',
],
],
'lines_number_in_name_product' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 103,
'value' => [
'desktop' => '1',
'tablet' => '2',
'mobile' => '2',
],
'variants' => [
'1',
'2',
'3',
'4',
],
'variants_as_language_variable' => YesNo::YES,
],
'show_sku' => [
'type' => 'checkbox',
'position' => 110,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'show_amount' => [
'type' => 'checkbox',
'position' => 115,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_qty' => [
'type' => 'checkbox',
'position' => 120,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::NO,
],
],
'show_button_add_to_cart' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 130,
'value' => [
'desktop' => 'icon_button',
'tablet' => 'icon_button',
'mobile' => 'icon_button',
],
'variants' => [
'none',
'icon',
'icon_button',
'text',
'icon_and_text',
],
'variants_as_language_variable' => YesNo::YES,
],
'show_buttons_on_hover' => [
'type' => 'checkbox',
'position' => 140,
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'grid_item_bottom_content' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 150,
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
'value' => [
'desktop' => 'features_and_description',
'tablet' => 'none',
'mobile' => 'none',
],
'variants' => [
'none',
'description',
'features',
'variations',
'features_and_description',
'features_and_variations',
],
'variants_as_language_variable' => YesNo::YES,
],
'show_content_on_hover' => [
'type' => 'checkbox',
'position' => 155,
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'show_brand_logo' => [
'type' => 'checkbox',
'position' => 160,
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::YES,
],
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'show_gallery' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 179,
'variants' => [
'N',
'points',
'arrows',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
'value' => [
'desktop' => 'points',
'tablet' => 'points',
'mobile' => 'points',
],
],
'enable_hover_gallery' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 180,
'variants' => [
'N',
'points',
'lines',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'value' => [
'desktop' => 'lines',
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
],
],
'products_without_options' => [
'is_group' => YesNo::YES,
'position' => 110,
'items' => [
'image_width' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 103,
'value' => [
'desktop' => '240',
'tablet' => '200',
'mobile' => '300',
],
],
'image_height' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 102,
'value' => [
'desktop' => '240',
'tablet' => '200',
'mobile' => '300',
],
],
'show_sku' => [
'type' => 'checkbox',
'position' => 170,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_amount' => [
'type' => 'checkbox',
'position' => 180,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_qty' => [
'type' => 'checkbox',
'position' => 190,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_button_add_to_cart' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 130,
'value' => [
'desktop' => 'text',
'tablet' => 'text',
'mobile' => 'text',
],
'variants' => [
'none',
'icon_button',
'text',
'icon_and_text',
],
'variants_as_language_variable' => YesNo::YES,
],
'grid_item_bottom_content' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 200,
'value' => [
'desktop' => 'features_and_variations',
'tablet' => 'none',
'mobile' => 'none',
],
'variants' => [
'none',
'features',
'variations',
'features_and_variations',
],
'variants_as_language_variable' => YesNo::YES,
],
'show_options' => [
'type' => 'checkbox',
'position' => 210,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_brand_logo' => [
'type' => 'checkbox',
'position' => 220,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_gallery' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 229,
'variants' => [
'N',
'points',
'arrows',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
'value' => [
'desktop' => YesNo::NO,
'tablet' => 'points',
'mobile' => 'points',
],
],
'enable_hover_gallery' => [
'type' => 'selectbox',
'position' => 230,
'class' => 'input-large',
'variants' => [
'N',
'points',
'lines',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'value' => [
'desktop' => 'lines',
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
],
],
'short_list' => [
'is_group' => YesNo::YES,
'position' => 120,
'items' => [
'image_width' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 103,
'value' => [
'desktop' => '100',
'tablet' => '100',
'mobile' => '85',
],
],
'image_height' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 102,
'value' => [
'desktop' => '100',
'tablet' => '100',
'mobile' => '85',
],
],
'show_two_columns' => [
'type' => 'checkbox',
'position' => 103,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::YES,
],
],
'show_sku' => [
'type' => 'checkbox',
'position' => 230,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'show_amount' => [
'type' => 'checkbox',
'position' => 235,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_qty' => [
'type' => 'checkbox',
'position' => 240,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_button_add_to_cart' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 255,
'value' => [
'desktop' => 'icon_button',
'tablet' => 'icon_button',
'mobile' => 'icon_button',
],
'variants' => [
'none',
'icon',
'icon_button',
],
'variants_as_language_variable' => YesNo::YES,
],
],
],
'small_items' => [
'is_group' => YesNo::YES,
'position' => 130,
'items' => [
'lines_number_in_name_product' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 103,
'value' => [
'desktop' => '2',
'tablet' => '2',
'mobile' => '2',
],
'variants' => [
'1',
'2',
'3',
'4',
],
'variants_as_language_variable' => YesNo::YES,
],
'show_sku' => [
'type' => 'checkbox',
'position' => 230,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'show_amount' => [
'type' => 'checkbox',
'position' => 115,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_qty' => [
'type' => 'checkbox',
'position' => 240,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'show_button_add_to_cart' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 130,
'value' => [
'desktop' => 'icon_button',
'tablet' => 'icon_button',
'mobile' => 'icon_button',
],
'variants' => [
'icon',
'icon_button',
'text',
'icon_and_text',
],
'variants_as_language_variable' => YesNo::YES,
],
],
],
'products_scroller' => [
'is_group' => YesNo::YES,
'position' => 140,
'items' => [
'lines_number_in_name_product' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 103,
'value' => [
'desktop' => '1',
'tablet' => '1',
'mobile' => '1',
],
'variants' => [
'1',
'2',
'3',
],
'variants_as_language_variable' => YesNo::YES,
],
'show_amount' => [
'type' => 'checkbox',
'position' => 115,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_qty' => [
'type' => 'checkbox',
'position' => 120,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'show_quick_view_button' => [
'type' => 'checkbox',
'position' => 130,
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'show_button_add_to_cart' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 135,
'value' => [
'desktop' => 'text',
'tablet' => 'text',
'mobile' => 'icon_button',
],
'variants' => [
'icon',
'icon_button',
'text',
'icon_and_text',
],
'variants_as_language_variable' => YesNo::YES,
],
],
],
'product_variations' => [
'is_group' => YesNo::YES,
'position' => 200,
'items' => [
'limit' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 20,
'value' => '10',
'is_for_all_devices' => YesNo::YES,
],
'display_as_links' => [
'type' => 'checkbox',
'position' => 30,
'value' => YesNo::NO,
'is_for_all_devices' => YesNo::YES,
],
'allow_variations_selection' => [
'type' => 'checkbox',
'position' => 30,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'is_for_all_devices' => YesNo::NO,
],
],
],
],
],
'products' => [
'position' => 300,
'items' => [
'custom_block_id' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 100,
'value' => '',
'is_for_all_devices' => YesNo::YES,
],
'search_similar_in_category' => [
'type' => 'checkbox',
'position' => 200,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'highlight_unavailable_variations' => [
'type' => 'checkbox',
'position' => 300,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'view' => [
'is_group' => YesNo::YES,
'position' => 40,
'items' => [
'image_width' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 1,
'value' => [
'desktop' => '570',
'tablet' => '430',
'mobile' => '430',
],
],
'image_height' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 2,
'value' => [
'desktop' => '570',
'tablet' => '430',
'mobile' => '430',
],
],
'brand_link_behavior' => [
'type' => 'selectbox',
'position' => 10,
'class' => 'input-large',
'value' => 'to_category_with_filter',
'variants' => [
'to_brand_page',
'to_category_with_filter',
],
'is_for_all_devices' => YesNo::YES,
],
'show_qty' => [
'type' => 'checkbox',
'position' => 20,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_sku' => [
'type' => 'checkbox',
'position' => 30,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_features' => [
'type' => 'checkbox',
'position' => 40,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'show_features_in_two_col' => [
'type' => 'checkbox',
'position' => 50,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
],
'show_short_description' => [
'type' => 'checkbox',
'position' => 60,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
],
'show_you_save' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 65,
'variants' => [
'none',
'full',
'short',
],
'value' => [
'desktop' => 'short',
'tablet' => 'short',
'mobile' => 'short',
],
],
'show_brand_format' => [
'type' => 'selectbox',
'position' => 80,
'class' => 'input-large',
'value' => [
'desktop' => 'logo',
'tablet' => 'logo',
'mobile' => 'logo',
],
'variants' => [
'none',
'name',
'logo',
],
],
'thumbnails_gallery_format' => [
'type' => 'selectbox',
'position' => 90,
'class' => 'input-large',
'disabled' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::NO,
],
'value' => [
'desktop' => 'default',
'tablet' => 'default',
'mobile' => 'lines_only',
],
'variants' => [
'default',
'counter_only',
'lines_only',
],
],
'show_sticky_panel_add_to_cart' => [
'type' => 'selectbox',
'position' => 100,
'class' => 'input-large',
'value' => [
'desktop' => 'top',
'tablet' => 'top',
'mobile' => 'bottom',
],
'variants' => [
'none',
'top',
'bottom',
],
],
],
],
'default_template' => [
'is_group' => YesNo::YES,
'position' => 50,
'items' => [
'multiple_product_images' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 10,
'value' => [
'desktop' => '1',
'tablet' => '1',
'mobile' => '1',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'variants' => [
'1',
'2',
],
],
]
],
'bigpicture_template' => [
'is_group' => YesNo::YES,
'position' => 60,
'items' => [
'multiple_product_images' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 10,
'value' => [
'desktop' => '1',
'tablet' => '1',
'mobile' => '1',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'variants' => [
'1',
'2',
],
],
]
],
'abt__ut2_bigpicture_flat_template' => [
'is_group' => YesNo::YES,
'position' => 70,
'items' => [
'multiple_product_images' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 10,
'value' => [
'desktop' => '1',
'tablet' => '1',
'mobile' => '1',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'variants' => [
'1',
'2',
],
],
]
],
'abt__ut2_bigpicture_gallery_template' => [
'is_group' => YesNo::YES,
'position' => 80,
'items' => [
'multiple_product_images' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 10,
'value' => [
'desktop' => '3',
'tablet' => '1',
'mobile' => '1',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'variants' => [
'1',
'2',
'3',
],
],
]
],
'abt__ut2_cascade_gallery_template' => [
'is_group' => YesNo::YES,
'position' => 85,
'items' => [
'formation_multiple_product_images' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 10,
'value' => [
'desktop' => '1',
'tablet' => '1',
'mobile' => '1',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'variants' => [
'1',
'2',
'3',
],
],
]
],
'abt__ut2_three_columns_template' => [
'is_group' => YesNo::YES,
'position' => 90,
'items' => [
'multiple_product_images' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 10,
'value' => [
'desktop' => '1',
'tablet' => '1',
'mobile' => '1',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'variants' => [
'1',
'2',
],
],
]
],
'addon_buy_together' => [
'is_group' => YesNo::YES,
'position' => 200,
'items' => [
'view' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 150,
'value' => 'as_block_above_tabs',
'variants' => [
'as_block_above_tabs',
'as_tab_in_tabs',
],
'variants_as_language_variable' => YesNo::YES,
'is_addon_dependent' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
],
],
'addon_required_products' => [
'is_group' => YesNo::YES,
'position' => 300,
'items' => [
'list_type' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 150,
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'value' => [
'desktop' => 'grid_list',
'tablet' => 'grid_list',
'mobile' => 'grid_list',
],
'variants' => [
'grid_list',
'compact_list',
'product_list',
],
'variants_as_language_variable' => YesNo::YES,
'is_addon_dependent' => YesNo::YES,
],
'item_quantity' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 100,
'value' => [
'desktop' => 6,
'tablet' => 4,
'mobile' => 2,
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'is_addon_dependent' => YesNo::YES,
],
],
],
'addon_social_buttons' => [
'is_group' => YesNo::YES,
'position' => 400,
'items' => [
'view' => [
'type' => 'checkbox',
'position' => 100,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'is_addon_dependent' => YesNo::YES,
],
],
],
],
],
'load_more' => [
'position' => 400,
'items' => [
'product_list' => [
'type' => 'checkbox',
'position' => 100,
'value' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
'blog' => [
'type' => 'checkbox',
'position' => 200,
'value' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
'mode' => [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 300,
'value' => [
'desktop' => 'on_button_click',
'tablet' => 'auto',
'mobile' => 'auto',
],
'variants' => [
'auto',
'on_button_click',
'semi_auto'
],
'is_for_all_devices' => YesNo::NO,
],
'show_products_num' => [
'type' => 'checkbox',
'position' => 400,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'is_for_all_devices' => YesNo::NO,
],
],
],
'addons' => [
'position' => 10000,
'items' => [
'wishlist_products' => [
'is_group' => YesNo::YES,
'position' => 100,
'items' => [
'item_quantity' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 100,
'value' => [
'desktop' => 6,
'tablet' => 4,
'mobile' => 2,
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'is_addon_dependent' => YesNo::YES,
],
],
],
'discussion' => [
'is_group' => YesNo::YES,
'position' => 300,
'items' => [
'highlight_administrator' => [
'type' => 'checkbox',
'position' => 100,
'value' => YesNo::NO,
'is_for_all_devices' => YesNo::YES,
'is_addon_dependent' => YesNo::YES,
],
'verified_buyer' => [
'type' => 'checkbox',
'position' => 200,
'value' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
'is_addon_dependent' => YesNo::YES,
],
],
],
],
],
];
return $schema;
