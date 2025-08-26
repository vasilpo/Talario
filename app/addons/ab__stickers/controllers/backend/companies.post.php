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
use Tygh\Enum\YesNo;
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
return [CONTROLLER_STATUS_OK];
}
if (Registry::get('runtime.mode') === 'update') {
if (
Registry::get('addons.ab__stickers.enable_for_vendors') === YesNo::YES && fn_allowed_for('MULTIVENDOR')
&& (fn_check_view_permissions('ab__stickers.view', 'GET') || Registry::ifGet('addons.vendor_privileges.status', ObjectStatuses::DISABLED) !== ObjectStatuses::ACTIVE)
) {
Registry::set('navigation.tabs.ab__stickers', [
'title' => __('ab__stickers'),
'js' => true,
]);

$repository = Tygh::$app['addons.ab__stickers.repository'];
$params = array_merge($_REQUEST, [
'status' => ObjectStatuses::ACTIVE,
]);
list($stickers) = $repository->find($params);

$view = Tygh::$app['view'];
$view->assign([
'ab__stickers' => $stickers,
]);
$default_placeholders = [];
$conditions_schema = fn_ab__stickers_get_conditions_schema()['dynamic']['conditions'] ?? [];
foreach ($conditions_schema as $condition) {
if (!empty($condition['available_placeholders'])) {
foreach ($condition['available_placeholders'] as $placeholder => $data) {
if (!empty($data['default_value'])) {
$default_placeholders[$placeholder] = $data['default_value'];
}
}
}
}
Tygh::$app['view']->assign('ab__stickers_default_placeholders', $default_placeholders);
return [CONTROLLER_STATUS_OK];
}
}
