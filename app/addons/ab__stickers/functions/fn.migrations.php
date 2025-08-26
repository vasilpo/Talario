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
use Tygh\Addons\Ab_addonsManager\DatabaseManager\TableManager;
use Tygh\Enum\Addons\Ab_stickers\OutputPositions;
use Tygh\Enum\NotificationSeverity;
defined('BOOTSTRAP') or die('Access denied');
function fn_ab__stickers_install()
{
$objects = [
['t' => '?:products',
'i' => [
['n' => 'ab__stickers_manual_ids', 'p' => 'mediumtext'],
['n' => 'ab__stickers_generated_ids', 'p' => 'mediumtext'],
],
],
];
if (!empty($objects) && is_array($objects)) {
foreach ($objects as $o) {
$fields = db_get_fields('DESCRIBE ' . $o['t']);
if (!empty($fields) && is_array($fields)) {
if (!empty($o['i']) && is_array($o['i'])) {
foreach ($o['i'] as $f) {
if (!in_array($f['n'], $fields)) {
db_query('ALTER TABLE ?p ADD ?p ?p', $o['t'], $f['n'], $f['p']);
if (!empty($f['add_sql']) && is_array($f['add_sql'])) {
foreach ($f['add_sql'] as $sql) {
db_query($sql);
}
}
}
}
}
if (!empty($o['indexes']) && is_array($o['indexes'])) {
foreach ($f['indexes'] as $index => $keys) {
$existing_indexes = db_get_array('SHOW INDEX FROM ' . $o['t'] . ' WHERE key_name = ?s', $index);
if (empty($existing_indexes) && !empty($keys)) {
db_query('ALTER TABLE ?p ADD INDEX ?p (?p)', $o['t'], $index, $keys);
}
}
}
}
}
}
fn_ab__stickers_migrate_v130_v120();
fn_ab__stickers_migrate_v150_v140();
fn_ab__stickers_migrate_v160_v150();
fn_ab__stickers_migrate_v200_v1111();
fn_ab__stickers_migrate_v210_v201();
fn_ab__stickers_migrate_v300_v220();
}

function fn_ab__stickers_before_install()
{
return fn_ab__stickers_migrate_v300_v220();
}

function fn_ab__stickers_migrate_v130_v120()
{
$description_table = '?:ab__sticker_descriptions';
$setting_type_exists = db_get_field("SHOW COLUMNS FROM {$description_table} WHERE `Field` = 'image_id'");
if (!empty($setting_type_exists)) {
db_query("ALTER TABLE {$description_table} DROP PRIMARY KEY");
db_query("ALTER TABLE {$description_table} DROP COLUMN image_id");
db_query("ALTER TABLE {$description_table} DROP INDEX sticker_lang_code_key");
db_query("ALTER TABLE {$description_table} ADD PRIMARY KEY (sticker_id, lang_code)");
}
}

function fn_ab__stickers_migrate_v150_v140()
{
$table = '?:ab__stickers';
$column = 'usergroup_ids';
$after = 'storefront_ids';
$setting_exists = db_get_field('SHOW COLUMNS FROM ?p WHERE `Field` = ?s', $table, $column);
if (empty($setting_exists)) {
db_query('ALTER TABLE ?p ADD COLUMN ?p AFTER ?p', $table, $column . ' varchar(255) NOT NULL DEFAULT \'0\'', $after);
db_query('ALTER TABLE ?p DROP INDEX status', $table);
db_query('CREATE INDEX c_status ON ?p (status, ?p)', $table, $column);
}
}

function fn_ab__stickers_migrate_v160_v150()
{
$table = '?:ab__stickers';
$column = 'vendor_edit';
$column_data = 'char(1) NOT NULL DEFAULT \'N\'';
$after = 'last_update_time';
$setting_exists = db_get_field('SHOW COLUMNS FROM ?p WHERE `Field` = ?s', $table, $column);
if (empty($setting_exists)) {
db_query('ALTER TABLE ?p ADD COLUMN ?p AFTER ?p', $table, $column . ' ' . $column_data, $after);
db_query('CREATE TABLE IF NOT EXISTS ?:ab__vendor_stickers (
sticker_id mediumint(8) unsigned NOT NULL DEFAULT 0,
company_id int(11) unsigned NOT NULL DEFAULT 0,
vendor_status char(1) NOT NULL DEFAULT \'A\',
CONSTRAINT sticker_company_id UNIQUE (sticker_id, company_id)
) DEFAULT CHARSET=utf8;');
}
}

