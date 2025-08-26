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
use Tygh\Enum\Addons\Abt_unitheme2\DeviceTypes;
use Tygh\Registry;
defined('BOOTSTRAP') || die('Access denied');
if ($mode == 'preview'){
$session = & \Tygh\Tygh::$app['session'];
if ($session['auth']['user_type'] == 'C'){
return [CONTROLLER_STATUS_DENIED];
}
$params = $_REQUEST;
if (!empty($session['banner_settings'][$params['banner_id']])){
$params = array_merge($session['banner_settings'][$params['banner_id']], $params);
}
if (!empty($params['banner_id'])){
$banner_device = DeviceTypes::DESKTOP;
if (!empty($params['device'])) {
$banner_device = $params['device'];
}
if (!empty($params['hr_device_orientation_landscape'])){
Tygh::$app['view']->assign('hr_device_orientation_landscape', $params['hr_device_orientation_landscape']);
}
Registry::set('settings.ab__device',$banner_device);
list($banner,) = fn_get_banners(['item_ids' => $params['banner_id']], DESCR_SL);
$banner = array_shift($banner);
$height = !empty($params['height']) ? $params['height'] : 430;
$block = ['properties' => ['navigation' => "", 'margin' => '0', 'height' => $height . 'px']];
unset($params['device']);
$session['banner_settings'][$params['banner_id']] = $params;
Tygh::$app['view']->assign('banner_device', $banner_device);
Tygh::$app['view']->assign('banner_width', !empty($params['width']) ? $params['width'] : 16);
Tygh::$app['view']->assign('banner_height', $height);
Tygh::$app['view']->assign('banner', $banner);
Tygh::$app['view']->assign('banner_block',$block);
}
}