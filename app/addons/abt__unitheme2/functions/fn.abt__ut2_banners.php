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
use Tygh\Enum\UserTypes;
use Tygh\Languages\Languages;
use Tygh\Registry;
use Tygh\Storage;
use Tygh\Enum\YesNo;
use Tygh\Enum\SiteArea;
use Tygh\Enum\ImagePairTypes;
use Tygh\Enum\Addons\Abt_unitheme2\DeviceTypes;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
function fn_abt__unitheme2_get_banner_data_post($banner_id, $lang_code, &$banner)
{
if (!empty($banner) && in_array($banner['type'], [ABT__UT2_BANNER_TYPE]) && !empty($banner['abt__ut2_banner_image_id'])) {
foreach (['', '_' . DeviceTypes::TABLET, '_' . DeviceTypes::MOBILE] as $device) {
$banner["abt__ut2{$device}_main_image"] = fn_get_image_pairs($banner['abt__ut2_banner_image_id'], rtrim('abt__ut2/banners/' . ltrim($device ? $device : DeviceTypes::ALL, '_'), '/'), ImagePairTypes::MAIN, true, true, $lang_code);
$banner["abt__ut2{$device}_background_image"] = fn_get_image_pairs($banner['abt__ut2_banner_image_id'], rtrim('abt__ut2/banners/' . ltrim($device ? $device : DeviceTypes::ALL, '_'), '/'), ImagePairTypes::ADDITIONAL, true, true, $lang_code);
if (!empty($banner["abt__ut2{$device}_background_image"])) {
$banner["abt__ut2{$device}_background_image"] = reset($banner["abt__ut2{$device}_background_image"]);
}
}
}
}
function fn_abt__unitheme2_get_banners($params, &$condition, $sorting, $limit, $lang_code, &$fields)
{
$fields[] = '?:banners.group_id';
if (!empty($params['group_id'])){
if ($params['group_id'] == -1){
$params['group_id'] = 0;
}
$condition .= db_quote(' AND ?:banners.group_id = ?i', $params['group_id']);
}
if (AREA == SiteArea::STOREFRONT) {
$sub_cond = db_quote(' ?:banners.type like ?s AND ( ?:banners.abt__ut2_use_avail_period = ?s
OR
(
?:banners.abt__ut2_use_avail_period = ?s
AND (?:banners.abt__ut2_avail_from = 0 OR ?:banners.abt__ut2_avail_from <= ?i)
AND (?:banners.abt__ut2_avail_till = 0 OR ?:banners.abt__ut2_avail_till >= ?i)
)
)', ABT__UT2_BANNER_TYPE, YesNo::NO, YesNo::YES, TIME, TIME);
$condition .= db_quote(" AND IF (?:banners.type not like ?s, 'available', IF ($sub_cond, 'available', 'not available') ) = 'available' ", ABT__UT2_BANNER_TYPE);

if (empty($params['usergroup_ids']) || Tygh::$app['session']['auth']['user_type'] === UserTypes::CUSTOMER) {
$params['usergroup_ids'] = Tygh::$app['session']['auth']['usergroup_ids'];
}
$condition .= ' AND (' . fn_find_array_in_set($params['usergroup_ids'], '?:banners.abt__ut2_usergroup_ids', true) . ')';
}
}
function fn_abt__unitheme2_get_banners_post(&$banners, $params)
{
if (AREA == SiteArea::STOREFRONT) {
$devices_enabled_fields = [
DeviceTypes::TABLET => fn_get_schema('abt__ut2_banners', DeviceTypes::TABLET),
DeviceTypes::MOBILE => fn_get_schema('abt__ut2_banners', DeviceTypes::MOBILE),
];
$device = Registry::get('settings.ab__device');
foreach ($banners as &$banner) {
if ($banner['type'] == ABT__UT2_BANNER_TYPE) {
$banner = array_merge($banner, fn_get_banner_data($banner['banner_id']));
$banner['abt__ut2_device_settings'] = DeviceTypes::DESKTOP;
if (in_array($device, [DeviceTypes::TABLET, DeviceTypes::MOBILE])
&& !empty($devices_enabled_fields[$device])
&& $banner["abt__ut2_{$device}_use"] == YesNo::YES
) {
$banner['abt__ut2_device_settings'] = $device;
foreach ($devices_enabled_fields[$device] as $field) {
if ($banner["abt__ut2_{$device}_{$field}_use_own"] == YesNo::YES) {
$banner["abt__ut2_{$field}"] = $banner["abt__ut2_{$device}_{$field}"];
}
}
}
if($banner['abt__ut2_object'] === 'products' && !empty($banner['abt__ut2_products_list'])){
$params = [
'item_ids' => $banner['abt__ut2_products_list']
];
if (Registry::get('addons.product_variations.status') == ObjectStatuses::ACTIVE){
$params['abt__ut2_is_banner_products'] = true;
}
[$banner['products']] = fn_get_products($params);
if($banner['products']){
fn_gather_additional_products_data($banner['products'], [
'get_icon' => true,
'get_detailed' => true,
'get_options' => true,
]);
}
}
}
}
}
}
function fn_abt__unitheme2_get_banner_data($banner_id, $lang_code, &$fields, &$joins, $condition)
{
static $abt__ut_fields = [];
$fields[] = '?:banners.group_id';
if (empty($abt__ut_fields)) {
$devices_enabled_fields = [
DeviceTypes::TABLET => fn_get_schema('abt__ut2_banners', DeviceTypes::TABLET),
DeviceTypes::MOBILE => fn_get_schema('abt__ut2_banners', DeviceTypes::MOBILE),
];
$abt__ut_fields[] = '?:abt__ut2_banner_images.abt__ut2_banner_image_id';
$abt__ut_fields[] = '?:banners.abt__ut2_usergroup_ids';
$abt__ut_fields[] = '?:banners.abt__ut2_use_avail_period';
$abt__ut_fields[] = '?:banners.abt__ut2_avail_from';
$abt__ut_fields[] = '?:banners.abt__ut2_avail_till';
$abt__ut_fields[] = '?:banners.abt__ut2_button_use';
$abt__ut_fields[] = '?:banners.abt__ut2_button_style';
$abt__ut_fields[] = '?:banners.abt__ut2_button_text_color';
$abt__ut_fields[] = '?:banners.abt__ut2_button_text_color_use';
$abt__ut_fields[] = '?:banners.abt__ut2_button_color';
$abt__ut_fields[] = '?:banners.abt__ut2_button_color_use';
$abt__ut_fields[] = '?:banners.abt__ut2_title_color';
$abt__ut_fields[] = '?:banners.abt__ut2_title_color_use';
$abt__ut_fields[] = '?:banners.abt__ut2_title_font_size';
$abt__ut_fields[] = '?:banners.abt__ut2_title_font_weight';
$abt__ut_fields[] = '?:banners.abt__ut2_title_tag';
$abt__ut_fields[] = '?:banners.abt__ut2_background_image_size';
$abt__ut_fields[] = '?:banners.abt__ut2_image_position';
$abt__ut_fields[] = '?:banners.abt__ut2_title_shadow';
$abt__ut_fields[] = '?:banners.abt__ut2_description_font_size';
$abt__ut_fields[] = '?:banners.abt__ut2_description_color';
$abt__ut_fields[] = '?:banners.abt__ut2_description_color_use';
$abt__ut_fields[] = '?:banners.abt__ut2_description_bg_color';
$abt__ut_fields[] = '?:banners.abt__ut2_description_bg_color_use';
$abt__ut_fields[] = '?:banners.abt__ut2_object';
$abt__ut_fields[] = '?:banners.abt__ut2_background_color';
$abt__ut_fields[] = '?:banners.abt__ut2_background_color_use';
$abt__ut_fields[] = '?:banners.abt__ut2_class';
$abt__ut_fields[] = '?:banners.abt__ut2_color_scheme';
$abt__ut_fields[] = '?:banners.abt__ut2_content_valign';
$abt__ut_fields[] = '?:banners.abt__ut2_content_align';
$abt__ut_fields[] = '?:banners.abt__ut2_content_full_width';
$abt__ut_fields[] = '?:banners.abt__ut2_content_bg';
$abt__ut_fields[] = '?:banners.abt__ut2_content_bg_position';
$abt__ut_fields[] = '?:banners.abt__ut2_content_bg_align';
$abt__ut_fields[] = '?:banners.abt__ut2_content_bg_opacity';
$abt__ut_fields[] = '?:banners.abt__ut2_content_bg_color';
$abt__ut_fields[] = '?:banners.abt__ut2_content_bg_color_use';
$abt__ut_fields[] = '?:banners.abt__ut2_padding';
$abt__ut_fields[] = '?:banners.abt__ut2_how_to_open';
$abt__ut_fields[] = '?:banners.abt__ut2_data_type';
$abt__ut_fields[] = '?:banners.abt__ut2_youtube_use';
$abt__ut_fields[] = '?:banners.abt__ut2_youtube_autoplay';
$abt__ut_fields[] = '?:banners.abt__ut2_youtube_hide_controls';
$abt__ut_fields[] = '?:banners.abt__ut2_background_type';
$abt__ut_fields[] = '?:banner_descriptions.abt__ut2_background_mp4_video';
$abt__ut_fields[] = '?:banner_descriptions.abt__ut2_button_text';
$abt__ut_fields[] = '?:banner_descriptions.abt__ut2_title';
$abt__ut_fields[] = '?:banner_descriptions.abt__ut2_url';
$abt__ut_fields[] = '?:banner_descriptions.abt__ut2_description';
$abt__ut_fields[] = '?:banner_descriptions.abt__ut2_youtube_id';
$abt__ut_fields[] = '?:banners.abt__ut2_products_template';
$abt__ut_fields[] = '?:banners.abt__ut2_products_links_thumb_columns';
$abt__ut_fields[] = '?:banners.abt__ut2_products_links_thumb_rows';
$abt__ut_fields[] = '?:banners.abt__ut2_products_small_items_columns';
$abt__ut_fields[] = '?:banners.abt__ut2_products_small_items_rows';
$abt__ut_fields[] = '?:banners.abt__ut2_products_grid_columns';
$abt__ut_fields[] = '?:banners.abt__ut2_products_list';
foreach (['tablet', 'mobile'] as $device) {
$abt__ut_fields[] = "?:banners.abt__ut2_{$device}_use";
foreach ($devices_enabled_fields[$device] as $enabled_field) {
$table = '?:banners';
if (in_array($enabled_field, ['button_text', 'title', 'url', 'description', 'youtube_id', 'background_mp4_video'])) {
$table = '?:banner_descriptions';
}
if (!preg_match('/_image$/', $enabled_field)) {
$abt__ut_fields[] = "{$table}.abt__ut2_{$device}_{$enabled_field}";
}
$abt__ut_fields[] = "{$table}.abt__ut2_{$device}_{$enabled_field}_use_own";
}
}
}
$fields = array_merge($fields, $abt__ut_fields);
$joins[] = db_quote('LEFT JOIN ?:abt__ut2_banner_images ON ?:abt__ut2_banner_images.banner_id = ?:banners.banner_id AND ?:abt__ut2_banner_images.lang_code = ?s', $lang_code);
}
function fn_abt__ut2_build_youtube_link($d, $only_params = false)
{
$link = "https://www.youtube.com/embed/{$d['abt__ut2_youtube_id']}";
$params = [];
$params['rel'] = 'rel=0';
$params['showinfo'] = 'showinfo=0';
$params['modestbranding'] = 'modestbranding=1';
$params['enablejsapi'] = 'enablejsapi=1';
$params['version'] = 'version=3';
if (!empty($d['abt__ut2_youtube_autoplay']) && $d['abt__ut2_youtube_autoplay'] == YesNo::YES) {
$params['autoplay'] = 'autoplay=1';
$params['mute'] = 'mute=1';
}
if (!empty($d['abt__ut2_youtube_hide_controls']) && $d['abt__ut2_youtube_hide_controls'] == YesNo::YES) {
$params['controls'] = 'controls=0';
}
$p = implode('&', $params);
return ($only_params) ? $p : $link . '?' . $p;
}
function fn_abt__ut2_is_disabled_field($field, $enabled_fields)
{
$result = false;
if (!empty($enabled_fields)) {
$result = !in_array($field, $enabled_fields);
}
return $result;
}

