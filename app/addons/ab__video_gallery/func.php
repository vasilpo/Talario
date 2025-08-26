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
use Tygh\Enum\Addons\Ab_videoGallery\VideoProductPositionTypes;
use Tygh\Languages\Languages;
use Tygh\Registry;
use Tygh\Enum\YesNo;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\SiteArea;
use Tygh\Enum\ImagePairTypes;
use Tygh\Enum\Addons\Ab_videoGallery\VideoTypes;
use Tygh\Http;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
foreach (glob(__DIR__ . '/functions/fn.*.php') as $functions) {
require_once $functions;
}
function fn_ab__video_gallery_install()
{
$columns = db_get_fields('DESCRIBE ?:ab__video_gallery');
if (in_array('video_path', $columns)) {
$videos = db_get_array('SELECT video_id, video_path FROM ?:ab__video_gallery');
foreach ($videos as $video) {
db_query('UPDATE ?:ab__video_gallery_descriptions SET video_path = ?s WHERE video_id = ?i', $video['video_path'], $video['video_id']);
}
db_query('ALTER TABLE ?:ab__video_gallery DROP COLUMN `video_path`');
}
db_query('UPDATE ?:ab__video_gallery SET icon_type = ?s WHERE icon_type = ?i', 'snapshot', 'button');
fn_ab__vg_migrate_v300_v250();
fn_ab__vg_migrate_v360_v350();
fn_ab__vg_migrate_v370_v360();
}
function fn_ab__vg_delete_video($video_id)
{
$can_delete = true;
fn_set_hook('ab__vg_delete_video_pre', $video_id, $can_delete);
if ($can_delete && !empty($video_id)) {
db_query('DELETE FROM ?:ab__video_gallery WHERE video_id = ?i', $video_id);
db_query('DELETE FROM ?:ab__video_gallery_descriptions WHERE video_id = ?i', $video_id);
fn_delete_image_pairs($video_id, 'ab__vg_video');
}
}
function fn_ab__vg_update_video($video, $video_id, $lang_code = DESCR_SL)
{
fn_set_hook('ab__vg_update_video_pre', $video_id, $video, $lang_code);
if (empty($video['video_path'])) {
return false;
}
if (!empty($video['settings'])) {
$video['settings'] = serialize($video['settings']);
}
if (!empty($video_id)) {
$video['lang_code'] = $lang_code;
db_replace_into('ab__video_gallery', $video);
db_replace_into('ab__video_gallery_descriptions', $video);
} else {
if (Registry::get('runtime.simple_ultimate') == true){
$video['storefront_id'] = 0;
}
$video_id = $video['video_id'] = db_query('INSERT INTO ?:ab__video_gallery ?e', $video);
foreach (Languages::getSimpleLanguages() as $video['lang_code'] => $language) {
db_query('INSERT INTO ?:ab__video_gallery_descriptions ?e', $video);
}
}
return $video_id;
}

