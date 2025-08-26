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
use Tygh\Enum\ImagePairTypes;
use Tygh\Enum\YesNo;
defined('BOOTSTRAP') or die('Access denied');

function fn_ab__stickers_exim_get_sticker_appearance($sticker_id, $field, $lang_code)
{
static $stickers = [];
if (empty($stickers[$lang_code])) {

$repository = Tygh::$app['addons.ab__stickers.repository'];
list($stickers[$lang_code]) = $repository->find(['get_icons' => false], 0, $lang_code);
}
return $stickers[$lang_code][$sticker_id]['appearance'][$field] ?? '';
}

function fn_ab__stickers_exim_set_sticker_appearance($sticker_id, $field, $value, $lang_code)
{

$repository = Tygh::$app['addons.ab__stickers.repository'];
$sticker = $repository->findById($sticker_id, $lang_code);
unset($sticker['pictogram_data']);
$sticker_data = fn_array_merge($sticker, ['appearance' => [$field => $value]], true);
return fn_ab__stickers_update_sticker($sticker_id, ['sticker_data' => $sticker_data], $lang_code, true);
}

function fn_ab__stickers_exim_add_storefront_condition(&$conditions)
{
$conditions[] = db_quote('FIND_IN_SET (?i, storefront_ids)', Tygh::$app['storefront']->storefront_id);
}

function fn_ab__stickers_exim_get_image_url($sticker_id, $lang_code = DESCR_SL)
{
$image_path = '';
$image_id = db_get_field('SELECT image_id FROM ?:ab__sticker_images WHERE sticker_id = ?i AND lang_code = ?s', $sticker_id, $lang_code);
$image = fn_get_image_pairs($image_id, 'ab__stickers', ImagePairTypes::MAIN, true, true, $lang_code);
if (!empty($image) && !empty($image['icon'])) {
$image_path = $image['icon']['image_path'];
}
return $image_path;
}

function fn_ab__stickers_exim_set_image_url($sticker_id, $image_path = [], $is_high_res = YesNo::NO)
{
foreach ($image_path as $lang_code => $path) {

$repository = Tygh::$app['addons.ab__stickers.repository'];
$sticker = $repository->findById($sticker_id, $lang_code);
unset($sticker['pictogram_data']);
$sticker_image_name = 'ab__sticker_img_image';
$_REQUEST["{$sticker_image_name}_data"] = [
[
'type' => ImagePairTypes::MAIN,
'object_id' => 0,
],
];
$_REQUEST["file_{$sticker_image_name}_icon"] = [$path];
$_REQUEST["type_{$sticker_image_name}_icon"] = ['url'];
$_REQUEST["is_high_res_{$sticker_image_name}_icon"] = [$is_high_res];
fn_ab__stickers_update_sticker($sticker_id, ['sticker_data' => $sticker], $lang_code, true);
}
return 0;
}

function fn_ab__stickers_exim_unset_sticker_id(&$object = [])
{
unset($object['sticker_id']);
}
