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
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');
if (Registry::get('addons.vendor_plans.status') === ObjectStatuses::ACTIVE) {
$schema['dynamic']['conditions']['vendor_plans'] = [
'operators' => ['eq', 'neq'],
'type' => 'select',
'variants_function' => ['fn_ab__stickers_get_simple_vendor_plans'],
'validate_function' => ['fn_ab__stickers_validate_vendor_plans'],
];
}
$schema['dynamic']['conditions']['company_id'] = $schema['constant']['conditions']['company_id'] = [
'operators' => ['in', 'nin'],
'type' => 'picker',
'picker_props' => [
'picker' => 'pickers/companies/picker.tpl',
'params' => [
'multiple' => true,
'use_keys' => 'N',
'view_mode' => 'table',
],
],
'validate_function' => ['fn_ab__stickers_validate_companies'],
];
return $schema;
