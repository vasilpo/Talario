<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2023   *
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
$schema['addons']['items']['ab__landing_categories'] = [
'is_group' => YesNo::YES,
'position' => 400,
'items' => [
'columns_count' => [
'class' => 'input-large',
'type' => 'selectbox',
'position' => 100,
'value' => 4,
'variants' => range(3, 6),
'is_addon_dependent' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
'thumbnail_width' => [
'class' => 'input-large',
'type' => 'input',
'position' => 101,
'value' => 250,
'is_addon_dependent' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
'thumbnail_height' => [
'class' => 'input-large',
'type' => 'input',
'position' => 102,
'value' => 250,
'is_addon_dependent' => YesNo::YES,
'is_for_all_devices' => YesNo::YES,
],
],
];
return $schema;