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
namespace Tygh\Addons\Abt_unitheme2\SmartyEngine\Extensions;
use Smarty\BlockHandler\BlockHandlerInterface;
use Smarty\Extension\Base;
use Tygh\Addons\Abt_unitheme2\SmartyEngine\Blocks\AbHideContent;
use Tygh\Addons\Abt_unitheme2\SmartyEngine\Filters\Post\ReplaceImageGallery;
use Tygh\Addons\Abt_unitheme2\SmartyEngine\Filters\Pre\AddHook;
use Tygh\Enum\SiteArea;
class AbtUnitheme2 extends Base
{
private array $block_handlers = [];
public function getBlockHandler(string $blockTagName): ?BlockHandlerInterface
{
if (isset($this->block_handlers[$blockTagName])) {
return $this->block_handlers[$blockTagName];
}
if($blockTagName === 'ab__hide_content'){
$this->block_handlers[$blockTagName] = new AbHideContent();
}
return $this->block_handlers[$blockTagName] ?? null;
}
public function getPostFilters(): array
{
$filters = [];
if (SiteArea::isStorefront(AREA) && fn_get_theme_path('[theme]') === 'abt__unitheme2') {
$filters[] = new ReplaceImageGallery();
}
return $filters;
}
public function getPreFilters(): array
{
$filters = [];
if(SiteArea::isAdmin(AREA)){
$filters[] = new AddHook();
}
return $filters;
}
}
