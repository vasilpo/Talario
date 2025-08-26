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
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
Tygh::$app->register(new \Tygh\Addons\Abt_unitheme2\ServiceProvider());
fn_register_hooks(// define settings
'dispatch_assign_template'
, 'render_block_pre'
, 'render_blocks', 'render_block_content_after', 'render_block_post'
, 'get_banners'
, 'get_banners_post'
, 'get_banner_data'
, 'get_banner_data_post'
, 'delete_banners'
, 'update_language_post'
, 'delete_languages_post'
, 'banners_update_banner_pre'
, 'update_static_data', 'get_static_data', 'top_menu_form_post'
, 'get_products_pre'
, 'get_products_post'
, 'description_tables_post'
, 'get_product_features', 'get_product_features_post', 'get_product_feature_variants'
, 'seo_get_schema_org_markup_items_post'
, 'get_products_layout_pre'
, 'get_discussion_posts', 'get_discussion_posts_post'
, 'get_categories_pre'
,'init_templater_post'
,'get_filters_products_count_before_select_filters'
,'get_cities_pre'
,'get_products'
,'dispatch_before_display'
,'get_grids_post'
,'redirect_complete'
,'filter_uploaded_data_post'
,'update_image'
,'generate_thumbnail_post'
,'get_image_pairs_post'
,'get_products_layout_post'
);
