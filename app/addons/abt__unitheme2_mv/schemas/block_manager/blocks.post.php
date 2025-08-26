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
use Tygh\Enum\YesNo;
use Tygh\Enum\ObjectStatuses;
$schema['abt__ut2_mv_vendor_subcategories_menu'] = [
'templates' => 'addons/abt__unitheme2_mv/blocks/abt__ut2_mv_vendor_subcategories_menu.tpl',
'content' => [
'abt__ut2_subcategories' => [
'type' => 'function',
'function' => ['fn_abt__ut2_get_sub_or_parent_categories'],
],
],
'settings' => [
'abt__ut2_show_parents' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'abt__ut2_show_siblings' => [
'type' => 'checkbox',
'default_value' => YesNo::NO,
],
'abt__ut2_show_children' => [
'type' => 'checkbox',
'default_value' => YesNo::YES,
],
],
'wrappers' => 'blocks/wrappers',
'cache' => [
'update_handlers' => [
'categories',
'category_descriptions',
],
'request_handlers' => [
'current_category_id' => '%CATEGORY_ID%',
'company_id' => '%COMPANY_ID%',
],
],
'show_on_locations' => ['companies.products'],
];
return $schema;
