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
defined('BOOTSTRAP') || die('Access denied');
$device = Registry::get('settings.ab__device');
if (Registry::ifGet('settings.abt__ut2.general.bfcache.' . $device, 'N') === 'Y') {
if (in_array($controller, ['categories', 'products'])) {
header('Cache-Control: no-cache, must-revalidate');
}
}
if ($controller === 'index' && $mode === 'index' && $action === 'check_changes') {
if (function_exists('fn_abt__ut2_assign_cart_wl_compare_state')) {
fn_abt__ut2_assign_cart_wl_compare_state();
}
}