function fn_abt__unitheme2_delete_banners($banner_id)
{
$banner_images_ids = db_get_fields("SELECT abt__ut2_banner_image_id FROM ?:abt__ut2_banner_images WHERE banner_id = ?i", $banner_id);
foreach (['', '_' . DeviceTypes::TABLET, '_' . DeviceTypes::MOBILE] as $device) {
foreach ($banner_images_ids as $banner_image_id) {
fn_delete_image_pairs($banner_image_id, rtrim('abt__ut2/banners/' . ltrim($device ? $device : DeviceTypes::ALL, '_'), '/'));
}
}
db_query("DELETE FROM ?:abt__ut2_banner_images WHERE banner_id = ?i", $banner_id);
}

function fn_abt__ut2_get_video_file($filepath = '')
{
return Storage::instance('images')->getUrl($filepath);
}

function fn_abt__ut2_get_video_absolute_path($filepath = '')
{
return Storage::instance('images')->getAbsolutePath($filepath);
}
function fn_abt__ut2_duplicate_banner_images($new_banner_id, $banner_id){
$ut2_banner_images = db_get_array(
'SELECT * FROM ?:abt__ut2_banner_images WHERE banner_id = ?i ORDER BY "lang_code" ASC',
$banner_id
);
$banner_images = db_get_array(
'SELECT * FROM ?:banner_images WHERE banner_id = ?i',
$banner_id
);
$ut2_image_links = db_get_array(
'SELECT * FROM ?:images_links WHERE object_id IN (?n) AND object_type IN (?a)',
array_column($ut2_banner_images,'abt__ut2_banner_image_id'),["abt__ut2/banners/all","abt__ut2/banners/mobile","abt__ut2/banners/tablet"]
);
$image_links = db_get_array(
'SELECT * FROM ?:images_links WHERE object_id IN (?n) AND object_type = ?s',
array_column($banner_images,'banner_image_id'),"promo"
);
if (!empty($ut2_banner_images && !empty($ut2_image_links))){
$images = db_get_array(
'SELECT * FROM ?:images WHERE image_id IN (?n)',
array_column($ut2_image_links,'image_id')
);
$object_ids = array_column($ut2_banner_images,'abt__ut2_banner_image_id');
foreach ($ut2_banner_images as $key => $ut2_banner_image){
unset($ut2_banner_images[$key]['abt__ut2_banner_image_id']);
$ut2_banner_images[$key]['banner_id'] = $new_banner_id;
}
db_query('INSERT INTO ?:abt__ut2_banner_images ?m', $ut2_banner_images);
$new_object_ids = db_get_fields('SELECT abt__ut2_banner_image_id FROM ?:abt__ut2_banner_images WHERE banner_id = ?i ORDER BY "lang_code" ASC', $new_banner_id);
$object_ids = array_combine($object_ids, $new_object_ids);
$image_ids = array_column($images,'image_id');
sort($image_ids);
$_images = $images;
foreach ($images as $key => $image){
unset($images[$key]['image_id']);
unset($image['image_id']);
$path_info = pathinfo($image['image_path']);
$image['image_path'] = $path_info['dirname'] . '/' . $path_info['filename'] . $new_banner_id . '.' . $path_info['extension'];
$new_image_ids[] = db_query('INSERT INTO ?:images ?e', $image);
}
sort($new_image_ids);
$image_ids = array_combine($image_ids, $new_image_ids);
foreach ($ut2_image_links as $key => &$image_link){
unset($ut2_image_links[$key]['pair_id']);
$image_link['object_id'] = $object_ids[$image_link['object_id']];
if ($image_link['image_id'] != 0){
$image_link['image_id'] = $image_ids[$image_link['image_id']];
}
}
db_query('INSERT INTO ?:images_links ?m', $ut2_image_links);
foreach ($_images as $image){
$image_types = [
'abt__ut2/banners/all',
'abt__ut2/banners/tablet',
'abt__ut2/banners/mobile'
];
foreach ($image_types as $type){
$image_data = fn_get_image($image['image_id'],$type);
if (empty($image_data)){
continue;
}
$image_path = $image_data['relative_path'];
$new_image_id = $image_ids[$image['image_id']];
$new_image_path = preg_replace('/(?<=\/)(\d{1,2})(?=\/[^\/]*$)/', substr($new_image_id, 0, 1), $image_path);
$path_info = pathinfo($new_image_path);
$new_image_path = $path_info['dirname'] . '/' . $path_info['filename'] . $new_banner_id . '.' . $path_info['extension'];
Storage::instance('images')->copy($image_path,$new_image_path);
}
}
}
if (!empty($banner_images) && !empty($image_links)){
$images = db_get_array(
'SELECT * FROM ?:images WHERE image_id IN (?n)',
array_column($image_links,'image_id')
);
$object_ids = array_column($banner_images,'banner_image_id');
sort($object_ids);
foreach ($banner_images as $key => $ut2_banner_image){
unset($banner_images[$key]['banner_image_id']);
$banner_images[$key]['banner_id'] = $new_banner_id;
}
db_query('INSERT INTO ?:banner_images ?m', $banner_images);
$new_object_ids = db_get_fields('SELECT banner_image_id FROM ?:banner_images WHERE banner_id = ?i', $new_banner_id);
sort($new_object_ids);
$object_ids = array_combine($object_ids, $new_object_ids);
$image_ids = array_column($images,'image_id');
$new_image_ids = [];
sort($image_ids);
$_images = $images;
foreach ($images as $key => $image){
unset($images[$key]['image_id']);
unset($image['image_id']);
$path_info = pathinfo($image['image_path']);
$image['image_path'] = $path_info['dirname'] . '/' . $path_info['filename'] . $new_banner_id . '.' . $path_info['extension'];
$new_image_ids[] = db_query('INSERT INTO ?:images ?e', $image);
}
sort($new_image_ids);
$image_ids = array_combine($image_ids, $new_image_ids);
foreach ($image_links as $key => &$image_link){
unset($image_links[$key]['pair_id']);
$image_link['object_id'] = $object_ids[$image_link['object_id']];
$image_link['image_id'] = $image_ids[$image_link['image_id']];
}
db_query('INSERT INTO ?:images_links ?m', $image_links);
foreach ($_images as $image){
$image_data = fn_get_image($image['image_id'],'promo');
$image_path = $image_data['relative_path'];
$new_image_id = $image_ids[$image['image_id']];
$new_image_path = preg_replace('/(?<=\/)(\d{1,2})(?=\/[^\/]*$)/', substr($new_image_id, 0, 1), $image_path);
$path_info = pathinfo($new_image_path);
$new_image_path = $path_info['dirname'] . '/' . $path_info['filename'] . $new_banner_id . '.' . $path_info['extension'];
Storage::instance('images')->copy($image_path,$new_image_path);
}
}
}
function fn_abt__unitheme2_banners_update_banner_pre($data, $banner_id, $lang_code){
if(!$banner_id){
$_REQUEST['abt__ut2_last_banner_id'] = db_get_field('SELECT MAX(banner_id) FROM ?:banners');
}
}
function fn_abt__ut2_get_banners_tree($params, $lang_code){
list($banners) = fn_get_banners($params, $lang_code);
$banners_tree = [];
foreach ($banners as $key => $banner){
if (!empty($banner['group_id'])){
if (empty($banners_tree[$banner['group_id']]['group_name'])){
$banners_tree[$banner['group_id']]['group_name'] = db_get_field(
'SELECT group_name FROM ?:abt__ut2_banner_group_descriptions WHERE banner_group_id = ?i AND lang_code = ?s',
$banner['group_id'],
$lang_code);
}
$banners_tree[$banner['group_id']]['items'][] = $banner;
unset($banners[$key]);
}
}
foreach ($banners as $banner){
$banners_tree[] = $banner;
}
return $banners_tree;
}
function fn_abt__ut2_update_banner_group($params, $lang_code)
{
if (empty( $params['banner_group_name'])){
fn_set_notification('W', __('warning'), __('abt__ut2.messages.group_field_must_be_not_empty'));
return false;
} else {
if (!empty($params['banner_group_id'])){
db_query('REPLACE INTO ?:abt__ut2_banner_group_descriptions VALUES(?i,?s,?s)',
$params['banner_group_id'],
$params['banner_group_name'],
$lang_code
);
} else {
$new_banner_group_id = db_query('INSERT INTO ?:abt__ut2_banner_groups VALUES(0)');
$languages = array_keys(Languages::getAll());
foreach ($languages as $lang){
db_query('INSERT INTO ?:abt__ut2_banner_group_descriptions VALUES(?i,?s,?s)',
$new_banner_group_id,
$params['banner_group_name'],
$lang
);
}
return $new_banner_group_id;
}
}
return true;
}
function fn_abt__ut2_get_banner_groups_list()
{
return db_get_hash_single_array('SELECT banner_group_id, group_name FROM ?:abt__ut2_banner_group_descriptions WHERE lang_code = ?s', ['banner_group_id', 'group_name'] ,CART_LANGUAGE);
}
function fn_abt__ut2_delete_banner_group($group_id)
{
db_query('UPDATE ?:banners SET group_id = 0 WHERE group_id = ?i', $group_id);
db_query('DELETE FROM ?:abt__ut2_banner_group_descriptions WHERE banner_group_id = ?i', $group_id);
db_query('DELETE FROM ?:abt__ut2_banner_groups WHERE banner_group_id = ?i', $group_id);
}
function fn_abt__ut2_group_banners($banner_ids, $group_id = 0){
if (!empty($banner_ids)){
db_query('UPDATE ?:banners SET group_id = ?i WHERE banner_id IN (?n)', $group_id, $banner_ids);
}
}