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
use Tygh\Enum\YesNo;
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');
include_once Registry::get('config.dir.addons') . 'ab__stickers/schemas/exim/ab__stickers.functions.php';
$schema = [
'section' => 'ab__stickers',
'name' => __('ab__stickers'),
'pattern_id' => 'ab__stickers',
'key' => ['sticker_id'],
'order' => 0,
'order_by' => 'sticker_id, ab__stickers.position',
'table' => 'ab__stickers',
'permissions' => [
'import' => 'ab__stickers.data.manage',
'export' => 'ab__stickers.data.view',
],
'references' => [
'ab__sticker_descriptions' => [
'reference_fields' => ['sticker_id' => '#key', 'lang_code' => '#lang_code'],
'join_type' => 'LEFT',
],
'ab__sticker_images' => [
'reference_fields' => ['sticker_id' => '#key', 'lang_code' => '#lang_code'],
'join_type' => 'LEFT',
],
],
'pre_export_process' => [
'add_storefront_condition' => [
'function' => 'fn_ab__stickers_exim_add_storefront_condition',
'args' => ['$conditions'],
'export_only' => true,
],
],
'import_process_data' => [
'unset_sticker_id' => [
'function' => 'fn_ab__stickers_exim_unset_sticker_id',
'args' => ['$object'],
'import_only' => true,
],
],
'options' => [
'lang_code' => [
'title' => 'language',
'type' => 'languages',
'default_value' => [DEFAULT_LANGUAGE],
'position' => 100,
],
],
'export_fields' => [
'Sticker ID' => [
'db_field' => 'sticker_id',
],
'Language' => [
'table' => 'ab__sticker_descriptions',
'db_field' => 'lang_code',
'type' => 'languages',
'required' => true,
'multilang' => true,
],
'Status' => [
'db_field' => 'status',
],
'Position' => [
'db_field' => 'position',
],
'Type' => [
'db_field' => 'type',
'required' => true,
],
'Style' => [
'db_field' => 'style',
'required' => true,
],
'Store' => [
'db_field' => 'storefront_ids',
'required' => fn_allowed_for('ULTIMATE'),
],
'Name for admin' => [
'db_field' => 'name_for_admin',
'required' => true,
'alt_key' => true,
],
'Name for desktop' => [
'table' => 'ab__sticker_descriptions',
'db_field' => 'name_for_desktop',
'multilang' => true,
],
'Name for mobile' => [
'table' => 'ab__sticker_descriptions',
'db_field' => 'name_for_mobile',
'multilang' => true,
],
'Description' => [
'table' => 'ab__sticker_descriptions',
'db_field' => 'description',
'multilang' => true,
],
'Image URL' => [
'process_get' => ['fn_ab__stickers_exim_get_image_url', '#key', '#lang_code'],
'process_put' => ['fn_ab__stickers_exim_set_image_url', '#key', '#this', '@is_high_res'],
'multilang' => true,
'linked' => false,
],
'Background' => [
'linked' => false,
'process_get' => ['fn_ab__stickers_exim_get_sticker_appearance', '#key', 'sticker_bg', '#lang_code'],
'process_put' => ['fn_ab__stickers_exim_set_sticker_appearance', '#key', 'sticker_bg', '#this', '#lang_code'],
],
'Text color' => [
'linked' => false,
'process_get' => ['fn_ab__stickers_exim_get_sticker_appearance', '#key', 'text_color', '#lang_code'],
'process_put' => ['fn_ab__stickers_exim_set_sticker_appearance', '#key', 'text_color', '#this', '#lang_code'],
],
'Border color' => [
'linked' => false,
'process_get' => ['fn_ab__stickers_exim_get_sticker_appearance', '#key', 'border_color', '#lang_code'],
'process_put' => ['fn_ab__stickers_exim_set_sticker_appearance', '#key', 'border_color', '#this', '#lang_code'],
],
'Border width' => [
'linked' => false,
'process_get' => ['fn_ab__stickers_exim_get_sticker_appearance', '#key', 'border_width', '#lang_code'],
'process_put' => ['fn_ab__stickers_exim_set_sticker_appearance', '#key', 'border_width', '#this', '#lang_code'],
],
'Uppercase text' => [
'linked' => false,
'process_get' => ['fn_ab__stickers_exim_get_sticker_appearance', '#key', 'uppercase_text', '#lang_code'],
'process_put' => ['fn_ab__stickers_exim_set_sticker_appearance', '#key', 'uppercase_text', '#this', '#lang_code'],
],
],
];
if (Registry::get('addons.hidpi.status') === ObjectStatuses::ACTIVE) {
$schema['options']['is_high_res'] = [
'title' => 'hidpi.upload_high_res_image',
'type' => 'checkbox',
'default_value' => YesNo::NO,
'import_only' => true,
];
}
return $schema;
