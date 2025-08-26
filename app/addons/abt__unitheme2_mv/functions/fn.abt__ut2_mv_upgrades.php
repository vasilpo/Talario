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
use Tygh\Registry;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
function fn_abt__ut2_mv_upgrades_copy_layouts()
{
$edition = 'multivendor';
$layouts = glob(Registry::get('config.dir.themes_repository') . "abt__unitheme2/layouts/layouts_{$edition}_*.xml");
if (!empty($layouts)) {
foreach (['responsive', 'abt__unitheme2'] as $theme) {
foreach ($layouts as $layout) {
$dir = Registry::get('config.dir.design_frontend') . $theme . '/layouts/';
if (is_dir($dir)) {
fn_copy($layout, $dir);
}
}
}
}
}
function fn_abt__ut2_mv_upgrades_set_notifications($version, $notifications = [])
{
if (!empty($notifications)) {
fn_set_notification('I', __('abt__ut2_mv.upgrade_notifications.title', ['[ver]' => $version]), Tygh::$app['view']->assign('ver', $version)
->assign('notifications', $notifications)
->fetch('addons/abt__unitheme2_mv/views/upgrade_notifications/list.tpl'), 'S');
}
}
