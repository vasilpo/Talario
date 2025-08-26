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
use Tygh\Enum\Addons\Abt_unitheme2\BlockInTabsTypes;
if (!defined('BOOTSTRAP')) { die('Access denied'); }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}
if ($mode === 'update_grid') {
Tygh::$app['view']->assign('abt__ut2_block_in_tabs_types', BlockInTabsTypes::getAll());
} elseif ($mode === 'manage') {
$default_layouts_sources = Tygh::$app['view']->getTemplateVars('default_layouts_sources');
foreach($default_layouts_sources as &$default_layouts_source) {
if (stripos($default_layouts_source['name'], 'unitheme') !== false) {
$default_layouts_source['theme_name'] = 'abt__unitheme2';
}
}
Tygh::$app['view']->assign('default_layouts_sources', $default_layouts_sources);
}