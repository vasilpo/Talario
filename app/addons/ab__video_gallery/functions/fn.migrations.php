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
use Tygh\Enum\YesNo;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
function fn_ab__vg_migrate_v300_v250()
{
$table = '?:ab__video_gallery_descriptions';
$exists = db_get_field('SHOW COLUMNS FROM ?p WHERE Field = ?s', $table, 'video_path');
if (empty($exists)) {
db_query('ALTER TABLE ?p CHANGE youtube_id video_path VARCHAR(255) NOT NULL DEFAULT ""', $table);
db_query('ALTER TABLE ?p MODIFY video_path VARCHAR(255) NOT NULL DEFAULT ""', $table);
$table = '?:ab__video_gallery';
$column = 'type';
$column_data = 'CHAR(1) NOT NULL DEFAULT "Y"';
db_query('ALTER TABLE ?p ADD COLUMN ?p AFTER video_id', $table, $column . ' ' . $column_data);
$column = 'settings';
$column_data = 'TEXT NOT NULL';
db_query('ALTER TABLE ?p ADD COLUMN ?p AFTER icon_type', $table, $column . ' ' . $column_data);
}
}
function fn_ab__vg_migrate_v360_v350()
{
$columns = db_get_fields('DESCRIBE ?:ab__video_gallery');
if (!in_array('storefront_id', $columns)) {
db_query('ALTER TABLE ?:ab__video_gallery ADD COLUMN storefront_id INT(11) UNSIGNED NOT NULL DEFAULT 0');
}
}
function fn_ab__vg_migrate_v370_v360()
{
$columns = db_get_fields('DESCRIBE ?:ab__video_gallery');
if (!in_array('product_pos_type', $columns)) {
db_query('ALTER TABLE ?:ab__video_gallery ADD COLUMN product_pos_type CHAR(1) NOT NULL DEFAULT ?s AFTER pos', VideoProductPositionTypes::DEFAULT);
}
if (!in_array('product_pos', $columns)) {
db_query('ALTER TABLE ?:ab__video_gallery ADD COLUMN product_pos SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 AFTER product_pos_type');
}
if (!in_array('autoplay', $columns)) {
db_query('ALTER TABLE ?:ab__video_gallery ADD COLUMN autoplay CHAR(1) NOT NULL DEFAULT ?s AFTER product_pos', YesNo::NO);
}
if (!in_array('show_in_list', $columns)) {
db_query('ALTER TABLE ?:ab__video_gallery ADD COLUMN show_in_list CHAR(1) NOT NULL DEFAULT ?s AFTER autoplay', YesNo::NO);
}
}