function fn_ab__stickers_migrate_v200_v1111()
{
$table = '?:ab__stickers';
$column = 'last_update_time_pictogram';
$column_data = 'int(11) unsigned NOT NULL DEFAULT 0';
$after = 'last_update_time';
$column_exists = db_get_field('SHOW COLUMNS FROM ?p WHERE `Field` = ?s', $table, $column);
if (empty($column_exists)) {
db_query('ALTER TABLE ?p ADD COLUMN ?p AFTER ?p', $table, $column . ' ' . $column_data, $after);
$column = 'pictogram_data';
$column_data = 'mediumtext';
$after = 'appearance';
db_query('ALTER TABLE ?p ADD COLUMN ?p AFTER ?p', $table, $column . ' ' . $column_data, $after);
$table = '?:ab__sticker_descriptions';
$after = 'description';
db_query('ALTER TABLE ?p ADD COLUMN ?p AFTER ?p', $table, $column . ' ' . $column_data, $after);
}
db_query('CREATE TABLE IF NOT EXISTS ?:ab__sticker_pictograms (
image_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
sticker_id mediumint(8) unsigned NOT NULL,
product_id mediumint(8) UNSIGNED NOT NULL,
lang_code char(2) NOT NULL,
PRIMARY KEY (image_id), UNIQUE pictogram (sticker_id, product_id, lang_code)
) DEFAULT CHARSET=utf8;');
}

function fn_ab__stickers_migrate_v210_v201()
{
$table = new TableManager('ab__sticker_pictograms');
if ($table->hasIndex('pictogram')) {
$table->dropIndex('pictogram');
$table->dropPrimaryKey();
$table->addIndex('pictogram_image', ['image_id', 'sticker_id', 'product_id', 'lang_code'], true);
$table->modifyColumn('image_id', 'mediumint(8) AUTO_INCREMENT');
}
}

