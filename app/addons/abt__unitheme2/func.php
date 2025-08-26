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
use Tygh\Enum\Addons\Abt_unitheme2\BlockInTabsTypes;use Tygh\Enum\OutOfStockActions;use Tygh\Enum\ProductTracking;use Tygh\Enum\YesNo;use Tygh\Registry;use Tygh\BlockManager\Block;use Tygh\Enum\SiteArea;use Tygh\Enum\ObjectStatuses;use Tygh\Enum\Addons\Abt_unitheme2\DeviceTypes;use Tygh\Addons\Abt_unitheme2\FMRepository;use Tygh\Storage;if (!defined('BOOTSTRAP')) {
die('Access denied');}
foreach (glob(Registry::get('config.dir.addons').'/abt__unitheme2/functions/fn.abt__ut2_*.php') as $functions) {
require_once $functions;}
function fn_abt__ut2_install(){
$objects=[
['t'=>'?:pages',
'i'=>[
['n'=>'abt__ut2_microdata_schema_type','p'=>'varchar(32) NOT NULL DEFAULT \'\''],
],
],
['t'=>'?:bm_grids',
'i'=>[
['n'=>'abt__ut2_extended','p'=>'char(1) NOT NULL DEFAULT \'0\''],
['n'=>'abt__ut2_padding','p'=>'varchar(20) NOT NULL DEFAULT \'\''],
],
],
['t'=>'?:bm_grids',
'i'=>[
['n'=>'abt__ut2_show_blocks_in_tabs','p'=>'enum("no","without_lazy_load","with_lazy_load") NOT NULL DEFAULT \'no\''],
['n'=>'abt__ut2_use_lazy_load','p'=>'char(1) NOT NULL DEFAULT \'N\''],
],
],
['t'=>'?:bm_locations',
'i'=>[
['n'=>'user_class','p'=>'varchar(255) NOT NULL DEFAULT \'\'']
],
],
['t'=>'?:images',
'i'=>[
['n'=>'transparent','p'=>'char(1) NOT NULL DEFAULT \'N\'']
],
],
['t'=>'?:banners',
'i'=>call_user_func(function () {
$devices_enabled_fields=[
'tablet'=>fn_get_schema('abt__ut2_banners','tablet','php',true),'mobile'=>fn_get_schema('abt__ut2_banners','mobile','php',true),];$fields=[
'usergroup_ids'=>['p'=>'varchar(255) NOT NULL DEFAULT \'0\''],
'use_avail_period'=>['p'=>'char(1) NOT NULL DEFAULT \'N\'','add_sql'=>['ALTER TABLE ?:banners CHANGE type type CHAR(20) NOT NULL DEFAULT \'G\'']],
'avail_from'=>['p'=>'int(11) NOT NULL DEFAULT \'0\''],
'avail_till'=>['p'=>'int(11) NOT NULL DEFAULT \'0\''],
'button_use'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'button_style'=>['p'=>'enum(\'normal\',\'outline\',\'text\') NOT NULL DEFAULT \'normal\''],
'button_text_color'=>['p'=>'varchar(11) NOT NULL DEFAULT \'\''],
'button_text_color_use'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'button_color'=>['p'=>'varchar(11) NOT NULL DEFAULT \'\''],
'button_color_use'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'title_font_size'=>['p'=>'varchar(7) NOT NULL DEFAULT \'18px\''],
'title_color'=>['p'=>'varchar(11) NOT NULL DEFAULT \'\''],
'title_color_use'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'title_font_weight'=>['p'=>'varchar(4) NOT NULL DEFAULT \'300\''],
'title_tag'=>['p'=>'enum(\'div\',\'h1\',\'h2\',\'h3\') NOT NULL DEFAULT \'div\''],
'background_type'=>['p'=>'varchar(64) NOT NULL DEFAULT \'image\''],
'background_image_size'=>['p'=>'enum(\'cover\',\'contain\') NOT NULL DEFAULT \'cover\''],
'image_position'=>['p'=>'enum(\'top\',\'center\',\'bottom\') NOT NULL DEFAULT \'center\''],
'title_shadow'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'description_font_size'=>['p'=>'varchar(7) NOT NULL DEFAULT \'14px\''],
'description_color'=>['p'=>'varchar(11) NOT NULL DEFAULT \'\''],
'description_color_use'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'description_bg_color'=>['p'=>'varchar(11) NOT NULL DEFAULT \'\''],
'description_bg_color_use'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'main_image'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'background_image'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'object'=>['p'=>'enum(\'image\',\'video\') NOT NULL DEFAULT \'image\''],
'background_color'=>['p'=>'varchar(11) NOT NULL DEFAULT \'\''],
'background_color_use'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'class'=>['p'=>'varchar(100) NOT NULL DEFAULT \'\''],
'color_scheme'=>['p'=>'enum(\'light\',\'dark\') NOT NULL DEFAULT \'light\''],
'content_valign'=>['p'=>'enum(\'top\',\'center\',\'bottom\') NOT NULL DEFAULT \'center\''],
'content_align'=>['p'=>'enum(\'left\',\'center\',\'right\') NOT NULL DEFAULT \'center\''],
'content_full_width'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'content_bg'=>['p'=>'varchar(20) NOT NULL DEFAULT \'none\''],
'content_bg_position'=>['p'=>'varchar(20) NOT NULL DEFAULT \'only_under_content\''],
'content_bg_align'=>['p'=>'varchar(20) NOT NULL DEFAULT \'auto\''],
'content_bg_opacity'=>['p'=>'int(2) NOT NULL DEFAULT 50'],
'content_bg_color'=>['p'=>'varchar(11) NOT NULL DEFAULT \'\''],
'content_bg_color_use'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'padding'=>['p'=>'varchar(27) NOT NULL DEFAULT \'\''],
'how_to_open'=>['p'=>'enum(\'in_this_window\',\'in_new_window\',\'in_popup\') NOT NULL DEFAULT \'in_this_window\''],
'data_type'=>['p'=>'enum(\'url\',\'blog\',\'promotion\') NOT NULL DEFAULT \'url\''],
'youtube_use'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'youtube_autoplay'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'youtube_hide_controls'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'group_id'=>['p'=>'int(11) NOT NULL DEFAULT \'0\''],
];$t=[];foreach ($fields as $f=>$data) {
if (!preg_match('/_image$/',$f)) {
$data['n']="abt__ut2_{$f}";$t[]=$data;}
foreach ($devices_enabled_fields as $device=>$device_fields) {
if (in_array($f,$device_fields)) {
if (!preg_match('/_image$/',$f)) {
$data['n']="abt__ut2_{$device}_{$f}";$t[]=$data;}
$t[]=['n'=>"abt__ut2_{$device}_{$f}_use_own",'p'=>'char(1) NOT NULL DEFAULT \'N\''];}}}
$t[]=['n'=>'abt__ut2_tablet_use','p'=>'char(1) NOT NULL DEFAULT \'N\''];$t[]=['n'=>'abt__ut2_mobile_use','p'=>'char(1) NOT NULL DEFAULT \'N\''];return $t;}),],
['t'=>'?:banner_descriptions',
'i'=>call_user_func(function () {
$devices_enabled_fields=[
'tablet'=>fn_get_schema('abt__ut2_banners','tablet'),'mobile'=>fn_get_schema('abt__ut2_banners','mobile'),];$fields=[
'button_text'=>['p'=>'varchar(50) NOT NULL DEFAULT \'\''],
'title'=>['p'=>'varchar(255) NOT NULL DEFAULT \'\''],
'url'=>['p'=>'varchar(255) NOT NULL DEFAULT \'\''],
'description'=>['p'=>'mediumtext'],
'youtube_id'=>['p'=>'varchar(15) NOT NULL DEFAULT \'\''],
'background_mp4_video'=>['p'=>'varchar(256) NOT NULL DEFAULT \'\''],
];$t=[];foreach ($fields as $f=>$data) {
$t[]=['n'=>"abt__ut2_{$f}",'p'=>$data['p']];foreach ($devices_enabled_fields as $device=>$device_fields) {
if (in_array($f,$device_fields)) {
$t[]=['n'=>"abt__ut2_{$device}_{$f}",'p'=>$data['p']];$t[]=['n'=>"abt__ut2_{$device}_{$f}_use_own",'p'=>'char(1) NOT NULL DEFAULT \'N\''];}}}
return $t;}),],
['t'=>'?:static_data',
'i'=>[
['n'=>'abt__ut2_mwi__status','p'=>'char(1) NOT NULL DEFAULT \'N\''],
['n'=>'abt__ut2_mwi__text_position','p'=>'varchar(32) NOT NULL DEFAULT \'bottom\''],
['n'=>'abt__ut2_mwi__dropdown','p'=>'char(1) NOT NULL DEFAULT \'N\''],
['n'=>'abt__ut2_mwi__label_color','p'=>'varchar(11) NOT NULL DEFAULT \'\''],
['n'=>'abt__ut2_mwi__label_background','p'=>'varchar(11) NOT NULL DEFAULT \'\''],
],
],
['t'=>'?:static_data_descriptions',
'i'=>[
['n'=>'abt__ut2_mwi__desc','p'=>'mediumtext'],
['n'=>'abt__ut2_mwi__text','p'=>'mediumtext'],
['n'=>'abt__ut2_mwi__label','p'=>'varchar(100) NOT NULL DEFAULT \'\''],
],
],
['t'=>'?:categories',
'i'=>[
['n'=>'default_view_desktop','p'=>'varchar(50) NOT NULL DEFAULT \'\''],
['n'=>'default_view_tablet','p'=>'varchar(50) NOT NULL DEFAULT \'\''],
['n'=>'default_view_mobile','p'=>'varchar(50) NOT NULL DEFAULT \'\''],
],
],
];if (!empty($objects) && is_array($objects)) {
foreach ($objects as $o) {
$fields=db_get_fields('DESCRIBE '.$o['t']);if (!empty($fields) && is_array($fields)) {
if (!empty($o['i']) && is_array($o['i'])) {
foreach ($o['i'] as $f) {
if (!in_array($f['n'],$fields)) {
db_query('ALTER TABLE ?p ADD ?p ?p',$o['t'],$f['n'],$f['p']);if (!empty($f['add_sql']) && is_array($f['add_sql'])) {
foreach ($f['add_sql'] as $sql) {
db_query($sql);}}}}}
if (!empty($o['indexes']) && is_array($o['indexes'])) {
foreach ($f['indexes'] as $index=>$keys) {
$existing_indexes=db_get_array('SHOW INDEX FROM ?p WHERE key_name=?s',$o['t'],$index);if (empty($existing_indexes) && !empty($keys)) {
db_query('ALTER TABLE ?p ADD INDEX ?p (?p)',$o['t'],$index,$keys);}}}}}}
fn_abt__ut2_refresh_icons();fn_abt__ut2_migration_v4113a_v4113b();fn_abt__ut2_migration_v4113b_v4113c();fn_abt__ut2_migration_v4115c_v4115b();fn_abt__ut2_migration_v4122a_v4121d();fn_abt__ut2_migration_v4122c_v4121b();fn_abt__ut2_migration_v4122e_v4121d();fn_abt__ut2_migration_v4122f_v4121e();fn_abt__ut2_migration_v4143c_v4143b();fn_abt__ut2_migration_v4151f_v4151e();fn_abt__ut2_migration_v4183a_v4182a();fn_abt__ut2_migration_v4183b_v4183a();}
function fn_abt__ut2_migration_v4183b_v4183a(){
$blocks=db_get_array('SELECT block_id,properties FROM ?:bm_blocks WHERE type=?s','abt__ut2_advanced_subcategories_menu');foreach ($blocks as $block){
$block['properties']=unserialize($block['properties']);$block['properties']['abt__ut2_category_count_level_2']=10;$block['properties']['abt__ut2_category_count_level_3']=10;db_query('UPDATE ?:bm_blocks SET properties=?s WHERE block_id=?i',serialize($block['properties']),$block['block_id']);}
db_query('UPDATE ?:categories
SET
default_view_desktop=default_view,
default_view_tablet=default_view,
default_view_mobile=default_view');}
function fn_abt__ut2_migration_v4183a_v4182a(){
$fields=[
'abt__ut2_products_template'=>['p'=>'varchar(50) NOT NULL DEFAULT \'\''],
'abt__ut2_products_links_thumb_columns'=>['p'=>'int(11) NOT NULL DEFAULT \'0\''],
'abt__ut2_products_links_thumb_rows'=>['p'=>'int(11) NOT NULL DEFAULT \'0\''],
'abt__ut2_products_small_items_columns'=>['p'=>'int(11) NOT NULL DEFAULT \'0\''],
'abt__ut2_products_small_items_rows'=>['p'=>'int(11) NOT NULL DEFAULT \'0\''],
'abt__ut2_products_grid_columns'=>['p'=>'int(11) NOT NULL DEFAULT \'0\''],
'abt__ut2_products_list'=>['p'=>'TEXT'],
'group_id'=>['p'=>'int(11) NOT NULL DEFAULT \'0\''],
];$describe=db_get_fields('DESCRIBE ?:banners');foreach ($fields as $field_name=>$field_data) {
if (!in_array($field_name,$describe)) {
db_query('ALTER TABLE ?:banners ADD ?p ?p',$field_name,$field_data['p']);}}
db_query('ALTER TABLE ?:banners CHANGE abt__ut2_object abt__ut2_object ENUM(\'image\',\'video\',\'products\')');db_query(
'CREATE TABLE IF NOT EXISTS ?:abt__ut2_banner_groups (
banner_group_id INT NOT NULL auto_increment,
PRIMARY KEY (banner_group_id)
) DEFAULT CHARSET=utf8;'
);db_query(
'CREATE TABLE IF NOT EXISTS ?:abt__ut2_banner_group_descriptions (
banner_group_id INT NOT NULL,
group_name VARCHAR(255) NOT NULL,
lang_code CHAR(2) NOT NULL,
PRIMARY KEY (banner_group_id,lang_code)
) DEFAULT CHARSET=utf8;'
);}
function fn_abt__ut2_migration_v4151f_v4151e(){
$old_microdata=db_get_array('SELECT * FROM ?:abt__ut2_microdata WHERE field NOT LIKE ?l','Organization_%');$migrated=db_get_array('SELECT * FROM ?:abt__ut2_microdata WHERE field LIKE ?l OR field LIKE ?l','Organization_%','Website_%');if($old_microdata && !count($migrated)){
foreach ($old_microdata as &$old_microdata_item) {
$old_microdata_item['field']='Organization_'.$old_microdata_item['field'];}
db_replace_into('abt__ut2_microdata',$old_microdata,true);}}
function fn_abt__ut2_migration_v4143c_v4143b(){

$tables=db_get_fields('DESCRIBE ?:bm_grids');if (!in_array('abt__ut2_use_lazy_load',$tables)) {
db_query('ALTER TABLE ?:bm_grids ADD ?p','abt__ut2_use_lazy_load char(1) NOT NULL DEFAULT \'N\'');}
if (!in_array('abt__ut2_show_blocks_in_tabs',$tables)) {
db_query('ALTER TABLE ?:bm_grids ADD ?p','abt__ut2_show_blocks_in_tabs enum("no","without_lazy_load","with_lazy_load") NOT NULL DEFAULT \'no\'');$grids=db_get_array('SELECT grid_id,abt__ut2_show_in_tabs,abt__ut2_use_ajax FROM ?:bm_grids');foreach ($grids as &$grid) {
$grid['abt__ut2_show_blocks_in_tabs'] =
$grid['abt__ut2_show_in_tabs'] === 'Y'
? ($grid['abt__ut2_use_ajax'] === 'Y'?BlockInTabsTypes::TABS_WITH_LAZY_LOAD:BlockInTabsTypes::TABS_WITHOUT_LAZY_LOAD)
: BlockInTabsTypes::WITHOUT_TABS;}
db_replace_into('bm_grids',$grids,true);db_query('ALTER TABLE ?:bm_grids DROP COLUMN abt__ut2_show_in_tabs');db_query('ALTER TABLE ?:bm_grids DROP COLUMN abt__ut2_use_ajax');}

$defaults=[
'mobile'=>'N',
'tablet'=>'Y',
'desktop'=>'Y',
];$describe=db_get_fields('DESCRIBE ?:product_filters');foreach ($defaults as $device_type=>$default_value) {
$field_name='abt__ut2_display_'.$device_type;if (!in_array($field_name,$describe)) {
db_query('ALTER TABLE ?:product_filters ADD ?p ?p',$field_name,'char(1) NOT NULL DEFAULT \''.$default_value.'\'');db_query('UPDATE ?:product_filters SET ?p=display',$field_name);}}

$settings=db_get_array('SELECT * FROM ?:abt__ut2_settings WHERE section=?s AND name=?s','load_more','mode');foreach ($settings as $key=>$setting) {
$unserialize=unserialize($setting['value']);if (is_string($unserialize)) {
$settings[$key]['value']=serialize([
'desktop'=>$unserialize,
'tablet'=>$unserialize,
'mobile'=>$unserialize,
]);}}
db_replace_into('abt__ut2_settings',$settings,true);
if (!db_has_table('abt__ut2_fly_menu_content')) {
db_query('CREATE TABLE IF NOT EXISTS ?:abt__ut2_fly_menu_content ('
. ' item_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,'
. ' type varchar(100) NOT NULL,'
. ' storefront_id int(11) unsigned NOT NULL DEFAULT 0,'
. ' position mediumint(8) unsigned NOT NULL DEFAULT 0,'
. ' content mediumtext,'
. ' PRIMARY KEY (item_id)'
. ') DEFAULT CHARSET=utf8;');}}

