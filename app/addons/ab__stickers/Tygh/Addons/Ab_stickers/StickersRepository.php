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
namespace Tygh\Addons\Ab_stickers;
use Tygh;
use Tygh\Database\Connection;
use Tygh\Enum\Addons\Ab_stickers\StickerPlaces;
use Tygh\Enum\Addons\Ab_stickers\StickerStyles;
use Tygh\Enum\ImagePairTypes;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\SiteArea;
use Tygh\Enum\YesNo;
use Tygh\Exceptions\DatabaseException;
use Tygh\Navigation\LastView;
use Tygh\Registry;
use Tygh\Storefront\Storefront;
use Tygh\Tools\ErrorHandler;

class StickersRepository
{

public $settings = [];

protected $db;

protected $storefront;

protected $area;

protected $table = 'ab__stickers';

protected $descriptions_table = 'ab__sticker_descriptions';

protected $images_table = 'ab__sticker_images';

protected $key = 'sticker_id';

protected $encoded_fields = ['display_on', 'display_place', 'output_position', 'user_class', 'appearance', 'conditions'];

private $auth = [];

public function __construct($settings, Connection $db, Storefront $storefront, array $auth = [], $area = '')
{
$this->settings = $settings;
$this->db = $db;
$this->area = $area;
$this->auth = $auth;
$this->storefront = $storefront;
}
public function setAuth(array $auth)
{
$this->auth = $auth;
}

public function deleteStickers(array $ids)
{
$res = false;

$vendors_repository = Tygh::$app['addons.ab__stickers.vendors_repository'];
try {
$res = $this->db->query('DELETE FROM ?:?p WHERE ?p IN (?n)', $this->table, $this->key, $ids);
$res = $res && $this->db->query('DELETE FROM ?:?p WHERE ?p IN (?n)', $this->descriptions_table, $this->key, $ids);
if (fn_allowed_for('MULTIVENDOR')) {
$res = $res && $vendors_repository->deleteStickersVendorDataByStickerIds($ids);
}
foreach ($ids as $id) {
$image_ids = $this->db->getColumn('SELECT image_id FROM ?:?p WHERE ?p = ?i', $this->images_table, $this->key, $ids);
foreach ($image_ids as $image_id) {
$res = $res && fn_delete_image_pairs($image_id, 'ab__stickers', ImagePairTypes::MAIN);
}
foreach (['manual', 'generated'] as $filling) {
fn_ab__stickers_attach_sticker_to_products($id, [], $filling);
}
}
$res = $res && $this->db->query('DELETE FROM ?:?p WHERE ?p IN (?n)', $this->images_table, $this->key, $ids);
} catch (DatabaseException $e) {
ErrorHandler::handleException($e);
}
return (bool) $res;
}

public function findById($id, $lang_code = DESCR_SL, $params = [])
{
if (empty($id)) {
return [];
}
$fields = empty($params['fields']) ? [
"?:{$this->table}.*",
'descriptions.*',
'images.image_id',
'?:' . $this->table . '.' . $this->key . ' AS ' . $this->key,
] : $params['fields'];
$join = $this->db->quote('LEFT JOIN ?:?p as descriptions
ON ?:?p.?p = descriptions.?p
AND descriptions.lang_code = ?s', $this->descriptions_table, $this->table, $this->key, $this->key, $lang_code);
$join .= $this->db->quote(' LEFT JOIN ?:?p AS images
ON ?:?p.?p = images.?p
AND images.lang_code = ?s', $this->images_table, $this->table, $this->key, $this->key, $lang_code);
$condition = $this->db->quote('?:?p.?p = ?i', $this->table, $this->key, $id);

fn_set_hook('ab__stickers_get_sticker_data_before_select', $fields, $join, $condition, $id, $lang_code);
$sticker = $this->db->getRow('SELECT ?p FROM ?:?p ?p WHERE ?p', implode(', ', $fields), $this->table, $join, $condition);
if (!empty($sticker)) {
foreach ($this->getEncodedFieldsList() as $field) {
$sticker[$field] = fn_ab__stickers_decode_data($sticker[$field] ?? []);
}
$sticker['pictogram_data'] = fn_ab__stickers_pictograms_get_texts($sticker['sticker_id'], null, $lang_code);
$sticker['product_ids'] = $this->db->getField('SELECT GROUP_CONCAT(product_id) FROM ?:products WHERE FIND_IN_SET(?i, ab__stickers_manual_ids)', $sticker[$this->key]);
$sticker['main_pair'] = fn_get_image_pairs($sticker['image_id'], 'ab__stickers', ImagePairTypes::MAIN, true, true, $lang_code);
if ($sticker['style'] === StickerStyles::PICTOGRAM) {
if ($pictogram_image_id = fn_ab__stickers_pictograms_get_image_id($sticker['sticker_id'], 0, DESCR_SL)) {
$sticker['main_pair_p'] = fn_get_image_pairs($pictogram_image_id, 'ab__pictograms', ImagePairTypes::MAIN, true, true, DESCR_SL);
}
}
}
return $sticker;
}

public function find($params = [], $items_per_page = 0, $lang_code = DESCR_SL)
{
$default_params = [
'get_icons' => true,
'page' => 1,
'items_per_page' => $items_per_page,
'area' => $this->area,
'sort_by' => 'position',
'sort_order' => 'asc',
'storefront_id' => $this->getStorefrontIds(),
'get_query' => false,
'timestamp' => $this->area == SiteArea::STOREFRONT ? strtotime('today') : 0,
];
if ($this->area == SiteArea::STOREFRONT) {
$default_params['usergroup_ids'] = $this->auth['usergroup_ids'];
}
$params = array_merge($default_params, $params);
$params = LastView::instance()->update('ab__stickers', $params);
$sortings = [
'name_for_admin' => '?:ab__stickers.name_for_admin',
'status' => '?:ab__stickers.status',
'position' => '?:ab__stickers.position',
'type' => '?:ab__stickers.type',
'last_update_time_pictogram' => '?:ab__stickers.last_update_time_pictogram',
];
$sorting = db_sort($params, $sortings, $params['sort_by'], $params['sort_order']);
$fields = $params['fields'] ?? [
'?:ab__stickers.*',
'descriptions.*',
'images.image_id',
'?:ab__stickers.sticker_id as sticker_id',
];
$limit = '';
try {
$condition = $this->db->quote('FIND_IN_SET (?i, storefront_ids)', $params['storefront_id']);
$join = $this->db->quote('LEFT JOIN ?:ab__sticker_descriptions as descriptions
ON ?:ab__stickers.sticker_id = descriptions.sticker_id
AND descriptions.lang_code = ?s
LEFT JOIN ?:ab__sticker_images AS images
ON ?:ab__stickers.sticker_id = images.sticker_id
AND images.lang_code = ?s', $lang_code, $lang_code);
if (!empty($params['status'])) {
$condition .= $this->db->quote(' AND ?:ab__stickers.status IN (?a)', $params['status']);
} elseif ($params['area'] == SiteArea::STOREFRONT) {
$condition .= $this->db->quote(' AND ?:ab__stickers.status = ?s', ObjectStatuses::ACTIVE);
}
if (!empty($params['timestamp'])) {
$condition .= $this->db->quote(' AND ((?:ab__stickers.from_date <= ?i OR ?:ab__stickers.from_date = 0) AND (?:ab__stickers.to_date >= ?i OR ?:ab__stickers.to_date = 0))', $params['timestamp'], $params['timestamp']);
}
if (!empty($params['type']) && fn_string_not_empty($params['type'])) {
$condition .= $this->db->quote(' AND ?:ab__stickers.type = ?s', $params['type']);
}
$_str = '0';
if (!empty($params['item_ids'])) {
$_str = $this->db->quote('?:ab__stickers.sticker_id IN (?n)', is_array($params['item_ids']) ? $params['item_ids'] : explode(',', $params['item_ids']));
}
if ($params['area'] == SiteArea::STOREFRONT) {
if (empty($params['forced_type']) || (isset($params['forced_type']) && strlen((trim($params['forced_type']))) == 0)) {
$condition .= " AND {$_str}";
} else {
$condition .= $this->db->quote(" AND (({$_str}) OR ?:ab__stickers.type = ?s)", $params['forced_type']);
}
} elseif ($_str != '0') {
$condition .= " AND {$_str}";
}
if (isset($params['name_for_admin']) && fn_string_not_empty($params['name_for_admin'])) {
$condition .= $this->db->quote(' AND ?:ab__stickers.name_for_admin LIKE ?l', '%' . trim($params['name_for_admin']) . '%');
}
if (isset($params['name_for_desktop']) && fn_string_not_empty($params['name_for_desktop'])) {
$condition .= $this->db->quote(' AND descriptions.name_for_desktop LIKE ?l', '%' . trim($params['name_for_desktop']) . '%');
}
if (isset($params['name_for_mobile']) && fn_string_not_empty($params['name_for_mobile'])) {
$condition .= $this->db->quote(' AND descriptions.name_for_mobile LIKE ?l', '%' . trim($params['name_for_mobile']) . '%');
}
if (!empty($params['styles']) && fn_string_not_empty($params['styles'][0])) {
$condition .= $this->db->quote(' AND ?:ab__stickers.style IN (?a)', $params['styles']);
}
if (!empty($params['with_conditions']) && $params['with_conditions'] === true) {
$condition .= $this->db->quote(' AND ?:ab__stickers.conditions LIKE "%?p%"', 'conditions');
}
if (!empty($params['appearance_style']) && in_array($params['appearance_style'], array_keys(fn_ab__stickers_sticker_get_ts_appearance_styles()))) {
$condition_part = $this->db->quote('?:ab__stickers.appearance LIKE \'?p\'', '%"' . $params['appearance_style'] . '"%');
if ($params['appearance_style'] == $this->settings['ts_appearance']) {
$condition_part = '(' . $condition_part . $this->db->quote(' OR ?:ab__stickers.appearance LIKE \'%?p%\' OR ?:ab__stickers.appearance NOT LIKE \'%"appearance_style"%\')', '"appearance_style":""');
}
$condition .= ' AND ' . $condition_part . $this->db->quote(' AND ?:ab__stickers.style IN (?a)', [StickerStyles::TEXT, StickerStyles::PICTOGRAM]);
}
if (!empty($params['update_time_to'])) {
$condition .= $this->db->quote(' AND ?:ab__stickers.last_update_time <= ?i', $params['update_time_to']);
} elseif (!empty($params['update_time_from'])) {
$condition .= $this->db->quote(' AND ?:ab__stickers.last_update_time >= ?i', $params['update_time_from']);
}
if (!empty($params['update_time_to_p'])) {
$condition .= $this->db->quote(' AND ?:ab__stickers.last_update_time_pictogram <= ?i', $params['update_time_to_p']);
} elseif (!empty($params['update_time_from_p'])) {
$condition .= $this->db->quote(' AND ?:ab__stickers.last_update_time_pictogram >= ?i', $params['update_time_from_p']);
}
if (isset($params['display_place']) && fn_string_not_empty($params['display_place'])) {
$condition .= $this->db->quote(' AND REPLACE(REPLACE(?:ab__stickers.display_place, "/", ""), "\\\", "") NOT LIKE \'?p\'', '%"' . str_replace(['/', '\\'], '', $params['display_place']) . '":"' . StickerPlaces::NOT_DISPLAY . '"%');
}
if (!empty($params['usergroup_ids'])) {
$condition .= ' AND (' . fn_find_array_in_set($this->auth['usergroup_ids'], '?:' . $this->table . '.usergroup_ids', true) . ')';
}
if (fn_allowed_for('MULTIVENDOR')) {
if ($this->settings['enable_for_vendors'] === YesNo::YES && $params['area'] == SiteArea::ADMIN_PANEL) {
$join .= $this->db->quote(' LEFT JOIN ?:ab__vendor_stickers ON ?:ab__vendor_stickers.sticker_id = ?:ab__stickers.sticker_id');
$fields[] = '?:ab__vendor_stickers.vendor_status';
$fields[] = '?:ab__vendor_stickers.company_id';
if (!empty($params['company_id'])) {
$join .= $this->db->quote(' AND ?:ab__vendor_stickers.company_id = ?i', $params['company_id']);
$condition .= $this->db->quote(' AND ?:ab__stickers.vendor_edit = ?s', YesNo::YES);
}
}
}
if (!empty($params['items_per_page'])) {
$params['total_items'] = $this->db->getField('SELECT COUNT(DISTINCT(?:ab__stickers.sticker_id)) FROM ?:ab__stickers ?p WHERE ?p', $join, $condition);
$limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
}

fn_set_hook('ab__stickers_get_stickers_before_select', $params, $fields, $join, $condition, $lang_code);
if (!empty($params['count_only']) && $params['count_only'] !== 'sl') {
return $this->db->getField('SELECT COUNT(*) FROM ?:ab__stickers ?p WHERE ?p', $join, $condition);
}
$query = $this->db->quote('SELECT ?p FROM ?:ab__stickers ?p WHERE ?p ?p ?p', implode(', ', $fields), $join, $condition, $sorting, $limit);
if ($params['get_query'] === true) {
return [$query, $params];
}
$stickers = $this->db->getHash($query, 'sticker_id');
if (!empty($params['count_only'])) {
return count($stickers);
}
} catch (DatabaseException $e) {
ErrorHandler::handleException($e);
}
$icons = [];
if ($params['get_icons'] !== false) {
$_g_stickers = array_filter($stickers, function ($item) {
return $item['style'] == StickerStyles::GRAPHIC || $item['style'] == StickerStyles::PICTOGRAM;
});
if (!empty($_g_stickers)) {
$icons = fn_get_image_pairs(array_column($_g_stickers, 'image_id'), 'ab__stickers', ImagePairTypes::MAIN, true, true, $lang_code);
}
}
foreach ($stickers as &$sticker) {
if (empty($sticker['image_id']) && $lang_code !== DEFAULT_LANGUAGE) {
static $descriptions = [];
if (empty($descriptions)) {
$descriptions = $this->findStickerDescriptions(array_keys($stickers), DEFAULT_LANGUAGE, true);
}
if (!empty($descriptions[$sticker['sticker_id']])) {
$sticker = array_merge($sticker, $descriptions[$sticker['sticker_id']]);
}
}
foreach ($this->getEncodedFieldsList() as $field) {
$sticker[$field] = fn_ab__stickers_decode_data($sticker[$field] ?? []);
}
if ($params['area'] !== SiteArea::ADMIN_PANEL) {
if (fn_allowed_for('MULTIVENDOR') && Registry::get('addons.ab__stickers.enable_for_vendors') === YesNo::YES) {
static $statuses = [];
if (empty($statuses)) {

$repository = Tygh::$app['addons.ab__stickers.vendors_repository'];
$statuses = $repository->getStickersVendorData(array_keys($stickers));
}
$sticker['vendors_data'] = $statuses[$sticker['sticker_id']] ?? [];
}
}
if (!empty($sticker['image_id']) && !empty($icons[$sticker['image_id']])) {
$sticker['main_pair'] = reset($icons[$sticker['image_id']]);
}
if ($params['area'] === SiteArea::ADMIN_PANEL && $sticker['style'] === StickerStyles::PICTOGRAM && $pictogram_image_id = fn_ab__stickers_pictograms_get_image_id($sticker['sticker_id'], 0, $lang_code)) {
$sticker['main_pair'] = fn_get_image_pairs($pictogram_image_id, 'ab__pictograms', ImagePairTypes::MAIN, true, true, $lang_code);
} elseif ($params['area'] === SiteArea::STOREFRONT && $sticker['style'] === StickerStyles::PICTOGRAM) {
$sticker['pictogram_data'] = fn_ab__stickers_pictograms_get_texts($sticker['sticker_id'], null, $lang_code);
}
}

fn_set_hook('ab__stickers_get_stickers_post', $params, $stickers, $lang_code);
return [$stickers, $params];
}

protected function findStickerDescriptions($ids, $lang_code, $get_images)
{
try {
$descriptions = $this->db->getHash('SELECT * FROM ?:?p WHERE ?p IN (?n) AND lang_code = ?s', $this->key, $this->descriptions_table, $this->key, $ids, $lang_code);
} catch (DatabaseException $e) {
ErrorHandler::handleException($e);
}
if ($get_images === true) {
static $icons = [];
if (empty($icons)) {
$icons = fn_get_image_pairs(array_column($descriptions, 'image_id'), 'ab__stickers', ImagePairTypes::MAIN, true, true, $lang_code);
}
foreach ($descriptions as &$description) {
if (!empty($description['image_id']) && !empty($icons[$description['image_id']])) {
$description['main_pair'] = reset($icons[$description['image_id']]);
}
}
}
return $descriptions;
}

public function getStickerIds()
{
return $this->db->getColumn('SELECT ?p FROM ?:?p', $this->key, $this->table);
}

public function duplicateImagesForLanguage($ids, $lang_to = DESCR_SL, $lang_from = DEFAULT_LANGUAGE)
{
try {
$image_ids = $this->db->getSingleHash('SELECT ?p, image_id FROM ?:?p WHERE ?p IN (?n)', [$this->key, 'image_id'], $this->key, $this->images_table, $this->key, $ids);
$images = fn_get_image_pairs($image_ids, 'ab__stickers', ImagePairTypes::MAIN, true, true, $lang_from);
foreach ($ids as $id) {
$data = [
'image_id' => '',
$this->key => $id,
'lang_code' => $lang_to,
];
$image_id = $this->db->query('INSERT INTO ?:?p ?e', $this->images_table, $data);
if (!empty($images[$image_ids[$id]])) {
fn_clone_image_pairs($image_id, $image_ids[$id], 'ab__stickers', $lang_to);
}
}
} catch (DatabaseException $e) {
ErrorHandler::handleException($e);
}
}

public function getEncodedFieldsList($exclude = [])
{
$encoded_fields = $this->encoded_fields;
if (!empty($exclude)) {
$encoded_fields = array_filter($encoded_fields, function ($encode_field) use ($exclude) {
return !in_array($encode_field, $exclude);
});
}
return $encoded_fields;
}
private function getStorefrontIds()
{

$repository = Tygh::$app['storefront.repository'];
return empty($this->storefront) ? implode(',', array_keys($repository->find()[0])) : $this->storefront->storefront_id;
}
}