function fn_ab__stickers_migrate_v300_v220($source = 'addon_xml')
{
if ($source === 'addon_xml') {
$table_to_check = ['ab__stickers', 'ab__sticker_descriptions', 'ab__sticker_pictograms'];
foreach ($table_to_check as $table) {
$table_exist = db_get_row('SHOW TABLES LIKE \'?:?p\'', $table);
if (!$table_exist) {
return true;
}
}
}
$table = new TableManager('ab__stickers');
if (!$table->hasColumn('display_on')) {
try {
$page = 0;
$displays = fn_ab__stickers_get_templates_displays();
$unserialize_fields = ['display_on_lists', 'appearance', 'pictogram_data', 'conditions'];
$serialize_fields = [
'display_place', 'display_on', 'output_position', 'user_class', 'appearance',
'pictogram_data', 'conditions'
];
$unset_fields = [
'sticker_id', 'display_on_lists', 'display_on_detailed_pages', 'output_position_list',
'output_position_detailed_page'
];
$select_fields = implode(', ', [
'sticker_id', 'display_on_lists', 'display_on_detailed_pages',
'output_position_list', 'output_position_detailed_page', 'appearance', 'pictogram_data', 'conditions'
]);
$stickers_data = [];
while ($stickers = db_get_hash_array("SELECT {$select_fields} FROM ?:?p ORDER BY sticker_id ASC LIMIT ?i, 100", 'sticker_id', $table->getTableName(), $page)) {
$page += 100;
foreach ($stickers as $sticker_id => $sticker) {
foreach ($unserialize_fields as $field) {
if (isset($sticker[$field])) {
$sticker[$field] = empty($sticker[$field]) ? [] : unserialize($sticker[$field]);
}
}
$sticker_data = $sticker;
foreach ($displays as $display_id => $display) {
if (in_array($display_id, ['detailed_pages', 'detailed_page', 'product_details'])) {
$display['tpl'] = 'detailed_page';
$sticker_data['display_on'][$display['tpl']] = $sticker['display_on_detailed_pages'] ?? 'full_size';
$sticker_data['output_position'][$display['tpl']] = $sticker['output_position_detailed_page'] ?? OutputPositions::TOP;
$sticker_data['user_class'][$display['tpl']] = $sticker['appearance']['detailed_page']['user_class'] ?? '';
} else {
$sticker_data['display_on'][$display['tpl']] = $sticker['display_on_lists'][$display['tpl']] ?? $display['display_size'];
$sticker_data['output_position'][$display['tpl']] = $sticker['output_position_list'] ?? OutputPositions::TOP;
$sticker_data['user_class'][$display['tpl']] = $sticker['appearance']['list']['user_class'] ?? '';
}
$display_place = 'product_labels';
$prohibited_product_labels = [
'blocks/products/products_links_thumb.tpl',
'blocks/products/products_small_items.tpl'
];
if (in_array($display['tpl'], $prohibited_product_labels)) {
$sticker_data['display_on'][$display['tpl']] = 'not_display';
}
if ($sticker_data['display_on'][$display['tpl']] === 'not_display') {
$display_place = 'not_display';
$sticker_data['display_on'][$display['tpl']] = 'small_size';
}
$sticker_data['display_place'][$display['tpl']] = $display_place;
}
foreach ($serialize_fields as $key) {
if (!empty($sticker_data[$key]) && is_array($sticker_data[$key])) {
$sticker_data[$key] = json_encode($sticker_data[$key]);
} else {
$sticker_data[$key] = '';
}
}
foreach ($unset_fields as $field) {
unset($sticker_data[$field]);
}
$stickers_data[$sticker_id] = $sticker_data;
}
}
$stickers_table_func = function () use ($table, $stickers_data) {
try {
$table_data = db_get_row('SHOW TABLE STATUS LIKE \'?:?p\'', $table->getTableName());
if (empty($table_data['Engine']) || $table_data['Engine'] !== 'InnoDB') {
db_query('ALTER TABLE ?:?p ENGINE = InnoDB', $table->getTableName());
}
$columns = [
'add' => [
['n' => 'display_on', 'd' => 'MEDIUMTEXT AFTER style'],
['n' => 'display_place', 'd' => 'MEDIUMTEXT AFTER display_on'],
['n' => 'output_position', 'd' => 'MEDIUMTEXT AFTER display_place'],
['n' => 'user_class', 'd' => 'MEDIUMTEXT AFTER output_position']
],
'drop' => [
'display_on_lists',
'display_on_detailed_pages',
'output_position_list',
'output_position_detailed_page'
]
];
foreach ($columns['add'] as $column) {
if (!$table->hasColumn($column['n'])) {
$table->addColumn($column['n'], $column['d'] ?? '');
}
}
foreach ($stickers_data as $sticker_id => $sticker) {
db_query('UPDATE ?:?p SET ?u WHERE sticker_id = ?i', $table->getTableName(), $sticker, $sticker_id);
}
$indexes = [
'add' => [],
'drop' => [
'output_position_list',
'output_position_detailed_page'
]
];
foreach ($indexes['drop'] as $index) {
if ($table->hasIndex($index)) {
$table->dropIndex($index);
}
}
foreach ($columns['drop'] as $column) {
if ($table->hasColumn($column)) {
$table->dropColumn($column);
}
}
foreach ($indexes['add'] as $index) {
if (!$table->hasIndex($index['n'])) {
$table->addIndex($index['n'], $index['c'], $index['u']);
}
}
} catch (Exception $e) {
return false;
}
return true;
};
$table = new TableManager('ab__sticker_descriptions');
$stickers_data = [];
$page = 0;
while ($stickers = db_get_hash_multi_array('SELECT sticker_id, lang_code, pictogram_data FROM ?:?p ORDER BY sticker_id ASC, lang_code ASC LIMIT ?i, 100', ['lang_code', 'sticker_id'], $table->getTableName(), $page)) {
$page += 100;
foreach ($stickers as $lang_code => $sticker) {
foreach ($sticker as $sticker_id => $sticker_data) {
$sticker_data['pictogram_data'] = empty($sticker_data['pictogram_data']) ? [] : unserialize($sticker_data['pictogram_data']);
if (!empty($sticker_data['pictogram_data']) && is_array($sticker_data['pictogram_data'])) {
$sticker_data['pictogram_data'] = json_encode($sticker_data['pictogram_data']);
} else {
$sticker_data['pictogram_data'] = '';
}
$stickers_data[$lang_code][$sticker_id] = $sticker_data;
}
}
}
$sticker_descriptions_table_func = function () use ($table, $stickers_data) {
try {
$table_data = db_get_row('SHOW TABLE STATUS LIKE \'?:?p\'', $table->getTableName());
if (empty($table_data['Engine']) || $table_data['Engine'] !== 'InnoDB') {
db_query('ALTER TABLE ?:?p ENGINE = InnoDB', $table->getTableName());
}
foreach ($stickers_data as $lang_code => $stickers) {
foreach ($stickers as $sticker_id => $sticker) {
db_query('UPDATE ?:?p SET ?u WHERE sticker_id = ?i AND lang_code = ?s', $table->getTableName(), $sticker, $sticker_id, $lang_code);
}
}
} catch (Exception $e) {
return false;
}
return true;
};
$sticker_pictograms_table_func = function () {
$table = new TableManager('ab__sticker_pictograms');
if (!$table->hasIndex('pictogram_index')) {
$imag_id_definition = $table->getColumnDefinition('image_id');
if (stripos($imag_id_definition, 'AUTO_INCREMENT')) {
$imag_id_definition = str_ireplace('AUTO_INCREMENT', '', $imag_id_definition);
$table->modifyColumn('image_id', $imag_id_definition);
}
$table->dropPrimaryKey();
foreach ($table->getIndexes() as $index) {
$table->dropIndex($index);
}
$table->addIndex('image_id_index', ['image_id']);
$table->modifyColumn('image_id', 'mediumint(8) unsigned NOT NULL AUTO_INCREMENT');
$table->addIndex('pictogram_index', ['sticker_id', 'product_id', 'lang_code']);
$table->addIndex('pictogram_unique', ['sticker_id', 'product_id', 'lang_code'], true);
}
};
$result = db_transaction(function () use ($stickers_table_func, $sticker_descriptions_table_func, $sticker_pictograms_table_func) {
return $stickers_table_func() && $sticker_descriptions_table_func() && $sticker_pictograms_table_func();
});
if ($result !== true) {
throw new Exception('transaction error!');
}
} catch (Exception $exception) {
if ($source === 'addon_xml') {
fn_set_notification(NotificationSeverity::ERROR, __('error'), 'Migration error: ' . $exception->getMessage());
return false;
}
return [false, [__('error') => 'Migration error: ' . $exception->getMessage()]];
}
}
if ($source === 'addon_xml') {
return true;
}
return [true, []];
}
