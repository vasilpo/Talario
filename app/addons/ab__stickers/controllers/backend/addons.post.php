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
defined('BOOTSTRAP') or die('Access denied');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}
if (Registry::get('runtime.mode') === 'update') {
if (!empty($_REQUEST['addon']) && $_REQUEST['addon'] == 'ab__stickers') {
$settings = Tygh::$app['view']->getTemplateVars('options');
if (!empty($settings) && !empty($settings['settings'])) {
$ab__stickers_settings = [];
foreach ($settings['settings'] as $setting) {
if (strpos($setting['name'], '_output_type') || strpos($setting['name'], '_max_count')) {
$ab__stickers_settings[$setting['name']] = $setting;
}
}
Tygh::$app['view']->assign('ab__stickers_output_settings', $ab__stickers_settings);
}
Registry::set('navigation.dynamic', [
'sections' => fn_get_schema('ab__stickers', 'sidebar_sections'),
'active_section' => 'ab__stickers.settings',
]);
}
}