function fn_ab__vg_get_videos($product_id = 0, array $params = [], $lang_code = CART_LANGUAGE)
{
$orig_product_id = $product_id;
if (is_null($product_id)) {
$product_id = 'category';
}
if (AREA === SiteArea::STOREFRONT) {
if (!empty($orig_product_id)) {
if (Registry::ifGet('addons.product_variations.status', ObjectStatuses::DISABLED) === ObjectStatuses::ACTIVE) {

$variations_product_map = \Tygh\Addons\ProductVariations\ServiceProvider::getProductIdMap();
$parent_product_id = $variations_product_map->getParentProductId($orig_product_id);
if (!empty($parent_product_id)) {
$orig_product_id = $parent_product_id;
}
}
if (Registry::ifGet('addons.master_products.status', ObjectStatuses::DISABLED) === ObjectStatuses::ACTIVE) {

$master_producs_repo = \Tygh\Addons\MasterProducts\ServiceProvider::getProductRepository();
$master_product_id = $master_producs_repo->findMasterProductId($orig_product_id);
if (!empty($master_product_id)) {
$orig_product_id = $master_product_id;
}
}
}
}
$params = array_merge([
'limit' => null,
'types' => [],
'exclude_types' => [],
'get_query' => false,
'unique_videos' => false,
'storefront_id' => Registry::ifGet('runtime.storefront_id', 0)
], $params);
$cache_key_product_id = $orig_product_id ?? $product_id;
$cache_conditions = ['ab__video_gallery', 'ab__video_gallery_descriptions', 'products'];
fn_set_hook('ab__vg_get_videos_build_cache', $cache_key_product_id, $cache_conditions, $params);
$cache_key = "ab__vg_" . md5("$cache_key_product_id-$lang_code-" . serialize($params));
if (!is_null($orig_product_id) && AREA === SiteArea::STOREFRONT) {
Registry::registerCache(['ab__video_gallery', $cache_key], $cache_conditions);
}
if (!Registry::isExist($cache_key)) {
$fields = [
$params['unique_videos'] === true
? 'DISTINCT(?:ab__video_gallery_descriptions.video_path)'
: '?:ab__video_gallery_descriptions.video_path',
'videos.video_id',
'videos.product_id',
'videos.status',
'videos.pos',
'videos.product_pos_type',
'videos.product_pos',
'videos.type',
'videos.autoplay',
'videos.show_in_list',
'videos.settings',
'videos.icon_type',
'videos.storefront_id',
'?:ab__video_gallery_descriptions.title',
'?:ab__video_gallery_descriptions.description',
];
$condition = '1';
if (AREA == "C"){
$condition .= db_quote(" AND videos.storefront_id IN (?i, 0)", $params['storefront_id']);
}
$limit = '';
if (!empty($params['limit'])) {
$limit = db_quote(" LIMIT 0, ?i", $params['limit']);
}
$join = db_quote('LEFT JOIN ?:ab__video_gallery_descriptions ON ?:ab__video_gallery_descriptions.video_id = videos.video_id AND ?:ab__video_gallery_descriptions.lang_code = ?s', $lang_code);
if (!empty($params['types'])) {
$condition .= db_quote(' AND videos.type IN (?a)', $params['types']);
}
if (!empty($params['exclude_types'])) {
$condition .= db_quote(' AND videos.type NOT IN (?a)', $params['exclude_types']);
}
if (!empty($params['autoplay'])) {
$condition .= db_quote(' AND videos.autoplay = ?s', $params['autoplay']);
}
if (!empty($params['show_in_list'])) {
$condition .= db_quote(' AND videos.show_in_list = ?s', $params['show_in_list']);
}
if (!empty($params['condition'])) {
$condition .= $params['condition'];
}
if (AREA === SiteArea::STOREFRONT) {
$condition .= db_quote(' AND videos.status = ?s', ObjectStatuses::ACTIVE);
}
if (!empty($orig_product_id)) {
$condition .= db_quote(' AND videos.product_id = ?i', $orig_product_id);
}
fn_set_hook('ab__vg_get_videos_pre', $product_id, $params, $lang_code, $fields, $join, $condition, $limit);
$query_string = db_quote('SELECT ?p FROM ?:ab__video_gallery AS videos ?p WHERE ?p ORDER BY videos.pos ASC ?p', implode(', ', $fields), $join, $condition, $limit);
if ($params['get_query'] === true) {
return $query_string;
}
$product_videos = db_get_hash_array($query_string, 'video_id');
if (!empty($product_videos)) {
$images = fn_get_image_pairs(array_keys($product_videos), 'ab__vg_video', ImagePairTypes::MAIN, true, false, $lang_code);
foreach ($product_videos as &$video) {
$video['settings'] = unserialize($video['settings']);
if (!empty($images[$video['video_id']])) {
$video['icon'] = reset($images[$video['video_id']]);
}
}
}
if (empty($product_videos)) {
$product_videos = 'empty';
}
Registry::set($cache_key, $product_videos);
}
$product_videos = Registry::get($cache_key);
if ($product_videos === 'empty') {
$product_videos = [];
}
$product_videos = array_map(function ($video) {
$video['unique_id'] = Tygh\Tools\SecurityHelper::generateRandomString();
return $video;
}, $product_videos);
return $product_videos;
}
function fn_ab__vg_update_settings($settings, $product_id)
{
foreach ($settings as $var => $value) {
db_replace_into('ab__video_gallery_settings', [
'product_id' => $product_id,
'var' => $var,
'value' => $value,
]);
}
}
function fn_ab__vg_get_setting($product_id)
{
if (empty($product_id)) {
return false;
}
$settings = db_get_hash_single_array('SELECT var, value FROM ?:ab__video_gallery_settings WHERE product_id = ?i', ['var', 'value'], $product_id);
return $settings;
}
function fn_ab__vg_delete_settings($product_id)
{
if (!empty($product_id)) {
db_query('DELETE FROM ?:ab__video_gallery_settings WHERE product_id = ?i', $product_id);
}
}
function fn_ab__vg_exim_get_replace_image($product_id, $var)
{
$settings = fn_ab__vg_get_setting($product_id);
return empty($settings[$var]) ? YesNo::NO : $settings[$var];
}
function fn_ab__vg_exim_put_replace_image($product_id, $var, $value)
{
fn_ab__vg_update_settings([
$var => $value
], $product_id);
}

