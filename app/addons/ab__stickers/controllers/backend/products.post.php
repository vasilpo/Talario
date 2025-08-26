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
use Tygh\Enum\Addons\Ab_stickers\StickerTypes;
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}
if (Registry::get('runtime.mode') === 'update' && fn_check_permissions('ab__stickers', 'view', 'admin')) {
$tabs = Registry::get('navigation.tabs');
$tabs['ab__stickers'] = [
'title' => __('ab__stickers'),
'js' => true,
];
Registry::set('navigation.tabs', $tabs);

$repository = Tygh::$app['addons.ab__stickers.repository'];
list($stickers, $search) = $repository->find(['get_icons' => false, 'type' => StickerTypes::CONSTANT]);
Tygh::$app['view']->assign('ab__stickers', $stickers);
}
