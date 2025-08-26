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
use Tygh\Enum\YesNo;
$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'ab__video_gallery';
$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'ab__video_gallery_descriptions';
$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'ab__video_gallery_settings';
$schema['ab__vg_videos'] = [
'content' => [
'videos' => [
'type' => 'function',
'function' => ['fn_ab__vg_videos_get_videos'],
],
],
'settings' => [
'ab__vg_max_videos' => [
'type' => 'input',
'default_value' => 4,
],
],
'templates' => [
'addons/ab__video_gallery/blocks/ab__video_gallery/ab__vg_videos.tpl' => [
'settings' => [
'number_of_columns' => [
'type' => 'selectbox',
'default_value' => 4,
'values' => array (
'3' => 'ab__vg.3',
'4' => 'ab__vg.4',
'5' => 'ab__vg.5',
'6' => 'ab__vg.6',
'7' => 'ab__vg.7',
'8' => 'ab__vg.8',
'9' => 'ab__vg.9',
),
],
'ab__vg_show_product_link' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'ab__vg_show_video_title' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
],
'ab__vg_show_video_description' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'ab__vg_image_width' => [
'type' => 'input',
'default_value' => '320',
],
'ab__vg_image_height' => [
'type' => 'input',
'default_value' => '240',
],
],
],
],
'wrappers' => 'blocks/wrappers',
'show_on_locations' => ['categories.view'],
'brief_info_function' => 'fn_ab__vg_videos_get_block_info',
'multilanguage' => false,
'cache' => [
'update_handlers' => ['ab__video_gallery', 'ab__video_gallery_descriptions', 'products_categories'],
'request_handlers' => ['category_id', 'company_id'],
'disable_cache_when' => [
'request_handlers' => ['features_hash'],
],
],
];
return $schema;