function fn_ab__video_gallery_get_products_post(&$products, $params, $lang_code)
{
if (Registry::ifGet('addons.ab__video_gallery.show_in_lists', YesNo::NO) == YesNo::YES && !empty($products)) {
$products_ids = array_column($products, 'product_id');
$condition = '';
if (AREA == "C"){
$condition .= db_quote("AND storefront_id IN (?i, 0)", Registry::ifGet('runtime.storefront_id',0));
}
$array = db_get_hash_array('SELECT video_id, product_id FROM ?:ab__video_gallery WHERE product_id IN (?n) AND status = "A" ?p', 'product_id', $products_ids, $condition);
foreach ($products as &$product) {
$product['ab__vg_videos'] = !empty($array[$product['product_id']]);
}
}
}

function fn_ab__video_gallery_delete_product_post($product_id, $product_deleted)
{
if ($product_deleted) {
$video_ids = db_get_fields('SELECT video_id FROM ?:ab__video_gallery WHERE product_id = ?i', $product_id);
if (!empty($video_ids)) {
foreach ($video_ids as $video_id) {
fn_ab__vg_delete_video($video_id);
}
}
fn_ab__vg_delete_settings($product_id);
}
}
function fn_ab__video_gallery_update_product_post($product_data, $product_id, $lang_code, $create)
{
if (isset($product_data['ab__vg_settings'])) {
fn_ab__vg_update_settings($product_data['ab__vg_settings'], $product_id);
}
if (isset($product_data['ab__vg_videos'])) {
$product_video_ids = [];
foreach ($product_data['ab__vg_videos'] as $key => $video) {
$video['product_id'] = $product_id;
if (!empty($video['video_path'])) {
$video_path = parse_url($video['video_path']);
if ($video['type'] === VideoTypes::YOUTUBE && !empty($video_path['query'])) {
$matches = [];
$m_count = preg_match('/v=([^&$]*)/', $video_path['query'], $matches);
if ($m_count) {
$video['video_path'] = $matches[1];
}
} elseif ($video['type'] === VideoTypes::VIMEO && !empty($video_path['host'])) {
$video['video_path'] = substr($video_path['path'], 1);
}
}
$video_id = fn_ab__vg_update_video($video, $video['video_id'], $lang_code);
if ($video_id) {
$product_video_ids[$key] = $video_id;
}
}
$old_video_ids = db_get_fields('SELECT video_id FROM ?:ab__video_gallery WHERE product_id = ?i AND video_id NOT IN (?n)', $product_id, $product_video_ids);
foreach ($old_video_ids as $old_video_id) {
fn_ab__vg_delete_video($old_video_id);
}
fn_attach_image_pairs('video_icon', 'ab__vg_video', 0, $lang_code, $product_video_ids);
}
}
function fn_ab__video_gallery_clone_product($product_id, $pid)
{
$videos = fn_ab__vg_get_videos($product_id);
if (!empty($videos)) {
foreach ($videos as $video) {
$old_id = $video['video_id'];
$video['video_id'] = 0;
$video['product_id'] = $pid;
$new_id = fn_ab__vg_update_video($video, 0);
fn_clone_image_pairs($new_id, $old_id, 'ab__vg_video');
}
}
$settings = fn_ab__vg_get_setting($product_id);
fn_ab__vg_update_settings($settings, $pid);
}
function fn_ab__vg_exim_get_video_id($pattern, &$alt_keys, &$object, &$skip_get_primary_object_id)
{
if (!empty($skip_get_primary_object_id)) {
return;
}
if (empty($alt_keys['video_id'])) {
$video_id = db_get_field('SELECT v.video_id FROM ?:ab__video_gallery AS v
INNER JOIN ?:ab__video_gallery_descriptions AS vd ON v.video_id = vd.video_id AND vd.lang_code = ?s AND vd.video_path = ?s
WHERE v.product_id = ?i', $object['lang_code'], $object['video_path'], $object['product_id']);
if (!empty($video_id)) {
$alt_keys['video_id'] = $object['video_id'] = $video_id;
} else {
$skip_get_primary_object_id = true;
}
}
}
function fn_ab__vg_exim_remove_videos($remove_videos, $import_data, $processed_data)
{
if ($remove_videos == YesNo::YES && !empty($import_data)) {
$updated_products = [];
foreach ($import_data as $item) {
if (empty($updated_products[$item['product_id']])) {
$updated_products[$item['product_id']] = $item['product_id'];
$video_ids = db_get_fields('SELECT video_id FROM ?:ab__video_gallery WHERE product_id = ?i', $item['product_id']);
if (!empty($video_ids)) {
foreach ($video_ids as $video_id) {
fn_ab__vg_delete_video($video_id);
}
}
}
}
}
}
function fn_ab__vg_exim_process_autoplay($import_data)
{
if (!is_array($import_data)) {
return;
}
$product_ids = [];
foreach ($import_data as $item) {
foreach ($item as $video) {
$product_ids[] = $video['product_id'] ?? 0;
}
}
$product_ids = array_unique($product_ids);
foreach ($product_ids as $product_id) {
$videos_ids = db_get_fields('SELECT video_id FROM ?:ab__video_gallery WHERE product_id = ?i AND autoplay = ?s', $product_id, YesNo::YES);
if (count($videos_ids) > 1) {
$video_id = array_shift($videos_ids);
db_query('UPDATE ?:ab__video_gallery SET autoplay = ?s WHERE product_id = ?i AND video_id != ?i', YesNo::NO, $product_id, $video_id);
db_query('UPDATE ?:ab__video_gallery SET show_in_list = ?s WHERE product_id = ?i AND autoplay != ?s', YesNo::NO, $product_id, YesNo::YES);
}
}
}

function fn_ab__video_gallery_get_products($params, $fields, $sortings, $condition, &$join, $sorting, $group_by, $lang_code, $having)
{
if (!empty($params['ab__vg_products_with_video']) && $params['ab__vg_products_with_video'] === YesNo::YES) {
$join .= db_quote(' INNER JOIN ?:ab__video_gallery ON ?:ab__video_gallery.product_id = products.product_id');
}
}

function fn_ab__video_gallery_seo_get_schema_org_markup_items_post($product_data, $show_price, $currency, &$markup_items)
{
if (Registry::get('addons.ab__video_gallery.enable_microdata') !== YesNo::YES) {
return;
}
$ab__vg_videos = fn_ab__vg_get_videos($product_data['product_id'], [
'exclude_types' => [VideoTypes::HREF, VideoTypes::RESOURCE],
]);
if (!empty($ab__vg_videos)) {
foreach ($ab__vg_videos as $video) {
$markup_items['product']['subjectOf'][] = fn_ab__vg_get_video_json_ld_markup($video, $product_data);
}
}
}

function fn_ab__vg_get_video_json_ld_markup(array $video, array $product_data = [])
{
$description = strip_tags($video['description']);
if (empty($description)) {
$description = strip_tags($video['title']);
}
$markup = [
'@type' => 'VideoObject',
'name' => $video['title'],
'description' => $description,
'thumbnailUrl' => fn_ab__vg_get_video_icon($video),
'embedUrl' => fn_ab__vg_get_video_embed_url($video),
];
if (!empty($product_data['timestamp'])) {
$markup['uploadDate'] = date('c', $product_data['timestamp']);
}
if ($video['type'] == VideoTypes::VIMEO) {
$info = fn_ab__vg_get_vimeo_video_info($video);
$markup['author'] = $info['user_name'] ?? 'undefined';
$markup['duration'] = date('c', $info['duration'] ?? 1);
if (!empty($info['stats_number_of_comments'])) {
$markup['commentCount'] = $info['stats_number_of_comments'];
}
}

fn_set_hook('ab__vg_get_video_json_ld_markup', $video, $product_data, $markup);
return $markup;
}

function fn_ab__video_gallery_get_enum_list($enum = 'VideoTypes')
{
$enum = 'Tygh\Enum\Addons\Ab_videoGallery\\' . $enum;
$data = $enum::getAll();
fn_set_hook('ab__video_gallery_get_enum_list_post', $enum, $data);
return $data;
}

function fn_ab__vg_get_video_icon(array $video)
{
$video_icon = '';
if ($video['type'] == VideoTypes::YOUTUBE) {
$video_icon = "https://img.youtube.com/vi/{$video['video_path']}/hqdefault.jpg";
} elseif ($video['type'] == VideoTypes::VIMEO) {
$vimeo_image_info = fn_ab__vg_get_vimeo_video_info($video);
if (!empty($vimeo_image_info['thumbnail_large'])) {
$video_icon = $vimeo_image_info['thumbnail_large'];
}
}

fn_set_hook('ab__vg_get_video_icon', $video, $video_icon);
return $video_icon;
}

function fn_ab__vg_get_video_embed_url(array $video)
{
$embed_url = '';
static $settings = [];
if (empty($settings)) {
$settings = Registry::get('addons.ab__video_gallery');
}
$site_origin = (Registry::get('settings.Security.secure_storefront') === YesNo::YES ? fn_url('', 'C', 'https') : fn_url('', 'C', 'http'));
$site_origin = (substr($site_origin, -1) === '/' ? substr($site_origin, 0, -1) : $site_origin);
if ($video['type'] == VideoTypes::YOUTUBE) {
$embed_url = "https://www.youtube.com/embed/{$video['video_path']}?rel=0&enablejsapi=1&version=3&autoplay=1"
. '&origin=' . $site_origin
. '&host=https://www.youtube.com'
. '&controls=' . ($settings['controls'] == YesNo::YES ? '1' : '0')
. '&loop=' . ($settings['repeat'] == YesNo::NO ? '0' : "1&playlist={$video['video_path']}");
} elseif ($video['type'] == VideoTypes::VIMEO) {
$embed_url = "https://player.vimeo.com/video/{$video['video_path']}?color=ffffff&autoplay=1&controls=1"
. '&loop=' . ($settings['repeat'] == YesNo::YES ? '1' : '0');
} elseif ($video['type'] == VideoTypes::HREF) {
$embed_url = $video['video_path'];
} elseif ($video['type'] == VideoTypes::RESOURCE) {
$embed_url = $video['video_path'];
}
fn_set_hook('ab__vg_get_video_embed_url', $video, $embed_url);
return $embed_url;
}

function fn_ab__vg_get_vimeo_video_info(array $video)
{
$path = $video['video_path'];
$info = [];
static $init_cache = false;
$cache_alias = 'ab__vg_vimeo_data';
$cache_name = ['ab__video_gallery', $cache_alias];
$key = $path;
if (!$init_cache) {
$init_cache = true;
Registry::registerCache($cache_name, [], null, true);
}
if (Registry::isExist($cache_alias . '.' . $key)) {
$info = Registry::get($cache_alias . '.' . $key);
} else {
$response = Http::get('https://vimeo.com/api/v2/video/' . $path . '.json', [], [
'headers' => [
'Content-type: application/json',
],
'timeout' => 3,
]);
if (!empty($response)) {
$response = json_decode($response, true);
if (!empty($response)) {
$info = array_pop($response);
Registry::set($cache_alias . '.' . $key, $info);
}
}
}
return $info;
}

function fn_ab__vg_videos_get_block_info(array $block)
{
return [
'content' => __('block_ab__vg_videos'),
];
}

function fn_ab__vg_videos_get_videos($value, array $block, array $block_scheme)
{
$get_product_conditions = $_REQUEST;
$get_product_conditions['get_query'] = true;
$get_product_conditions['cid'] = $_REQUEST['category_id'];
$get_product_conditions['subcats'] = true;
$query = fn_get_products($get_product_conditions);
$videos = fn_ab__vg_get_videos(null, [
'limit' => (int)$block['properties']['ab__vg_max_videos'],
'condition' => db_quote('AND videos.product_id IN (?p)', $query),
'unique_videos' => true,
'exclude_types' => [VideoTypes::HREF, VideoTypes::RESOURCE],
'autoplay' => YesNo::NO
]);
return $videos;
}

function fn_ab__video_gallery_init_templater_post($view)
{
if (AREA === SiteArea::STOREFRONT) {
$view->registerFilter('post', 'fn_ab__vg_replace_image_gallery');
if (fn_ab__vg_get_theme_name() !== 'abt__unitheme2') {
$view->registerFilter('pre', 'fn_ab__vg_add_storefront_hooks');
}
}
}

function fn_ab__vg_replace_image_gallery($content, \Smarty_Internal_Template $template)
{
if (strpos($content, 'js/tygh/product_image_gallery.js') !== false) {
$content = str_replace('js/tygh/product_image_gallery.js', 'js/addons/ab__video_gallery/product_image_gallery.js', $content);
}
return $content;
}

function fn_ab__vg_add_storefront_hooks($content, \Smarty_Internal_Template $template)
{
if (strpos($template->template_resource, 'views/products/components/product_icon.tpl') !== false) {
$content = preg_replace('/\{\s*if\s*\$product\.image_pairs/i', '{hook name="ab__vg:product_main_icon"}{/hook}' . PHP_EOL . '$0', $content);
$content = preg_replace('/\{\s*if\s*\$product\.main_pair\s*}/i', '{hook name="ab__vg:product_main_pair"}{/hook}' . PHP_EOL . '$0', $content);
}
return $content;
}
function fn_ab__vg_get_storefronts() {
return db_get_hash_single_array('SELECT storefront_id, name FROM ?:storefronts', array('storefront_id', 'name'));
}

function fn_ab__vg_get_videos_by_position(&$videos, $position, $iterator, $total)
{
$videos_by_pos = [];
$default_position = Registry::get('addons.ab__video_gallery.position') === 'pre' ? VideoProductPositionTypes::TOP : VideoProductPositionTypes::BOTTOM;
if (in_array($position, [VideoProductPositionTypes::TOP, VideoProductPositionTypes::BOTTOM])) {
$videos_by_pos = array_filter($videos, function($video) use($position, $default_position) {
$cond1 = $video['product_pos_type'] === VideoProductPositionTypes::DEFAULT && $position === $default_position;
$cond2 = $video['product_pos_type'] === $position;
return $cond1 || $cond2;
});
} else if ($position === VideoProductPositionTypes::CUSTOM) {
if ($total === 0) {
$videos_by_pos = array_filter($videos, function($video) {
return $video['product_pos_type'] === VideoProductPositionTypes::CUSTOM;
});
} else if ($iterator === 0) {
$videos_by_pos = array_filter($videos, function($video) {
return $video['product_pos_type'] === VideoProductPositionTypes::CUSTOM && $video['product_pos'] <= 0;
});
} else if ($iterator >= $total) {
$videos_by_pos = array_filter($videos, function($video) use ($iterator) {
return $video['product_pos_type'] === VideoProductPositionTypes::CUSTOM && $video['product_pos'] >= $iterator;
});
} else {
$videos_by_pos = array_filter($videos, function($video) use ($iterator) {
return $video['product_pos_type'] === VideoProductPositionTypes::CUSTOM && $video['product_pos'] == $iterator;
});
}
}
$videos = array_diff_key($videos, $videos_by_pos);
return $videos_by_pos;
}

function fn_ab__vg_get_theme_name()
{
static $theme_name = null;
if (is_null($theme_name)) {
$theme_name = fn_get_theme_path('[theme]');
}
return $theme_name;
}