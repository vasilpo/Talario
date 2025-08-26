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

class BlockInTabsTypes
{

const WITHOUT_TABS = 'no';

const TABS_WITHOUT_LAZY_LOAD = 'without_lazy_load';

const TABS_WITH_LAZY_LOAD = 'with_lazy_load';

public static function getList() : array
{
return [
self::WITHOUT_TABS => self::WITHOUT_TABS,
self::TABS_WITHOUT_LAZY_LOAD => self::TABS_WITHOUT_LAZY_LOAD,
self::TABS_WITH_LAZY_LOAD => self::TABS_WITH_LAZY_LOAD,
];
}

public static function getAll() : array
{
return self::getList();
}
}
