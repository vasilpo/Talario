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
namespace Tygh\Addons\Ab_videoGallery\SmartyEngine\Extensions;
use Smarty\Extension\Base;
use Tygh\Addons\Ab_videoGallery\SmartyEngine\Filters\Post\ReplaceImageGallery;
use Tygh\Addons\Ab_videoGallery\SmartyEngine\Filters\Pre\AddStorefrontHooks;
use Tygh\Enum\SiteArea;
class AbVideoGallery extends Base
{
public function getPreFilters() : array
{
$filters = [];
if (SiteArea::isStorefront(AREA) && fn_ab__vg_get_theme_name() !== 'abt__unitheme2') {
$filters[] = new AddStorefrontHooks();
}
return $filters;
}
public function getPostFilters() : array
{
$filters = [];
if (SiteArea::isStorefront(AREA)) {
$filters[] = new ReplaceImageGallery();
}
return $filters;
}
}
