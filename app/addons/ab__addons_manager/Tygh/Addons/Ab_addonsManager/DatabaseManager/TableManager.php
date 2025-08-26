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
namespace Tygh\Addons\Ab_addonsManager\DatabaseManager;
use Tygh\Addons\Ab_addonsManager\Exceptions;
class TableManager
{
protected $table_name;
public function __construct(string $table_name)
{
$this->table_name = $table_name;
return $this;
}
public function getTableName()
{
return $this->table_name;
}
public function hasTable(string $table_name = '')
{
if (empty($table_name)) {
$table_name = $this->table_name;
}
return db_has_table($table_name);
}
public function renameTable(string $new_table_name = '')
{
if (empty($new_table_name)) {
throw new Exceptions\ABTMException('The new table name is empty!');
}
if ($this->hasTable($new_table_name)) {
throw new Exceptions\ABTMException(sprintf('The table "%s" already exists!', $new_table_name));
}
db_query("RENAME TABLE ?:{$this->table_name} TO ?:{$new_table_name}");
$this->table_name = $new_table_name;
return true;
}
public function truncateTable()
{
if (!$this->hasTable()) {
throw new Exceptions\ABTMException(sprintf('The table "%s" does not exist!', $this->table_name));
}
db_query("TRUNCATE TABLE ?:{$this->table_name}");
return true;
}
public function dropTable()
{
if (!$this->hasTable()) {
throw new Exceptions\ABTMException(sprintf('The table "%s" does not exist!', $this->table_name));
}
db_query("DROP TABLE IF EXISTS ?:{$this->table_name}");
return true;
}
public function checksumTable(string $table_name = '')
{
if (!empty($table_name) && !$this->hasTable()) {
throw new Exceptions\ABTMException(sprintf('The table "%s" does not exist!', $this->table_name));
}
if (empty($table_name)) {
$table_name = $this->table_name;
}
return db_get_row("CHECKSUM TABLE ?:{$table_name}")['Checksum'];
}
public function copyTable(string $copy_table_name, $drop_if_exist = false)
{
if (empty($copy_table_name)) {
throw new Exceptions\ABTMException('The copy table name is empty!');
}
if (db_has_table($copy_table_name)) {
if (!$drop_if_exist) {
throw new Exceptions\ABTMException(sprintf('The copy table "%s" already exists!', $copy_table_name));
}
db_query("DROP TABLE ?:{$copy_table_name}");
}
db_query("CREATE TABLE ?:{$copy_table_name} LIKE ?:{$this->table_name}");
db_query("INSERT INTO ?:{$copy_table_name} SELECT * FROM ?:{$this->table_name}");
if ($this->checksumTable($copy_table_name) != $this->checksumTable()) {
throw new Exceptions\ABTMException(sprintf('The copied table "%s" is not equivalent to the original table "%s"!', $copy_table_name, $this->table_name));
}
return true;
}
public function getStructure()
{
return db_get_row("SHOW CREATE TABLE ?:{$this->table_name}")['Create Table'];
}
public function getTableComment()
{
$table_info = db_get_row("SHOW TABLE STATUS WHERE Name='?:{$this->table_name}'");
return $table_info['Comment'];
}
public function setTableComment(string $comment = '')
{
db_query("ALTER TABLE ?:{$this->table_name} COMMENT = ?s", $comment);
return true;
}
public function getColumnsDefinition()
{
return db_get_hash_array("SHOW COLUMNS FROM ?:{$this->table_name}", 'Field');
}
public function getColumns()
{
return array_keys($this->getColumnsDefinition());
}
public function getColumnDefinition(string $column_name = '')
{
if (!$this->hasColumn($column_name)) {
throw new Exceptions\ABTMException(sprintf('The column "%s" don\'t exist!', $column_name));
}
$table_data = db_get_row("SHOW CREATE TABLE ?:{$this->table_name}")['Create Table'];
$definition = '';
if (preg_match_all('/^\s*[`"](.*?)[`"]\s+(.*?),?$/m', $table_data, $matches)) {
foreach ($matches[1] as $i => $c) {
if ($c == $column_name) {
$definition = $matches[2][$i];
break;
}
}
}
return $definition;
}
public function hasColumn($column_name)
{
return in_array($column_name, $this->getColumns());
}
public function addColumn(string $column_name = '', string $definition = '')
{
if (empty($column_name)) {
throw new Exceptions\ABTMException('The column name is empty!');
}
if ($this->hasColumn($column_name)) {
throw new Exceptions\ABTMException(sprintf('The column "%s" already exist!', $column_name));
}
if (empty($definition)) {
throw new Exceptions\ABTMException(sprintf('The column definition "%s" is empty!', $column_name));
}
db_query("ALTER TABLE ?:{$this->table_name} ADD COLUMN {$column_name} {$definition}");
return true;
}
public function renameColumn($column_name, $new_column_name)
{
if (!$this->hasColumn($column_name)) {
throw new Exceptions\ABTMException(sprintf('The old column "%s" don\'t exist!', $column_name));
}
if ($this->hasColumn($new_column_name)) {
throw new Exceptions\ABTMException(sprintf('The new column "%s" already exist!', $new_column_name));
}
$table_data = db_get_row("SHOW CREATE TABLE ?:{$this->table_name}")['Create Table'];
if (preg_match_all('/^\s*[`"](.*?)[`"]\s+(.*?),?$/m', $table_data, $matches)) {
foreach ($matches[1] as $i => $c) {
if ($c == $column_name) {
db_query("ALTER TABLE ?:{$this->table_name} CHANGE {$column_name} {$new_column_name} {$matches[2][$i]}");
return true;
}
}
}
return false;
}
public function modifyColumn($column_name, $definition)
{
if (!$this->hasColumn($column_name)) {
throw new Exceptions\ABTMException(sprintf('The column "%s" don\'t exist!', $column_name));
}
if (empty($definition)) {
throw new Exceptions\ABTMException(sprintf('The column definition "%s" is empty!', $column_name));
}
db_query("ALTER TABLE ?:{$this->table_name} MODIFY COLUMN {$column_name} {$definition}");
return true;
}
public function dropColumn($column_name)
{
if (!$this->hasColumn($column_name)) {
throw new Exceptions\ABTMException(sprintf('The column "%s" don\'t exist!', $column_name));
}
db_query("ALTER TABLE ?:{$this->table_name} DROP COLUMN {$column_name}");
return true;
}
public function getIndexesDefinition()
{
$index_list = [];
$all_indexes = db_get_hash_multi_array('SHOW INDEX FROM ?:' . $this->table_name, ['Key_name']);
if (!empty($all_indexes)) {
foreach ($all_indexes as $index_name => $index_items) {
foreach ($index_items as $index_item) {
$index_list[$index_name]['fields'][] = $index_item['Column_name'];
}
}
}
return $index_list;
}
public function getIndexes()
{
return array_keys($this->getIndexesDefinition());
}
public function hasIndex($index_name)
{
if (empty($index_name)) {
throw new Exceptions\ABTMException('The index name is empty!');
}
return in_array($index_name, $this->getIndexes());
}
public function addPrimaryKey(array $columns, $add_auto_increment = false)
{
if ($this->hasIndex('PRIMARY')) {
throw new Exceptions\ABTMException('PRIMARY already exists!');
}
if (empty($columns)) {
throw new Exceptions\ABTMException('The column list is empty!');
}
$existing_columns = array_filter($columns, function ($column) { return $this->hasColumn($column); });
if (empty($existing_columns) || count($existing_columns) != count($columns)) {
throw new Exceptions\ABTMException(sprintf('The column list "%s" contains non-existent column!', implode(', ', $columns)));
}
$index_columns = implode(', ', $columns);
db_query("ALTER TABLE ?:{$this->table_name} ADD PRIMARY KEY ($index_columns)");
if (count($columns) == 1 && $add_auto_increment == true) {
$column = $columns[0];
$this->modifyColumn($column, $this->getColumnDefinition($column) . ' auto_increment');
}
return true;
}
public function dropPrimaryKey()
{
if ($this->hasIndex('PRIMARY')) {
$primary = $this->getIndexesDefinition()['PRIMARY'];
if (count($primary['fields']) == 1) {
$column_name = $primary['fields'][0];
$column_definition = $this->getColumnDefinition($column_name);
if (stripos($column_definition, 'auto_increment') !== false) {
$column_definition = str_ireplace('auto_increment', '', $column_definition);
$this->modifyColumn($column_name, $column_definition);
}
}
db_query("ALTER TABLE ?:{$this->table_name} DROP PRIMARY KEY");
}
return true;
}
public function addIndex(string $index_name, array $columns, $unique = false)
{
if ($this->hasIndex($index_name)) {
throw new Exceptions\ABTMException('The index already exists!');
}
if (empty($columns)) {
throw new Exceptions\ABTMException('The column list is empty!');
}
$existing_columns = array_filter($columns, function ($column) { return $this->hasColumn($column); });
if (empty($existing_columns) || count($existing_columns) != count($columns)) {
throw new Exceptions\ABTMException('The column list contains non-existent column!');
}
$index_columns = implode(', ', $columns);
$index_unique = $unique ? 'UNIQUE' : '';
db_query("ALTER TABLE ?:{$this->table_name} ADD {$index_unique} KEY {$index_name} ({$index_columns})");
return true;
}
public function dropIndex($index_name)
{
if (empty($index_name)) {
throw new Exceptions\ABTMException('The index name is empty!');
}
if ($this->hasIndex($index_name)) {
db_query("ALTER TABLE ?:{$this->table_name} DROP INDEX {$index_name}");
}
return true;
}
}
