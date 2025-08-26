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
use Tygh\Enum\Addons\Ab_stickers\PictogramPositions;
use Tygh\Enum\ObjectStatuses;
defined('BOOTSTRAP') or die('Access denied');
$schema = [
'color' => 'rgba(0,0,0,1)',
'font' => [
'name' => fn_ab__stickers_pictograms_get_font('default\OpenSans-Regular.ttf', true),
'size' => 8,
],
'stroke' => [
'width' => 0,
'color' => 'rgba(255,255,255,1)',
],
'margin' => [
'horizontal' => 0,
'vertical' => 0,
],
'position' => [
'horizontal' => PictogramPositions::HORIZONTAL_LEFT,
'vertical' => PictogramPositions::VERTICAL_TOP,
],
'rotation' => 0,
'status' => ObjectStatuses::DISABLED,
];
return $schema;
