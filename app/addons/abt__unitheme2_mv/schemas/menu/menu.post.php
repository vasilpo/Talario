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
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
$schema['central']['ab__addons']['items']['abt__unitheme2_mv'] = [
'attrs' => [
'class' => 'is-addon',
],
'href' => 'abt__ut2_mv.help',
'position' => 100,
'subitems' => [
'abt__ut2_mv.vendor_settings_tab' => [
'href' => 'abt__ut2.settings&selected_section=vendor',
'position' => 100,
],
'abt__ut2_mv.product_page_vendor_settings' => [
'href' => 'abt__ut2.settings&selected_section=products#products_vendor_group',
'position' => 200,
],
'abt__ut2_mv.help' => [
'href' => 'abt__ut2_mv.help',
'position' => 300,
],
],
];
return $schema;
