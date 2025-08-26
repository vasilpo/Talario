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
use Tygh\Enum\YesNo;
use Tygh\Registry;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\Addons\Ab_motivationBlock\ObjectTypes as Ab_objectTypes;
if (!defined('BOOTSTRAP')) {
die;
}
function fn_ab__mb_migration_from_v220_to_v230()
{
$new_table_name = 'ab__mb_motivation_item_objects';
$new_table_empty = false;
$old_columns = [
Ab_objectTypes::CATEGORY => 'categories_ids',
Ab_objectTypes::DESTINATION => 'destinations_ids',];
$rows_count = db_get_field('SELECT COUNT(*) FROM ?:?f', $new_table_name);
if (empty($rows_count)) {
$new_table_empty = true;
}
if ($new_table_empty) {
$old_column_exists = true;
$main_table_describe = db_get_fields('DESCRIBE ?:ab__mb_motivation_items');
foreach ($old_columns as $column) {
if (empty($main_table_describe[$column])) {
$old_column_exists = false;
}
}
if ($old_column_exists) {
$items = db_get_hash_array('SELECT motivation_item_id, ?p FROM ?:ab__mb_motivation_items', 'motivation_item_id', implode(', ', $old_columns));
$insert_vals = [];
$count = 0;
foreach ($items as $item) {
foreach ($old_columns as $type => $old_column) {
if (!empty($item[$old_column])) {
foreach (explode(',', $item[$old_column]) as $_tmp_id) {
$insert_vals[] = "({$item['motivation_item_id']}, {$_tmp_id}, '{$type}')";
$count++;
}
} else {
$insert_vals[] = "({$item['motivation_item_id']}, 0, '{$type}')";
$count++;
}
}
}
$res = db_query('INSERT INTO ?:?f (motivation_item_id, object_id, object_type)
VALUES ?p', $new_table_name, implode(', ', $insert_vals));
if ($res == $count) {
foreach ($old_columns as $old_column) {
db_query('ALTER TABLE ?:?f DROP COLUMN ?f', 'ab__mb_motivation_items', $old_column);
}
}
}
}
}
function fn_ab__mb_migration_from_v230_to_v240()
{
$has_column = db_get_array('SHOW COLUMNS FROM ?:ab__mb_motivation_items LIKE "icon_class"');
if(!empty($has_column)) {
db_query('ALTER TABLE ?:ab__mb_motivation_items CHANGE icon_class icon_class varchar(64) NOT NULL DEFAULT ""');
}
}
function fn_ab__mb_migration_from_v240_to_v250()
{
db_query('UPDATE ?:ab__mb_motivation_items SET template_path = "addons/ab__motivation_block/blocks/components/item_templates/custom_content.tpl" WHERE template_path = "" OR template_path IS NULL');
}

function fn_ab__mb_migration_from_v271_to_v280()
{
$table = '?:ab__mb_motivation_items';
$exists = db_get_field('SHOW COLUMNS FROM ?p WHERE Field = ?s', $table, 'exclude_products');
if (empty($exists)) {
db_query('ALTER TABLE ?p ADD COLUMN ?p AFTER ?p', $table, 'exclude_products char(1) NOT NULL default \'N\'', 'exclude_destinations');
db_query('CREATE INDEX exc_products ON ?p (exclude_products)', $table);
}
}

function fn_ab__mb_migration_from_v290_to_v2100()
{
$table = '?:ab__mb_motivation_items';
$exists = db_get_field('SHOW COLUMNS FROM ?p WHERE Field = ?s', $table, 'storefront_id');
if (empty($exists)) {
db_query('ALTER TABLE ?p MODIFY motivation_item_id mediumint(8) unsigned NOT NULL', $table);
db_query('ALTER TABLE ?p DROP PRIMARY KEY', $table);
db_query('ALTER TABLE ?p ADD COLUMN ?p AFTER ?p', $table, 'storefront_id int(11) unsigned NOT NULL default 0', 'motivation_item_id');
db_query('ALTER TABLE ?p ADD PRIMARY KEY(motivation_item_id, storefront_id)', $table);
db_query('ALTER TABLE ?p MODIFY motivation_item_id mediumint(8) unsigned NOT NULL auto_increment', $table);
if (fn_allowed_for('MULTIVENDOR')) {
$default_storefront = Tygh::$app['storefront.repository']->findDefault();
$default_storefront_id = $default_storefront
? $default_storefront->storefront_id
: 0;
db_query("INSERT INTO {$table} (motivation_item_id, storefront_id, company_id, position, expanded, vendor_edit, status, icon_type, icon_class, icon_color, exclude_categories, exclude_destinations, exclude_products, template_path, template_settings)
SELECT motivation_item_id, {$default_storefront_id}, company_id, position, expanded, vendor_edit, status, icon_type, icon_class, icon_color, exclude_categories, exclude_destinations, exclude_products, template_path, template_settings FROM {$table} WHERE company_id = 0");
} else {
$company_ids = db_get_fields('SELECT company_id FROM ?:companies');
foreach ($company_ids as $company_id) {
$storefront_id = Tygh::$app['storefront.repository']->findByCompanyId($company_id, true)->storefront_id;
db_query('UPDATE ?p SET storefront_id = ?i WHERE company_id = ?i', $table, $storefront_id, $company_id);
}
}
db_query('ALTER TABLE ?p DROP company_id', $table);
}
}

function fn_ab__mb_migration_from_v2110_to_v2111()
{
$sf_addon = Registry::ifGet('addons.absf__motivation_block', []);
if (!empty($sf_addon)) {
fn_uninstall_addon('absf__motivation_block');
fn_rm(Registry::get('config.dir.addons') . 'absf__motivation_block', true);
fn_rm(Registry::get('config.dir.design_backend') . 'css/addons/absf__motivation_block', true);
fn_rm(Registry::get('config.dir.design_backend') . 'templates/addons/absf__motivation_block', true);
foreach (['ru', 'en', 'ua'] as $lang) {
fn_rm(Registry::get('config.dir.var') . 'langs/' . $lang . '/addons/absf__motivation_block.po');
}
fn_set_notification(NotificationSeverity::NOTICE, __('notice'), __('ab__mb.upgrade_notifications.2.11.1.sf_addon_deleted.text'));
}
}
function fn_ab__mb_migration_from_v2122_to_v2130()
{
$columns = db_get_fields('DESCRIBE ?:ab__mb_motivation_items');
if (in_array('expanded', $columns)) {
db_query('ALTER TABLE ?:ab__mb_motivation_items CHANGE `expanded` `expanded_desktop` char(1) NOT NULL default ?s', YesNo::YES);
}
if (!in_array('expanded_tablet', $columns)) {
db_query('ALTER TABLE ?:ab__mb_motivation_items ADD COLUMN `expanded_tablet` CHAR(1) NOT NULL DEFAULT ?s AFTER `expanded_desktop`', YesNo::NO);
}
if (!in_array('expanded_mobile', $columns)) {
db_query('ALTER TABLE ?:ab__mb_motivation_items ADD COLUMN `expanded_mobile` CHAR(1) NOT NULL DEFAULT ?s AFTER `expanded_tablet`', YesNo::NO);
}
}
