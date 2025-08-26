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
function fn_abt__unitheme2_mv_fn_abt__ut2_check_versions_post(&$arr)
{
$arr['multivendor'] = fn_get_addon_version('abt__unitheme2_mv');
}
function fn_abt__ut2_mv_copy_layouts()
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
function fn_abt__ut2_mv_remove_cscart_layouts()
{
$layouts = db_get_fields('SELECT name FROM ?:bm_layouts WHERE theme_name = \'abt__unitheme2\'');
if (empty($layouts)) {
foreach (['themes_repository', 'design_frontend'] as $dir) {
$ultimate_layouts = glob(Registry::get("config.dir.{$dir}") . 'abt__unitheme2/layouts/layouts_ultimate_*.xml');
foreach ($ultimate_layouts as $ultimate_layout) {
fn_rm($ultimate_layout);
}
}
}
}
