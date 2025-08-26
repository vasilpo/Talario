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
defined('BOOTSTRAP') or die('Access denied');
$schema['product_list']['items']['products_multicolumns']['items']['ab__s_pictogram_position'] = [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 200,
'is_addon_dependent' => true,
'variants' => [
YesNo::NO,
'position_1',
'position_2',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::YES,
],
'value' => [
'desktop' => 'position_1',
'tablet' => 'position_1',
'mobile' => YesNo::NO,
],
];
$schema['product_list']['items']['products_without_options']['items']['ab__s_pictogram_position'] = [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 200,
'is_addon_dependent' => true,
'variants' => [
YesNo::NO,
'position_1',
'position_2',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
'value' => [
'desktop' => 'position_1',
'tablet' => 'position_1',
'mobile' => 'position_1',
],
];
$schema['product_list']['items']['short_list']['items']['ab__s_pictogram_position'] = [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 200,
'is_addon_dependent' => true,
'variants' => [
YesNo::NO,
'position_1',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
'value' => [
'desktop' => 'position_1',
'tablet' => 'position_1',
'mobile' => 'position_1',
],
];
$schema['products']['items']['view']['items']['ab__s_pictogram_position'] = [
'type' => 'selectbox',
'class' => 'input-large',
'position' => 200,
'is_addon_dependent' => true,
'variants' => [
YesNo::NO,
'position_1',
'position_2',
],
'disabled' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
'value' => [
'desktop' => 'position_1',
'tablet' => 'position_1',
'mobile' => 'position_1',
],
];
$schema['product_list']['items']['ab__stickers'] = [
'is_group' => YesNo::YES,
'position' => 500,
'items' => [
'hide_stickers_on_hover' => [
'type' => 'checkbox',
'position' => 100,
'value' => [
'desktop' => YesNo::YES,
'tablet' => YesNo::YES,
'mobile' => YesNo::YES,
],
'is_addon_dependent' => YesNo::YES,
]
]
];
$schema['products']['items']['ab__stickers'] = [
'is_group' => YesNo::YES,
'position' => 500,
'items' => [
'hide_stickers_on_hover' => [
'type' => 'checkbox',
'position' => 100,
'value' => [
'desktop' => YesNo::NO,
'tablet' => YesNo::NO,
'mobile' => YesNo::NO,
],
'is_addon_dependent' => YesNo::YES,
]
]
];
return $schema;
