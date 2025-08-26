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
namespace Tygh\Enum\Addons\Ab_stickers;

class PictogramPositions
{

const VERTICAL_TOP = 'VT';

const VERTICAL_MIDDLE = 'VM';

const VERTICAL_BOTTOM = 'VB';

const HORIZONTAL_LEFT = 'HL';

const HORIZONTAL_CENTER = 'HC';

const HORIZONTAL_RIGHT = 'HR';

public static function getList(array $exclude = [])
{
$positions = [
self::VERTICAL_TOP => self::VERTICAL_TOP,
self::VERTICAL_MIDDLE => self::VERTICAL_MIDDLE,
self::VERTICAL_BOTTOM => self::VERTICAL_BOTTOM,
self::HORIZONTAL_LEFT => self::HORIZONTAL_LEFT,
self::HORIZONTAL_CENTER => self::HORIZONTAL_CENTER,
self::HORIZONTAL_RIGHT => self::HORIZONTAL_RIGHT,
];
foreach ($exclude as $_tmp) {
unset($positions[$_tmp]);
}
return $positions;
}

public static function getVerticalList()
{
return self::getList([self::HORIZONTAL_LEFT, self::HORIZONTAL_CENTER, self::HORIZONTAL_RIGHT]);
}

public static function getHorizontalList()
{
return self::getList([self::VERTICAL_TOP, self::VERTICAL_MIDDLE, self::VERTICAL_BOTTOM]);
}

public static function getAll()
{
return self::getList();
}
}
