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
namespace Tygh\Enum\Addons\Abt_unitheme2;

class DeviceTypes
{

const ALL = 'all';

const DESKTOP = 'desktop';

const TABLET = 'tablet';

const MOBILE = 'mobile';

public static function getList(array $exclude = [self::ALL])
{
$types = [
self::ALL => self::ALL,
self::DESKTOP => self::DESKTOP,
self::TABLET => self::TABLET,
self::MOBILE => self::MOBILE,
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
