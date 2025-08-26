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

class StickerPlaces
{

const NOT_DISPLAY = 'not_display';

const PRODUCT_IMAGE = 'product_labels';

const PRICE_BEFORE = 'price.before';

const PRICE_AFTER = 'price.after';

public static function getList(array $exclude = [])
{
$types = [
self::NOT_DISPLAY => self::NOT_DISPLAY,
self::PRODUCT_IMAGE => self::PRODUCT_IMAGE,
self::PRICE_BEFORE => self::PRICE_BEFORE,
self::PRICE_AFTER => self::PRICE_AFTER
];
foreach ($exclude as $_tmp) {
unset($types[$_tmp]);
}
return $types;
}

public static function getAll()
{
return self::getList();
}
}
