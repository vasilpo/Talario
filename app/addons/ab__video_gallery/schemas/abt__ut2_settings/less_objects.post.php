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
$schema['addons']['items']['ab__video_gallery'] = [
'is_group' => YesNo::YES,
'position' => 10,
'items' => [
'block_bg' => [
'is_for_all_devices' => YesNo::YES,
'type' => 'colorpicker',
'position' => 100,
'value' => '',
'value_styles' => [
'Black.less' => '#1e1e1e',
'Blue.less' => '#646464',
'Brick.less' => '#646464',
'Cobalt.less' => '#454545',
'Dark_Blue.less' => '#24488e',
'Dark_Navy.less' => '#4a5259',
'Default.less' => '#1e1e1e',
'Fiolent.less' => '#4651a7',
'Flame.less' => '#777777',
'Gray.less' => '#626262',
'Green.less' => '#646464',
'Indigo.less' => '#00ac89',
'Ink.less' => '#808080',
'Malachite' => '#1e1e1e',
'Mint.less' => '#637878',
'Orange.less' => '#1d1d1f',
'Original.less' => '#646464',
'Powder.less' => '#333333',
'Purple.less' => '#009f92',
'Skyfall.less' => '#1e1e1e',
'Velvet.less' => '#666666',
'White.less' => '#2e2e2e',
],
],
],
];
return $schema;
