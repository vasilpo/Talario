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
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
function fn_abt__unitheme2_update_static_data(&$data, $param_id, $condition, $section, $lang_code)
{
if (Registry::get('runtime.mode') == 'update') {
$data['abt__ut2_mwi__text'] = $_POST['static_data']['abt__ut2_mwi__text'];
fn_attach_image_pairs('abt__ut2_mwi__icon', 'abt__ut2/menu-with-icon', $param_id, $lang_code);
}
}
function fn_abt__unitheme2_get_static_data($params, &$fields, $condition, $sorting, $lang_code)
{
$fields[] = 'sd.abt__ut2_mwi__status';
$fields[] = '?:static_data_descriptions.abt__ut2_mwi__label';
$fields[] = 'sd.abt__ut2_mwi__label_color';
$fields[] = 'sd.abt__ut2_mwi__label_background';
if (empty($params['abt__ut2_fly_menu'])) {
$fields[] = '?:static_data_descriptions.abt__ut2_mwi__desc';
$fields[] = '?:static_data_descriptions.abt__ut2_mwi__text';
$fields[] = 'sd.abt__ut2_mwi__text_position';
$fields[] = 'sd.abt__ut2_mwi__dropdown';
}
}
function fn_abt__unitheme2_top_menu_form_post(&$top_menu, $level, $active)
{
static $images_cache = [];
if (!empty($top_menu) && is_array($top_menu)) {
$ids = [];
$get_images_ids = [];
foreach ($top_menu as $i => $m) {
if (!empty($m['abt__ut2_mwi__status']) && $m['abt__ut2_mwi__status'] === 'Y') {
$ids[] = $i;
if(!isset($images_cache[$i])){
$get_images_ids[] = $i;
}
}
}
if (!empty($ids)) {
$images = $get_images_ids ? fn_get_image_pairs($get_images_ids, 'abt__ut2/menu-with-icon', 'M', true, false) : [];
foreach ($ids as $i) {
if(!isset($images_cache[$i])) {
if(!isset($images[$i])) {
continue;
}
$images_cache[$i] = reset($images[$i]);
}
$top_menu[$i]['abt__ut2_mwi__icon'] = $images_cache[$i];
}
}
}
}

function fn_abt__ut2_ajax_menu_save($data, $id, $lang_code = DESCR_SL)
{
static $init_cache = false;
$cache_name = 'abt__ut2_am';
$key = $id . '_' . $lang_code . '_' . fn_ab__am_get_device_type();
if (!$init_cache) {
$init_cache = true;
Registry::registerCache($cache_name, ['static_data', 'static_data_descriptions'], Registry::cacheLevel('static'), true);
}
Registry::set($cache_name . '.' . $key, $data);
}
function fn_abt__ut2_ajax_menu_get($key)
{
static $init_cache = false;
$cache_name = 'abt__ut2_am';
if (!$init_cache) {
$init_cache = true;
Registry::registerCache($cache_name, ['static_data', 'static_data_descriptions'], Registry::cacheLevel('static'), true);
}
static $data;
if (empty($data)) {
$data = Registry::get($cache_name);
}
return isset($data[$key]) ? $data[$key] : '';
}
