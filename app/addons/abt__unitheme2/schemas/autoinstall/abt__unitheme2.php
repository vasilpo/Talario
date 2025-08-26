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
use Tygh\Registry;
$addon = basename(__FILE__, '.php');
$style = 'Default';
$styles = glob(Registry::get('config.dir.themes_repository') . $addon . '/styles/data/*.less');
if (!empty($styles)) {
array_walk($styles, function (&$style, $key) {
$style = basename($style, '.less');
});
$style = $styles[array_rand($styles)];
}
$path = Registry::get('config.dir.addons') . 'abt__unitheme2/schemas/autoinstall/';
require_once ($path . 'install_demodata.functions.php');
require_once ($path . 'install_extradata.functions.php');
require_once ($path . 'install_theme.functions.php');
$schema = [
'version' => '2.0',
'steps' => [
'install_theme' => [
'type' => 'function',
'params' => [
'function_name' => 'fn_abt__ut2_autoinstall_theme',
'arguments' => [$addon, $style],
],
],
'install_demodata' => [
'type' => 'function',
'params' => [
'function_name' => 'fn_abt__ut2_autoinstall_demodata',
'arguments' => [],
],
],
'install_fly_menu' => [
'type' => 'function',
'params' => [
'function_name' => 'fn_abt__ut2_demodata_add_fly_menu',
'arguments' => [],
],
],
'autoinstall_extradata_menu' => [
'type' => 'function',
'params' => [
'function_name' => 'fn_abt__ut2_autoinstall_extradata_menu',
'arguments' => [],
],
],
'autoinstall_extradata_blog' => [
'type' => 'function',
'params' => [
'function_name' => 'fn_abt__ut2_autoinstall_extradata_blog',
'arguments' => ['7'],
],
],
],
];
return $schema;