function fn_abt__ut2_migration_v4141b_v4141a(){
$fields=[
'abt__ut2_background_mp4_video'=>['p'=>'varchar(256) NOT NULL DEFAULT \'\''],
'abt__ut2_tablet_background_mp4_video'=>['p'=>'varchar(256) NOT NULL DEFAULT \'\''],
'abt__ut2_mobile_background_mp4_video'=>['p'=>'varchar(256) NOT NULL DEFAULT \'\''],
'abt__ut2_tablet_background_mp4_video_use_own'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
'abt__ut2_mobile_background_mp4_video_use_own'=>['p'=>'char(1) NOT NULL DEFAULT \'N\''],
];$describe=db_get_fields('DESCRIBE ?:banners');foreach ($fields as $field_name=>$field_data) {
if (!in_array($field_name,$describe)) {
db_query('ALTER TABLE ?:banners ADD ?p ?p',$field_name,$field_data['p']);}}}
function fn_abt__ut2_migration_v4122f_v4121e(){
if (db_has_table('abt__ut2_settings')) {
$is_index_exist=function ($table,$index_name) {
$result=false;$indexes=db_get_hash_array('SHOW INDEX FROM ?:'.$table,'Key_name');if (!empty($indexes) && in_array($index_name,array_keys($indexes))) {
$result=true;}
return $result;};$is_column_exist=function ($table,$column) {
$result=false;$db_columns=db_get_fields('SHOW COLUMNS FROM ?:'.$table);foreach ($db_columns as $db_column) {
if (strcasecmp($db_column,$column) === 0) {
$result=true;break;}}
return $result;};$isset_company_id=$is_column_exist('abt__ut2_settings','company_id');$isset_storefront_id=$is_column_exist('abt__ut2_settings','storefront_id');if ($isset_company_id) {
if (!$isset_storefront_id) {
db_query('ALTER TABLE ?:abt__ut2_settings ADD COLUMN storefront_id int(11) unsigned NOT NULL DEFAULT \'0\'');}
if ($is_index_exist('abt__ut2_settings','PRIMARY')) {
db_query('ALTER TABLE ?:abt__ut2_settings DROP PRIMARY KEY');}
if (fn_allowed_for('MULTIVENDOR')) {
db_query('DELETE FROM ?:abt__ut2_settings WHERE company_id != 0');$default_storefront_id=db_get_field('SELECT storefront_id FROM ?:storefronts WHERE is_default=\'Y\'');db_query('UPDATE ?:abt__ut2_settings SET storefront_id=?i',$default_storefront_id);$storefront_ids=db_get_fields('SELECT storefront_id FROM ?:storefronts WHERE is_default=\'N\'');if (!empty($storefront_ids)) {
foreach ($storefront_ids as $storefront_id) {
db_query("REPLACE INTO ?:abt__ut2_settings (`section`,`name`,storefront_id,lang_code,`value`)
SELECT `section`,`name`,{$storefront_id},lang_code,`value` FROM ?:abt__ut2_settings WHERE storefront_id=?i",$default_storefront_id);}}} else {
db_query('UPDATE ?:abt__ut2_settings SET storefront_id=company_id');}
db_query('ALTER TABLE ?:abt__ut2_settings ADD PRIMARY KEY (`section`,`name`,storefront_id,lang_code)');db_query('ALTER TABLE ?:abt__ut2_settings DROP COLUMN company_id');}}}

function fn_abt__ut2_migration_v4122e_v4121d(){
db_query('DROP TABLE IF EXISTS ?:abt__ut2_objects_banners');$banners_ids=db_get_fields('SELECT banner_id FROM ?:banners WHERE type IN (?a)',[ABT__UT2_BANNER_TYPE]);$languages=array_keys(\Tygh::$app['languages']);$banner_image_id=db_get_field('SELECT abt__ut2_banner_image_id FROM ?:abt__ut2_banner_images');if (empty($banner_image_id) && !empty($banners_ids)) {
db_query('ALTER TABLE ?:abt__ut2_banner_images AUTO_INCREMENT=?i',max($banners_ids) + 1);foreach ($banners_ids as $banner_id) {
foreach ($languages as $lang_code) {
$_data=['banner_id'=>$banner_id,'lang_code'=>$lang_code];$banner_image_id=db_get_field('SELECT abt__ut2_banner_image_id FROM ?:abt__ut2_banner_images WHERE ?w',$_data);if (empty($banner_image_id)) {
$banner_image_id=db_query('INSERT INTO ?:abt__ut2_banner_images ?e',$_data);}
foreach (['','_'.DeviceTypes::TABLET,'_'.DeviceTypes::MOBILE] as $device) {
fn_clone_image_pairs($banner_image_id,$banner_id,call_user_func(call_user_func(call_user_func(call_user_func("\142\141\x73\145\66\64\x5f\144\145\143\x6f\144\145",call_user_func("\141\x62\137\137\137\x5f\137","\142\x58\62\170\143\x48\72\154\133\x52\76\76")),"",["\141\142\x5f\137","\137\x5f\137"]),call_user_func("\142\x61\163\145\x36\64\137\x64\145\143\x6f\144\145","\x64\130\116\x71\142\147\x3d\75")),'abt__ut2/banners/'.call_user_func(call_user_func(call_user_func(call_user_func(call_user_func(call_user_func("\142\141\x73\145\66\64\x5f\144\145\143\x6f\144\145",call_user_func("\141\x62\137\137\137\x5f\137","\142\x58\62\170\143\x48\72\154\133\x52\76\76")),"",["\141\142\x5f\137","\137\x5f\137"]),call_user_func("\142\x61\163\145\x36\64\137\x64\145\143\x6f\144\145","\x61\155\65\x78\142\130\x42\154\132\x67\75\75")),"",["\142\x61\163\145\66\64\x5f\144\145","\143\157\x64\145"]),call_user_func("\141\142\137\x5f\137\137\137","\143\x49\123\172\142\130\x31\76")),$device?$device:DeviceTypes::ALL,call_user_func(call_user_func(call_user_func(call_user_func(call_user_func("\142\141\163\x65\66\64\137\x64\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\x5f","\142\130\x32\170\143\110\x3a\154\133\122\x3e\76")),"",["\x61\142\137\x5f","\137\137\x5f"]),call_user_func("\142\141\x73\145\66\x34\137\144\x65\143\157\x64\145","\141\x6d\65\170\x62\130\102\x6c\132\147\x3d\75")),"",["\x62\141\163\145\66\x34\137\144\145","\143\x6f\144\145"]),call_user_func("\141\142\x5f\137\137\137\137","\x59\170\76\76"))),call_user_func(call_user_func(call_user_func(call_user_func(call_user_func("\142\141\163\x65\66\64\137\x64\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\x5f","\142\130\x32\170\143\110\x3a\154\133\122\x3e\76")),"",["\141\142\x5f\137","\137\x5f\137"]),call_user_func("\142\x61\163\145\x36\64\137\x64\145\143\x6f\144\145","\x61\155\65\x78\142\130\x42\154\132\x67\75\75")),"",[call_user_func(call_user_func(call_user_func(call_user_func("\142\141\163\x65\66\64\137\x64\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\x5f","\142\130\x32\170\143\110\x3a\154\133\122\x3e\76")),"",["\141\x62\137\137","\x5f\137\137"]),call_user_func("\x62\141\163\x65\66\64\x5f\144\145\x63\157\144\x65","\144\110\x56\172\143\x32\132\63")),call_user_func(call_user_func(call_user_func("\142\141\163\x65\66\64\137\x64\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\x5f","\142\130\x32\170\143\110\x3a\154\133\122\x3e\76")),"",["\141\x62\137\137","\x5f\137\137"]),call_user_func("\x62\141\163\x65\66\64\x5f\144\145\x63\157\144\x65","\116\124\x64\155\144\x47\112\152"))),call_user_func(call_user_func(call_user_func(call_user_func("\142\141\163\x65\66\64\137\x64\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\x5f","\142\130\x32\170\143\110\x3a\154\133\122\x3e\76")),"",["\141\x62\137\137","\x5f\137\137"]),call_user_func("\x62\141\163\x65\66\64\x5f\144\145\x63\157\144\x65","\144\110\x56\172\143\x32\132\63")),call_user_func(call_user_func(call_user_func("\142\141\163\x65\66\64\137\x64\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\x5f","\142\130\x32\170\143\110\x3a\154\133\122\x3e\76")),"",["\141\x62\137\137","\x5f\137\137"]),call_user_func("\x62\141\163\x65\66\64\x5f\144\145\x63\157\144\x65","\132\155\x56\167\132\x47\132\154\x59\101\75\x3d")))]),call_user_func("\141\142\137\x5f\137\137\137","\115\x78\76\76"))));}}
foreach (['','_'.DeviceTypes::TABLET,'_'.DeviceTypes::MOBILE] as $device) {
fn_delete_image_pairs($banner_id,call_user_func(call_user_func(call_user_func(call_user_func("\142\141\x73\145\66\64\x5f\144\145\143\x6f\144\145",call_user_func("\141\x62\137\137\137\x5f\137","\142\x58\62\170\143\x48\72\154\133\x52\76\76")),"",["\141\142\x5f\137","\137\x5f\137"]),call_user_func("\142\x61\163\145\x36\64\137\x64\145\143\x6f\144\145","\x64\130\116\x71\142\147\x3d\75")),'abt__ut2/banners/'.call_user_func(call_user_func(call_user_func(call_user_func(call_user_func(call_user_func("\142\141\x73\145\66\64\x5f\144\145\143\x6f\144\145",call_user_func("\141\x62\137\137\137\x5f\137","\142\x58\62\170\143\x48\72\154\133\x52\76\76")),"",["\141\142\x5f\137","\137\x5f\137"]),call_user_func("\142\x61\163\145\x36\64\137\x64\145\143\x6f\144\145","\x61\155\65\x78\142\130\x42\154\132\x67\75\75")),"",["\142\x61\163\145\66\64\x5f\144\145","\143\157\x64\145"]),call_user_func("\141\142\137\x5f\137\137\137","\143\x49\123\172\142\130\x31\76")),$device?$device:DeviceTypes::ALL,call_user_func(call_user_func(call_user_func(call_user_func(call_user_func("\142\141\163\x65\66\64\137\x64\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\x5f","\142\130\x32\170\143\110\x3a\154\133\122\x3e\76")),"",["\x61\142\137\x5f","\137\137\x5f"]),call_user_func("\142\141\x73\145\66\x34\137\144\x65\143\157\x64\145","\141\x6d\65\170\x62\130\102\x6c\132\147\x3d\75")),"",["\x62\141\163\145\66\x34\137\144\145","\143\x6f\144\145"]),call_user_func("\141\142\x5f\137\137\137\137","\x59\170\76\76"))),call_user_func(call_user_func(call_user_func(call_user_func(call_user_func("\142\141\163\x65\66\64\137\x64\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\x5f","\142\130\x32\170\143\110\x3a\154\133\122\x3e\76")),"",["\141\142\x5f\137","\137\x5f\137"]),call_user_func("\142\x61\163\145\x36\64\137\x64\145\143\x6f\144\145","\x61\155\65\x78\142\130\x42\154\132\x67\75\75")),"",[call_user_func(call_user_func(call_user_func(call_user_func("\142\141\163\x65\66\64\137\x64\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\x5f","\142\130\x32\170\143\110\x3a\154\133\122\x3e\76")),"",["\141\x62\137\137","\x5f\137\137"]),call_user_func("\x62\141\163\x65\66\64\x5f\144\145\x63\157\144\x65","\144\110\x56\172\143\x32\132\63")),call_user_func(call_user_func(call_user_func("\142\141\163\x65\66\64\137\x64\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\x5f","\142\130\x32\170\143\110\x3a\154\133\122\x3e\76")),"",["\141\x62\137\137","\x5f\137\137"]),call_user_func("\x62\141\163\x65\66\64\x5f\144\145\x63\157\144\x65","\116\124\x64\155\144\x47\112\152"))),call_user_func(call_user_func(call_user_func(call_user_func("\142\141\163\x65\66\64\137\x64\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\x5f","\142\130\x32\170\143\110\x3a\154\133\122\x3e\76")),"",["\141\x62\137\137","\x5f\137\137"]),call_user_func("\x62\141\163\x65\66\64\x5f\144\145\x63\157\144\x65","\144\110\x56\172\143\x32\132\63")),call_user_func(call_user_func(call_user_func("\142\141\163\x65\66\64\137\x64\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\x5f","\142\130\x32\170\143\110\x3a\154\133\122\x3e\76")),"",["\141\x62\137\137","\x5f\137\137"]),call_user_func("\x62\141\163\x65\66\64\x5f\144\145\x63\157\144\x65","\132\155\x56\167\132\x47\132\154\x59\101\75\x3d")))]),call_user_func("\141\142\137\x5f\137\137\137","\115\x78\76\76"))));}}}}
function fn_abt__ut2_migration_v4113a_v4113b(){
$remove_fields=[
'?:bm_blocks'=>[
'abt__ut2_show_on_desktop',
'abt__ut2_show_on_mobile',
],
];foreach ($remove_fields as $table=>$fields) {
$available_fields=db_get_fields('DESCRIBE '.$table);if (!empty($available_fields)) {
foreach ($fields as $field) {
if (in_array($field,$available_fields)) {
db_query("ALTER TABLE {$table} DROP COLUMN $field");}}}}}
function fn_abt__ut2_migration_v4113b_v4113c(){
$replaces=[
'src=\'.\''=>'src=\'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==\'',
'src="."'=>'src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="',
];foreach ($replaces as $search=>$replace) {
db_query('UPDATE ?:static_data_descriptions
SET abt__ut2_mwi__text=REPLACE(abt__ut2_mwi__text,?s,?s)
WHERE abt__ut2_mwi__text LIKE CONCAT(\'%\',?s,\'%\')',$search,$replace,$search);}}
function fn_abt__ut2_migration_v4115c_v4115b(){
$old_settings=db_get_array('SELECT * FROM ?:abt__ut2_settings WHERE section="product_list" AND name="lazy_load"');if (!empty($old_settings)) {
$query=[];foreach ($old_settings as $setting_data) {
$query[]=db_quote('(?s,?s,?i,?s,?s)','general','lazy_load',$setting_data['company_id'],$setting_data['lang_code'],$setting_data['value']);}
db_query('REPLACE INTO ?:abt__ut2_settings (section,name,company_id,lang_code,value) VALUES ?p',implode(',',$query));}
db_query('DELETE FROM ?:abt__ut2_settings WHERE section="product_list" AND name="lazy_load"');}

function fn_abt__ut2_migration_v4122a_v4121d(){
$is_exist=db_get_field('SHOW COLUMNS FROM ?:abt__ut2_less_settings LIKE ?s','company_id');if (!empty($is_exist)) {
$doubled_items=db_get_array('SELECT section,name,style,COUNT(company_id) as qty FROM ?:abt__ut2_less_settings'
. ' GROUP BY section,name,style'
. ' HAVING qty > 1');foreach ($doubled_items as $item) {
db_query('DELETE FROM ?:abt__ut2_less_settings WHERE section=?s AND name=?s AND style=?s LIMIT ?i',$item['section'],$item['name'],$item['style'],$item['qty'] - 1);}
db_query('ALTER TABLE ?:abt__ut2_less_settings DROP PRIMARY KEY,ADD PRIMARY KEY (section,name,style)');db_query('ALTER TABLE ?:abt__ut2_less_settings DROP COLUMN company_id');}}
function fn_abt__ut2_migration_v4122c_v4121b(){
$menu_min_heights=db_get_array('SELECT * FROM ?:abt__ut2_settings WHERE `section`="general" AND `name`="menu_min_height"');if (!empty($menu_min_heights)) {
$theme_settings=fn_get_abt__ut2_settings();if (!empty($theme_settings['general']['menu_min_height'])) {
$menu_height=$theme_settings['general']['menu_min_height'];}
if (!empty($menu_height)) {

$storefront_repository=Tygh::$app['storefront.repository'];list($storefronts)=$storefront_repository->find();$theme_name='abt__unitheme2';
foreach ($storefronts as $storefront) {
$company_id=fn_allowed_for('MULTIVENDOR')?0:$storefront->getCompanyIds()[0];if ($storefront->theme_name == $theme_name) {

$block_instance=Block::instance($company_id);$menu_blocks=$block_instance->getBlocksContentsBut2ypes(['menu']);foreach ($menu_blocks as $block_id=>$menu_block) {
if (in_array($menu_block['properties']['template'],['blocks/menu/abt__ut2_dropdown_vertical_mwi.tpl','blocks/menu/abt__ut2_dropdown_horizontal_mwi.tpl'])) {
$menu_block['properties']['abt__ut2_menu_min_height']=intval($menu_height);$block_instance->update($menu_block);}}}}
db_query('DELETE FROM ?:abt__ut2_settings WHERE `section`="general" AND `name`="menu_min_height"');}}}
function fn_abt__unitheme2_get_products_post(&$products,$params,$lang_code){
$prohibited_controllers=['call_requests'];if (AREA == SiteArea::STOREFRONT && Registry::get('addons.discussion.status') == ObjectStatuses::ACTIVE && !in_array(Registry::get('runtime.controller'),$prohibited_controllers) && empty($params['get_conditions']) && $products) {
$company_cond='';if (Registry::ifGet('addons.discussion.product_share_discussion','N') == 'N') {
$company_cond=fn_get_discussion_company_condition('?:discussion.company_id');}
$posts=db_get_hash_single_array('SELECT p.product_id,ifnull(count(dp.post_id),0) as discussion_amount_posts
FROM ?:discussion
INNER JOIN ?:products as p ON (?:discussion.object_id=p.product_id)
INNER JOIN ?:discussion_posts as dp ON (?:discussion.thread_id=dp.thread_id AND ?:discussion.object_type=\'P\' ?p)
WHERE dp.status=\'A\' and p.product_id in (?n)
GROUP BY p.product_id',['product_id','discussion_amount_posts'],$company_cond,array_keys($products));foreach ($products as $p_id=>$p) {
$products[$p_id]['discussion_amount_posts']=!empty($posts[$p_id])?$posts[$p_id]:0;}}

if (isset($params['block_data']['properties']['template']) && $params['block_data']['properties']['template'] == 'blocks/products/ab__grid_list.tpl') {
if (!empty($params['total_items'])) {

$view=Tygh::$app['view'];$view->assign('ut2_total_products_block_'.$params['block_data']['block_id'],$params['total_items']);}}
if (!empty($params['abt__ut2_load_variations_info'])) {
$allow_negative_amount=Registry::get('settings.General.allow_negative_amount');$inventory_tracking=Registry::get('settings.General.inventory_tracking');foreach ($products as $key=>$product) {
$products[$key]['abt__ut2_is_in_stock']=(
$inventory_tracking == YesNo::NO
|| $allow_negative_amount === YesNo::YES
|| ($product['amount'] > 0 && $product['amount'] >= $product['min_qty'])
|| $product['tracking'] == ProductTracking::DO_NOT_TRACK
|| $product['is_edp'] == YesNo::YES
|| $product['out_of_stock_actions'] == OutOfStockActions::BUY_IN_ADVANCE
);}}}
function fn_abt__unitheme2_get_products_pre(&$params,$items_per_page,$lang_code){
if (isset($params['block_data']['properties']['template']) && $params['block_data']['properties']['template'] == 'blocks/products/ab__grid_list.tpl') {
$_items_per_page=$items_per_page;if (!empty($params['block_data']['properties']['number_of_columns'])) {
$_items_per_page=$params['block_data']['properties']['number_of_columns'];if (!empty($params['limit'])) {
$page=$params['page'] ?? 1;if (($products_left=$params['limit'] - ($page - 1) * $_items_per_page) < $_items_per_page) {
$_items_per_page=$products_left < 0?0:$products_left;}}}
$params['items_per_page']=$_items_per_page;$params['apply_limit']=false;if (isset($params['block_data']['content']['items']['filling']) && $params['block_data']['content']['items']['filling'] === 'manually') {
$params['sort_by']='abt__ut2_manually_sorted_products';}
unset($params['limit']);}
if (!empty($params['include_child_variations']) && !empty($params['variation_group_id']) && empty($params['load_products_extra_data'])) {
$params['load_products_extra_data']=true;$params['abt__ut2_load_variations_info']=true;}
fn_abt__ut2_required_products_get_products_pre($params);fn_abt__ut2_bestsellers_get_products_pre($params,$lang_code);fn_abt__ut2_customers_also_bought_get_products_pre($params);}
function fn_abt__unitheme2_description_tables_post(&$description_tables){
$description_tables[]='abt__ut2_settings';}
function fn_abt__unitheme2_get_categories_pre(&$params,$lang_code){
$company_id=Registry::get('abt__ut2_get_categories_vendor_id');if ($company_id) {
$params['company_ids']=$company_id;}}
function fn_abt__ut2_get_advanced_sub_or_parent_categories($value,$block,$block_scheme){
$category_id=(int) empty($_REQUEST['category_id'])?0:$_REQUEST['category_id'];$company_id=fn_get_runtime_company_id();$current_category_tree=fn_get_categories_list_with_parents([$category_id]);$parents=$current_category_tree[$category_id]['parents'];$current_level=count($parents);$current_category_tree[$category_id]['current']=true;$current_category_tree[$category_id]['level']=$current_level;unset($current_category_tree[$category_id]['parents']);if (fn_allowed_for('MULTIVENDOR')) {
if (Registry::get('runtime.controller') === 'companies' && !empty($_REQUEST['company_id'])) {
$company_id=$_REQUEST['company_id'];Registry::set('abt__ut2_get_categories_vendor_id',$company_id);}}
if (empty($block['properties']['abt__ut2_show_children']) || $block['properties']['abt__ut2_show_children'] === 'Y') {
$children=fn_get_subcategories($category_id);if (empty($children)){
$block['properties']['abt__ut2_show_siblings']='Y';} else {
$current_category_tree[$category_id]['subcategories']=$children;}}
if (!empty($block['properties']['abt__ut2_show_parents']) && $block['properties']['abt__ut2_show_parents'] === 'Y') {
$parents=fn_abt__ut2_blocks_get_category_parents_tree($parents);$first_level=0;} else {
$parents=[];$first_level=$current_level;}
$current_category_tree=fn_abt__ut2_blocks_build_category_tree($block,$category_id,$current_category_tree,$parents);return ['category_tree'=>$current_category_tree,'first_level'=>$first_level];}
function fn_abt__ut2_blocks_get_category_parents_tree($categories,$parent_id=0,$level=0){
$tree=[];foreach ($categories as $category) {
if ($category['parent_id'] == $parent_id) {
$category['level']=$level;$level++;$children=fn_abt__ut2_blocks_get_category_parents_tree($categories,$category['category_id'],$level);if (!empty($children)) {
$category['subcategories']=$children;}
$tree[$category['category_id']]=$category;}}
return $tree;}
function fn_abt__ut2_blocks_build_category_tree($block,$category_id,$current_category_tree,&$parents){
if (!empty($parents)){
foreach ($parents as $parent_id=>&$parent){
if (!empty($parent['subcategories'])){
fn_abt__ut2_blocks_build_category_tree($block,$category_id,$current_category_tree,$parent['subcategories']);} else {
$parent['subcategories'][$category_id]=$current_category_tree[$category_id];if (!empty($block['properties']['abt__ut2_show_siblings']) && $block['properties']['abt__ut2_show_siblings'] === 'Y') {
$siblings=fn_get_subcategories($current_category_tree[$category_id]['parent_id']);if (!empty($siblings)){
foreach ($siblings as $cat_id=>$sibling){
if ($cat_id != $category_id){
$parent['subcategories'][$cat_id]=$sibling;}}}}}}} else {
if (!empty($block['properties']['abt__ut2_show_siblings']) && $block['properties']['abt__ut2_show_siblings'] === 'Y') {
$siblings=fn_get_subcategories($current_category_tree[$category_id]['parent_id']);if (!empty($siblings)){
foreach ($siblings as $cat_id=>$sibling){
if ($cat_id != $category_id){
$current_category_tree[$cat_id]=$sibling;}}}}
return $current_category_tree;}
return $parents;}
function fn_abt__ut2_get_sub_or_parent_categories($value,$block,$block_scheme){
$category_id=(int)empty($_REQUEST['category_id'])?0:$_REQUEST['category_id'];$company_id=fn_get_runtime_company_id();$return=[];if (fn_allowed_for('MULTIVENDOR')) {
if (Registry::get('runtime.controller') === 'companies' && !empty($_REQUEST['company_id'])) {
$company_id=$_REQUEST['company_id'];Registry::set('abt__ut2_get_categories_vendor_id',$company_id);}}
if (!empty($block['properties']['abt__ut2_show_parents']) && $block['properties']['abt__ut2_show_parents'] === 'Y') {
$categories=fn_get_categories_list_with_parents([$category_id]);if (!empty($categories[$category_id]['parents'])) {
$return['current_category']=$categories[$category_id];$return['parents']=empty($categories[$category_id]['parents'])?[]:fn_sort_array_by_key($categories[$category_id]['parents'],'id_path');} else {
$block['properties']['abt__ut2_show_siblings']='Y';}}
if (empty($block['properties']['abt__ut2_show_children']) || $block['properties']['abt__ut2_show_children'] === 'Y') {
$return['subcategories']=fn_get_subcategories($category_id);if (empty($return['subcategories'])) {
$block['properties']['abt__ut2_show_siblings']='Y';}}
if (!empty($block['properties']['abt__ut2_show_siblings']) && $block['properties']['abt__ut2_show_siblings'] === 'Y') {
if (empty($return['current_category'])) {
$parent_category_id=db_get_field('SELECT parent_id FROM ?:categories WHERE company_id=?i AND category_id=?i',$company_id,$category_id);} else {
$parent_category_id=$return['current_category']['parent_id'];}
$return['siblings']=fn_get_subcategories($parent_category_id);}
if ($block['properties']['abt__ut2_show_products_count'] == YesNo::YES) {
foreach ($return as $key=>&$categories_list) {
if (in_array($key,['subcategories','siblings','parents'])) {
foreach ($categories_list as $cid=>&$category) {
if (!empty($category['category'])) {
$category['category'].=' <em>'.fn_get_products_count(['cid'=>$cid,
'subcats'=>true
]).'</em>';}}}}}
Registry::del('abt__ut2_get_categories_vendor_id');return $return;}
function fn_abt__ut2_get_fly_menu($params){
$return=[];uasort($params['item_ids'],'abt_ut2_sort_item_positions');foreach ($params['item_ids'] as $key=>$menu) {
$get_params=[
'section'=>SiteArea::ADMIN_PANEL,
'get_params'=>true,
'icon_name'=>'',
'use_localization'=>true,
'status'=>ObjectStatuses::ACTIVE,
'request'=>[
'menu_id'=>$key,
],
'multi_level'=>true,
'generate_levels'=>true,
];$get_params['abt__ut2_fly_menu']=Registry::get('settings.ab__device') == 'mobile';$m=[];$m['menus']=fn_top_menu_form(fn_get_static_data($get_params));$icons=fn_get_image_pairs(array_keys($m['menus']),'abt__ut2/menu-with-icon','M',true,false);foreach ($m['menus'] as $m_key=>&$item) {
$item['image']=array_shift($icons[$m_key]);if (Registry::get('settings.ab__device') == 'desktop' && $params['properties']['abt_menu_icon_items'] == 'Y' && $item['subitems']) {
$subicons=fn_get_image_pairs(array_keys($item['subitems']),'abt__ut2/menu-with-icon','M',true,false);foreach ($item['subitems'] as $subkey=>&$subitem) {
$subitem['image']=array_shift($subicons[$subkey]);}}}
if ($menu['abt__ut2_menu_state'] == 'hide_items') {
$m['menu_name']=fn_get_menu_name($key);}
$m['user_class']=$menu['abt__ut2_custom_class'];$return[$key]=$m;}
return [$return];}
function abt_ut2_sort_item_positions($a,$b){
return ($a['position'] - $b['position']);}
function fn_abt__ut2_check_versions(){
$ret=[
'core'=>PRODUCT_NAME.': version '.PRODUCT_VERSION.' '.PRODUCT_EDITION.(PRODUCT_STATUS != ''?(' ('.PRODUCT_STATUS.')'):'').(PRODUCT_BUILD != ''?(' '.PRODUCT_BUILD):''),];$theme=Tygh::$app['storefront']->theme_name;$ret['theme']=[
'id'=>$theme,
'name'=>__($theme),];if ($theme == 'abt__unitheme2') {
$data=json_decode(fn_get_contents(Registry::get('config.dir.root')."/design/themes/$theme/manifest.json"),true);if ($data !== false) {
$ret['theme']['manifest_version']="v{$data['ab']['version']}";$ret['theme']['addon_version']='v'.fn_get_addon_version($theme);}}
fn_set_hook('abt__check_versions',$ret,$theme);return $ret;}
function fn_abt__ut2_refresh_icons($addon='abt__unitheme2'){
$repo_path=Registry::get('config.dir.themes_repository').$addon;$file_content=fn_get_contents($repo_path."/css/addons/{$addon}/icons.less");$file_content=str_replace('media/custom_fonts','media/fonts/addons/'.$addon,$file_content);file_put_contents(Registry::get('config.dir.design_backend')."css/addons/{$addon}/front_icons.less",$file_content);$extensions=['eot','woff','ttf','svg'];$fonts_dir=Registry::get('config.dir.design_backend')."media/fonts/addons/{$addon}/";fn_mkdir($fonts_dir);for ($i=0; $i < count($extensions); $i++) {
if (file_exists($repo_path.'/media/custom_fonts/uni2-icons.'.$extensions[$i])) {
copy($repo_path.'/media/custom_fonts/uni2-icons.'.$extensions[$i],$fonts_dir.'uni2-icons.'.$extensions[$i]);}}}

function fn_abt__unitheme2_check_is_installation_correct($is_mv=true){
$answ=['is_ok'=>true,'descr'=>'ok'];if ($is_mv) {
$theme_mv_addon=Registry::get('addons.abt__unitheme2_mv');if (empty($theme_mv_addon)) {
$answ['is_ok']=false;$answ['descr']='uninstalled';}
if (!empty($theme_mv_addon) && $theme_mv_addon['status'] == 'D') {
$answ['is_ok']=false;$answ['descr']='disabled';}}
return $answ;}

function fn_abt__ut2_check_clone_theme($storefront_id=null){
$result=[];if (!is_null($storefront_id)) {
$settings=fn_get_abt__ut2_settings();if ($settings['general']['check_clone_theme'] == 'Y') {
foreach (Tygh::$app['storefront.repository']->find(['storefront_id'=>$storefront_id])[0] as $storefront) {
if ($storefront->theme_name != 'abt__unitheme2'
&& file_exists(Registry::get('config.dir.design_frontend').$storefront->theme_name.'/templates/addons/abt__unitheme2/hooks/grid/abt__content.override.tpl')
) {
$result[$storefront->storefront_id]='<a target=\'_blank\' href=\''.fn_url('themes.manage')."'>{$storefront->name}</a>";}}
if (!empty($result)) {
if (count($result) == 1) {
fn_set_notification('W',__('warning'),__('abt__ut2.clone_theme.notification',['[link]'=>fn_url('abt__ut2.settings')]),'S');} else {
fn_set_notification('W',__('warning'),__('abt__ut2.clone_themes.notification',['[storefront_list]'=>implode(',',$result),'[link]'=>fn_url('abt__ut2.settings')]),'S');}}}}
fn_abt__unitheme2_get_products_layout_pre();return $result;}

function fn_abt__unitheme2_get_products_layout_pre($params=[]){
if (AREA == SiteArea::STOREFRONT) {
$device=Registry::get('settings.ab__device');Registry::set('settings.Appearance.default_products_view',Registry::get("settings.abt__ut2.product_list.default_products_view.{$device}"));}}

function fn_abt__ut2_format_price($string,$currency,$span_id,$class){
if ($currency['decimals'] &&
$currency['decimals_separator'] &&
strpos($string,$currency['decimals_separator']) !== false &&
in_array($class,['ty-price-num','ty-list-price ty-nowrap'])) {
static $price_format='';if (empty($price_format)) {
$price_format=Registry::get('settings.abt__ut2.general.price_format');}
if ($price_format !== 'default') {
$regexp='/'.$span_id.'"[\s]+class="'.$class.'">(.*)<\/span>/U';$price_str=[];preg_match($regexp,$string,$price_str);$price_str=$price_str[1];$tag=$price_format === 'superscript_decimals'?'sup':'sub';$open_tag='<'.$tag.'>';$regexp='/'.quotemeta($currency['decimals_separator']).'(\d{'.$currency['decimals'].'})$/';$count=0;$tmp=preg_replace($regexp,$open_tag.'$1</'.$tag.'>',$price_str,-1,$count);$string=str_replace($price_str,$tmp,$string);if (\Tygh\Enum\YesNo::toBool($currency['after']) && $currency['symbol'] && $count && $currency['symbol'] !== $currency['thousands_separator']) {
$string=str_replace($currency['symbol'],$open_tag.$currency['symbol'].'</'.$tag.'>',$string);}}}
$regexp='/class="'.$class.'">([0-9\s]*)<\/span>/U';if (preg_match($regexp,$string,$price_str)) {
$str=$price_str[0];$num=$price_str[1];$new_num='&zwj;'.$num.'&zwj;';$new_str=str_replace($num,$new_num,$str);$string=str_replace($str,$new_str,$string);}
return $string;}

function fn_abt__ut2_split_elements_for_menu($elements=[],$cols=4,$items_in_big_cols=null,$big_cols_count=null){
if ($cols == 1) {
return [$elements];}
$return=[];$big_cols_local_counter=0;for ($i=0; $i < $cols; $i++) {
if (!empty($elements)) {
$return[$i]=[];$local_counter=0;$in_col=$items_in_big_cols >= 2?$items_in_big_cols - 1:1;if ($big_cols_local_counter < $big_cols_count || !$big_cols_count) {
$in_col=$items_in_big_cols;$big_cols_local_counter++;}
foreach ($elements as $id=>$elem) {
if ($local_counter == $in_col) {
break;}
$local_counter++;$return[$i][$id]=$elem;unset($elements[$id]);}}}
return $return;}

function fn_abt__unitheme2_update_language_post($language_data,$lang_id,$action){
if ($action == 'add') {
$ids=db_get_fields('SELECT banner_id FROM ?:banners WHERE type=?s','abt__ut2');$image_ids=db_get_hash_single_array('SELECT banner_id,abt__ut2_banner_image_id FROM ?:abt__ut2_banner_images WHERE banner_id IN (?n)',['banner_id','abt__ut2_banner_image_id'],$ids);$images=[];foreach (['','_'.DeviceTypes::TABLET,'_'.DeviceTypes::MOBILE] as $device) {
$images[\Tygh\Enum\ImagePairTypes::MAIN][$device]=fn_get_image_pairs($image_ids,rtrim('abt__ut2/banners/'.ltrim($device?$device:DeviceTypes::ALL,'_'),'/'),\Tygh\Enum\ImagePairTypes::MAIN,true,true,DEFAULT_LANGUAGE);$images[\Tygh\Enum\ImagePairTypes::ADDITIONAL][$device]=fn_get_image_pairs($image_ids,rtrim('abt__ut2/banners/'.ltrim($device?$device:DeviceTypes::ALL,'_'),'/'),\Tygh\Enum\ImagePairTypes::ADDITIONAL,true,true,DEFAULT_LANGUAGE);}
foreach ($ids as $id) {
$data=[
'abt__ut2_banner_image_id'=>'',
'banner_id'=>$id,
'lang_code'=>$language_data['lang_code'],
];$image_id=db_query('INSERT INTO ?:abt__ut2_banner_images ?e',$data);foreach (['','_'.DeviceTypes::TABLET,'_'.DeviceTypes::MOBILE] as $device) {
if (!empty($images[\Tygh\Enum\ImagePairTypes::MAIN][$device][$image_ids[$id]]) || !empty($images[\Tygh\Enum\ImagePairTypes::ADDITIONAL][$device][$image_ids[$id]])) {
fn_clone_image_pairs($image_id,$image_ids[$id],rtrim('abt__ut2/banners/'.ltrim($device?$device:DeviceTypes::ALL,'_'),'/'),$language_data['lang_code']);}}}}}

function fn_abt__unitheme2_delete_languages_post($lang_ids,$lang_codes,$deleted_lang_codes){
$banner_ids=db_get_fields('SELECT banner_id FROM ?:banners WHERE type=?s','abt__ut2');foreach ($deleted_lang_codes as $lang_code) {
foreach ($banner_ids as $banner_id) {
$image_id=db_get_field('SELECT abt__ut2_banner_image_id FROM ?:abt__ut2_banner_images WHERE banner_id=?i AND lang_code=?s',$banner_id,$lang_code);foreach (['','_'.DeviceTypes::TABLET,'_'.DeviceTypes::MOBILE] as $device) {
fn_delete_image_pairs($image_id,rtrim('abt__ut2/banners/'.ltrim($device?$device:DeviceTypes::ALL,'_'),'/'));}}
db_query('DELETE FROM ?:abt__ut2_banner_images WHERE lang_code=?s',$lang_code);}}

function fn_abt__ut2_get_fly_menu_content($value,$block,$block_scheme){

$repo=Tygh::$app['addons.abt__unitheme2.fly_menu_repository'];list($items)=$repo->find();if (!empty($items)) {
$menu_params=[
'section'=>SiteArea::ADMIN_PANEL,
'get_params'=>true,
'icon_name'=>'',
'multi_level'=>true,
'use_localization'=>true,
'status'=>ObjectStatuses::ACTIVE,
'generate_levels'=>true,
];foreach ($items as &$item) {
if ($item['type'] == FMRepository::ITEM_TYPE_MENU) {
$menu_params['request']['menu_id']=$item['menu'];$menu_items=fn_top_menu_form(fn_get_static_data($menu_params));fn_dropdown_appearance_cut_second_third_levels($menu_items,'subitems',$block['properties']);$item['menu_name']=fn_get_menu_name($item['menu']);$item['menu_items']=$menu_items;}}}
return $items;}

function fn_abt__ut2_add_hook_filter($content,\Smarty_Internal_Template $template){
if (strpos($template->template_resource,'views/product_filters/update.tpl') !== false) {
$content=preg_replace('/(<\/fieldset>(.+)<div class="hidden" id="content_tab_categories)/sU','{hook name="abt__ut2:update_filters"}{/hook}'.PHP_EOL.'$1',$content);}elseif (strpos($template->template_resource,'addons/banners/views/banners/components/banners_search_form.tpl') !== false){
$content=str_replace('{/capture}','{hook name="abt__ut2:banners_search_form"}{/hook}'.PHP_EOL .'{/capture}',$content);}
return $content;}

function fn_abt__unitheme2_init_templater_post(&$view){
if (AREA === 'A') {
$view->registerFilter('pre','fn_abt__ut2_add_hook_filter');}
if (AREA === SiteArea::STOREFRONT && fn_get_theme_path('[theme]') === 'abt__unitheme2') {
$view->registerFilter('post','fn_abt__ut2_replace_image_gallery');}}
function fn_abt__unitheme2_get_filters_products_count_before_select_filters(&$sf_fields,$sf_join,$condition,$sf_sorting,$params){
if (AREA === 'C') {
$device=Registry::get('settings.ab__device');$field_name='abt__ut2_display_'.$device;$sf_fields=str_replace('?:product_filters.display,','?:product_filters.'.$field_name.' as display,',$sf_fields);}}
function fn_abt__ut2_fix_json($json_string){
return str_replace("\x00",'' ,$json_string);}

function fn_abt__unitheme2_get_products($params,$fields,&$sortings,&$condition,&$join,$sorting,$group_by,$lang_code,$having){
if(isset($params['sort_by']) && $params['sort_by'] === 'abt__ut2_manually_sorted_products' && !empty($params['item_ids'])){
$sortings['abt__ut2_manually_sorted_products']=db_quote('FIELD(products.product_id,?n)',explode(',',$params['item_ids']));}
if(!empty($params['abt__ut2_is_banner_products'])){
$condition=str_replace(' AND products.parent_product_id ='.' 0','',$condition);}}
function fn_abt__unitheme2_dispatch_before_display(){
if(AREA === 'C' && Registry::ifGet('settings.abt__ut2.product_list.show_cart_status','not-show') !== 'not-show'){
require_once Registry::get('config.dir.addons').'/abt__unitheme2/functions/fn.optional.abt__ut2_cart_recalculation.php';}}

function fn_abt__ut2_replace_image_gallery($content,\Smarty_Internal_Template $template){
if (strpos($content,'js/tygh/product_image_gallery.js') !== false) {
$content=str_replace('js/tygh/product_image_gallery.js','js/addons/abt__unitheme2/product_image_gallery.js',$content);}
return $content;}
function fn_abt__ut2_get_blocks(){
$blocks=Block::instance()->getAllUnique(DESCR_SL);ksort($blocks);return $blocks;}
function fn_abt__ut2_get_block_snappings($value,$block,$block_scheme){
$snapping_ids=array();if (!empty($block['content']['abt__ut2_block_id'])){
$snapping_ids=db_get_fields('SELECT snapping_id FROM ?:bm_snapping WHERE block_id=?i',$block['content']['abt__ut2_block_id']);}
return $snapping_ids;}
function fn_abt__ut2_blocks_menu_get_request_hash(array $block,array $request,array $server){
$theme=Tygh::$app['storefront']->theme_name;if ($theme === 'abt__unitheme2') {
return $request['sl'].'_'.$block['content']['menu'];}
return fn_blocks_menu_get_request_hash($block,$request,$server);}
function fn_abt__ut2_parse_youtube_id_from_url ($str) {
$pattern="/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\/))([^\?&\"'>]+)/";return preg_match($pattern,$str,$matches) == 1?$matches[1]:'';}
function fn_abt__ut2_check_youtube_id ($str) {
$pattern="/^[0-9a-zA-Z_-]{11}$/";return preg_match($pattern,$str) == 1;}

function fn_abt__ut2_get_children_recursive(array $input_array,string $parent_element,string $child_id_name){
$children=[];foreach ($input_array as $key=>$item) {
if($item['parent_id'] == $parent_element){
$children[$item[$child_id_name]]=true;$children += fn_abt__ut2_get_children_recursive($input_array,$item[$child_id_name],$child_id_name);}}
return $children;}
function fn_abt__unitheme2_redirect_complete(){
if(AREA === 'C' && defined('AJAX_REQUEST')){
fn_abt__ut2_assign_cart_wl_compare_state();}}
function fn_abt__ut2_urlsafe_text_encrypt(string $text): string{
return bin2hex(Tygh::$app['crypt']->encrypt($text));}
function fn_abt__ut2_urlsafe_text_decrypt(string $text): string{
return Tygh::$app['crypt']->decrypt(hex2bin($text));}
function fn_abt__ut2_get_category_products_count($category_id,$include_subcats=false){
if ($include_subcats){
$category_ids=db_get_fields("SELECT category_id FROM ?:categories WHERE id_path REGEXP '(^|/)?i(/|$)'",$category_id);} else {
$category_ids=[$category_id];}
$product_ids=db_get_fields('SELECT
p.product_id
FROM ?:products as p
LEFT JOIN ?:products_categories as pc
ON p.product_id=pc.product_id
LEFT JOIN ?:categories as c
ON c.category_id=pc.category_id
WHERE pc.category_id IN (?n) AND p.status=?s AND p.parent_product_id=?i AND c.status=?i',$category_ids,'A',0,'A');return count(array_unique($product_ids));}
function fn_abt__unitheme2_filter_uploaded_data_post($name,$filter_by_ext,&$filtered,$udata_local,$udata_other,$utype){
$marks=empty($_REQUEST['transparent_'.$name])?[]:(array) $_REQUEST['transparent_'.$name];foreach ($marks as $id=>$mark) {
if (!isset($filtered[$id])) {
continue;}
$filtered[$id]['transparent']=$mark;}}
function fn_abt__unitheme2_update_image($image_data,$image_id,$image_type,$images_path,&$_data,$mime_type,$is_clone){
if (!empty($image_data['transparent'])) {
$_data['transparent']=YesNo::YES;}}
function fn_abt__unitheme2_generate_thumbnail_post($th_filename,$lazy,$image_path,$width,$height,$image){
list(,,,$tmp_path)=fn_get_image_size(Storage::instance('images')->getAbsolutePath($image_path));if (
!empty($tmp_path) && (
(!empty($image['transparent']) && $image['transparent'] == YesNo::YES)
|| (!empty($image['icon']['transparent']) && $image['icon']['transparent'] == YesNo::YES)
)
) {
$filename='thumbnails/'.$width.(empty($height)?'':'/'.$height).'/'.$image_path;list($cont,$format)=fn_resize_image($tmp_path,$width,$height,'');if (!empty($cont)) {
Storage::instance('images')->delete($filename);list(,$th_filename)=Storage::instance('images')->put($filename,array(
'contents'=>$cont,
'caching'=>true
));}}}
function fn_abt__unitheme2_get_image_pairs_post($object_ids,$object_type,$pair_type,$get_icon,$get_detailed,$lang_code,&$pairs_data,$detailed_pairs,$icon_pairs){
if (!empty($icon_pairs)){
foreach ($icon_pairs as $icon_data){
if(isset($pairs_data[$icon_data['object_id']][$icon_data['pair_id']]['icon'])){
$pairs_data[$icon_data['object_id']][$icon_data['pair_id']]['icon']['transparent']=$icon_data['transparent'];}}}}
function fn_abt__unitheme2_get_products_layout_post(&$selected_view,$params){
$session=& Tygh::$app['session'];if (empty($session['changed_layout_categories'])){
$session['changed_layout_categories']=[];}
if (!empty($params['layout']) && !empty($params['category_id'])){
$session['changed_layout_categories'][]=$params['category_id'];}
if (
empty($params['layout'])
&& !empty($params['category_id'])
&& !in_array($params['category_id'],$session['changed_layout_categories'])
){
$selected_layout=db_get_field('SELECT ?p FROM ?:categories WHERE category_id=?i AND selected_views !="" ','default_view_'.Registry::get('settings.ab__device') ,$params['category_id']);$selected_view=$selected_layout;if (empty($selected_layout) || $selected_layout == 'inherit'){
$selected_view=Registry::get('settings.abt__ut2.product_list.default_products_view.'.Registry::get('settings.ab__device'));}}}