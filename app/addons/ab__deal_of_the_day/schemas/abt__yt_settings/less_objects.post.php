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
$schema['addons']['items']['ab__deal_of_the_day'] = [
'position' => 100,
'is_group' => YesNo::YES,
'items' => [
'block_background' => [
'is_for_all_devices' => YesNo::YES,
'type' => 'colorpicker',
'position' => 100,
'value' => '#ffffff',
'value_styles' => [
'Bee.less' => '#ffffff',
'Bluor.less' => '#ffffff',
'Coral.less' => '#ffffff',
'Grass.less' => '#ffffff',
'Original.less' => '#ffffff',
'Plum.less' => '#ffffff',
'Sea.less' => '#ffffff',
'Sunset.less' => '#ffffff',
],
],
'block_fonts_color' => [
'is_for_all_devices' => YesNo::YES,
'type' => 'colorpicker',
'position' => 100,
'value' => '#000000',
'value_styles' => [
'Bee.less' => '#232323',
'Bluor.less' => '#232323',
'Coral.less' => '#202021',
'Grass.less' => '#363636',
'Original.less' => '#28282f',
'Plum.less' => '#363636',
'Sea.less' => '#363636',
'Sunset.less' => '#363636',
],
],
'bright_color_promotion_title' => [
'is_for_all_devices' => YesNo::YES,
'type' => 'checkbox',
'position' => 100,
'value' => YesNo::YES,
'value_styles' => [
'Bee.less' => YesNo::YES,
'Bluor.less' => YesNo::YES,
'Coral.less' => YesNo::YES,
'Grass.less' => YesNo::YES,
'Original.less' => YesNo::YES,
'Plum.less' => YesNo::YES,
'Sea.less' => YesNo::YES,
'Sunset.less' => YesNo::YES,
],
],
'bordered_block' => [
'is_for_all_devices' => YesNo::YES,
'type' => 'checkbox',
'position' => 100,
'value' => YesNo::NO,
'value_styles' => [
'Bee.less' => YesNo::NO,
'Bluor.less' => YesNo::NO,
'Coral.less' => YesNo::NO,
'Grass.less' => YesNo::NO,
'Original.less' => YesNo::NO,
'Plum.less' => YesNo::NO,
'Sea.less' => YesNo::NO,
'Sunset.less' => YesNo::NO,
],
],
'shadow_block' => [
'is_for_all_devices' => YesNo::YES,
'type' => 'checkbox',
'position' => 100,
'value' => YesNo::NO,
'value_styles' => [
'Bee.less' => YesNo::NO,
'Bluor.less' => YesNo::NO,
'Coral.less' => YesNo::NO,
'Grass.less' => YesNo::NO,
'Original.less' => YesNo::NO,
'Plum.less' => YesNo::NO,
'Sea.less' => YesNo::NO,
'Sunset.less' => YesNo::NO,
],
],
'rounded_corners_blocks' => [
'is_for_all_devices' => YesNo::YES,
'type' => 'checkbox',
'position' => 100,
'value' => YesNo::NO,
'value_styles' => [
'Bee.less' => YesNo::NO,
'Bluor.less' => YesNo::NO,
'Coral.less' => YesNo::NO,
'Grass.less' => YesNo::NO,
'Original.less' => YesNo::NO,
'Plum.less' => YesNo::NO,
'Sea.less' => YesNo::NO,
'Sunset.less' => YesNo::NO,
],
],
],
];
return $schema;
