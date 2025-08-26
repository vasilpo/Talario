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
$schema['controllers']['ab__stickers'] = [
'permissions' => false,
'modes' => [
'change_vendor_status' => [
'permissions' => true,
],
],
];
if (Registry::ifGet('addons.vendor_privileges.status', ObjectStatuses::DISABLED) == ObjectStatuses::ACTIVE) {
$schema['controllers']['ab__stickers'] = fn_array_merge($schema['controllers']['ab__stickers'], [
'modes' => [
'view' => [
'permissions' => true,
],
'edit' => [
'permissions' => true,
],
'get_stickers_objects' => [
'permissions' => true,
],
],
], true);
}
return $schema;
