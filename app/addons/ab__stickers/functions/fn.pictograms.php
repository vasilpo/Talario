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
use Imagine\Driver\InfoProvider;
use Imagine\Image\Box;
use Imagine\Image\Format;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point\Center;
use Tygh\Addons\Ab_stickers\Imagine\Image\Point\ABPictogramPoint;
use Tygh\Enum\Addons\Ab_stickers\PictogramPositions;
use Tygh\Enum\Addons\Ab_stickers\StickerStyles;
use Tygh\Enum\FileUploadTypes;
use Tygh\Enum\ImagePairTypes;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\ProductFeatures;
use Tygh\Enum\YesNo;
use Tygh\Languages\Languages;
use Tygh\Registry;
use Tygh\Storage;
use Tygh\Tools\Archivers\ZipArchiveReader;
defined('BOOTSTRAP') or die('Access denied');
define('AB_S_PICTOGRAMS_FONTS_DIRECTORY', Registry::get('config.dir.design_backend') . str_replace('\\', DIRECTORY_SEPARATOR, 'media\fonts\ab__stickers\\'));

function fn_ab__stickers_pictograms_get_fonts($groups = ['default', 'custom'], $fonts_type = ['ttf'])
{
$fonts = [];
foreach ($groups as $group) {
$fonts[$group] = [];
}
$fonts_folders = array_filter(scandir(AB_S_PICTOGRAMS_FONTS_DIRECTORY), function ($value) {
return $value !== '.' && $value !== '..';
});
foreach ($fonts_folders as $folder) {
if (!isset($fonts[$folder])) {
continue;
}
$exists_fonts = fn_get_dir_contents(AB_S_PICTOGRAMS_FONTS_DIRECTORY . $folder, false, true, $fonts_type, '', true);
foreach ($exists_fonts as $font) {
$font_info = pathinfo(AB_S_PICTOGRAMS_FONTS_DIRECTORY . $folder . DIRECTORY_SEPARATOR . $font);
$fonts[$folder][] = [
'name' => $font_info['filename'],
'path' => str_replace(AB_S_PICTOGRAMS_FONTS_DIRECTORY, '', $font_info['dirname'] . DIRECTORY_SEPARATOR . $font_info['basename']),
];
}
}
return $fonts;
}

function fn_ab__stickers_pictograms_add_fonts($uploaded_files)
{
$total_fonts = [];
$tmp_dirs = [];
Registry::set('config.storage.ab__sticker_fonts', [
'prefix' => str_replace('\\', DIRECTORY_SEPARATOR, 'media\fonts\ab__stickers\custom'),
'dir' => Registry::get('config.dir.design_backend'),
]);
foreach ($uploaded_files as $uploaded_file) {
if ($uploaded_file['type'] === 'application/x-zip-compressed' || $uploaded_file['type'] === 'application/zip') {
$archive_reader = new ZipArchiveReader($uploaded_file['path']);
$fonts_tmp_path = $tmp_dirs[] = $uploaded_file['path'] . '_unpacked' . DIRECTORY_SEPARATOR;
$archive_reader->extractTo($fonts_tmp_path);
foreach (fn_get_dir_contents($fonts_tmp_path, false, true, 'ttf', '', true) as $extracted_font) {
$total_fonts[] = ['name' => $extracted_font, 'path' => $fonts_tmp_path];
}
} else {
$total_fonts[] = ['name' => $uploaded_file['name'], 'path' => $uploaded_file['path']];
}
}
foreach ($total_fonts as $font) {
Storage::instance('ab__sticker_fonts')->put($font['name'], [
'file' => $font['path'] . $font['name'],
'overwrite' => true,
]);
}
Registry::del('config.storage.ab__sticker_fonts');
foreach ($tmp_dirs as $tmp_dir) {
if (is_dir($tmp_dir)) {
fn_rm($tmp_dir);
}
}
}

function fn_ab__stickers_pictograms_delete_fonts($paths)
{
$repository = Tygh::$app['addons.ab__stickers.repository'];
$pictograms_params = [
'get_icons' => false,
'styles' => [StickerStyles::PICTOGRAM],
];
list($pictograms, $search) = $repository->find($pictograms_params);
$used_fonts = [];
$not_deleted_fonts = [];
$message = '';
foreach ($pictograms as $pictogram) {
$pictogram_texts = fn_ab__stickers_pictograms_get_texts($pictogram['sticker_id']);
foreach ($pictogram_texts as $text) {
if (!isset($used_fonts[$text['font']['name']]) || !in_array($pictogram['sticker_id'], $used_fonts[$text['font']['name']])) {
$used_fonts[$text['font']['name']][] = $pictogram['sticker_id'];
}
}
}
foreach ($paths as $path) {
if (isset($used_fonts[$path])) {
$not_deleted_fonts[$path] = $used_fonts[$path];
} elseif (is_file(AB_S_PICTOGRAMS_FONTS_DIRECTORY . $path)) {
fn_rm(AB_S_PICTOGRAMS_FONTS_DIRECTORY . $path);
}
}
foreach ($not_deleted_fonts as $font => $pictograms_list) {
$stickers = [];
foreach ($pictograms_list as $pictogram_id) {
$stickers[] = '<a target="_blank" href="' . fn_url("ab__stickers.update&sticker_id={$pictogram_id}") . '">' . $pictograms[$pictogram_id]['name_for_admin'] . '</a>';
}
$font_info = pathinfo(AB_S_PICTOGRAMS_FONTS_DIRECTORY . $font);
$message .= $font_info['filename'] . ':<br>' . implode(', ', $stickers) . '<br>';
}
return $message;
}

function fn_ab__stickers_pictograms_generate($pictogram_ids = [], $params = [], $items_per_page = 0, $for_preview = false, $is_ajax = true)
{
$generated_for = [];
$default_params = [
'styles' => [StickerStyles::PICTOGRAM],
'sort_by' => 'last_update_time_pictogram',
'sort_order' => 'asc',
];
$params = array_merge($default_params, $params);
if (!empty($pictogram_ids)) {
$params['item_ids'] = $pictogram_ids;
}

$repository = Tygh::$app['addons.ab__stickers.repository'];
$pictograms_count = $repository->find(array_merge($params, [
'count_only' => 'sl',
]), $items_per_page);
if ($pictograms_count) {
$pictograms_tmp_dir = str_replace('/', DIRECTORY_SEPARATOR, Registry::get('config.dir.cache_misc') . 'tmp/ab__pictograms/');
if (!is_dir($pictograms_tmp_dir)) {
fn_mkdir($pictograms_tmp_dir);
}
$languages = Languages::getActive();
$languages_count = count($languages);
$is_commet = defined('AJAX_REQUEST') && $is_ajax;
if ($is_commet) {
fn_set_progress('parts', $pictograms_count * $languages_count);
fn_set_progress('title', __('ab__stickers.cron.generate_pictograms_title'));
}
$first_language = true;
$current_language_i = 0;
foreach ($languages as $language) {
list($pictograms, $search) = $repository->find($params, $items_per_page, $language['lang_code']);
$current_language_i++;
foreach ($pictograms as $pictogram) {
$sticker_id = $pictogram['sticker_id'];
if ($first_language) {
$condition = db_quote(' AND product_id != 0');
if ($for_preview) {
$condition = db_quote(' AND product_id = 0');
}
$pictogram_images = db_get_fields('SELECT image_id FROM ?:ab__sticker_pictograms WHERE sticker_id=?i ?p', $sticker_id, $condition);
db_query('DELETE FROM ?:ab__sticker_pictograms WHERE sticker_id=?i ?p', $sticker_id, $condition);
$pictograms_image_pair = fn_get_image_pairs($pictogram_images, 'ab__pictograms', ImagePairTypes::MAIN, false, false, $language['lang_code']);
foreach ($pictograms_image_pair as $pair) {
if (isset($pair['pair_id'], $pair['object_type'])) {
fn_delete_image_pair($pair['pair_id'], $pair['object_type']);
}
}
}
if ($for_preview) {
$products = fn_ab__stickers_pictograms_get_demo_products();
} else {
$products = db_get_array('SELECT product_id FROM ?:products WHERE FIND_IN_SET(?i,ab__stickers_manual_ids) OR FIND_IN_SET(?i,ab__stickers_generated_ids)', $sticker_id, $sticker_id);
}
$products_count = 0;
foreach ($products as $product) {
try {
$products_count += fn_ab__stickers_generate_pictogram($pictogram, $language['lang_code'], $product['product_id']);
} catch (Exception $e) {
if (!defined('CONSOLE')) {
fn_set_notification(NotificationSeverity::ERROR, __('error'), $e->getMessage());
} else {
fn_print_r($e->getMessage());
}
break;
}
}
if ($products_count) {
$generated_for[] = defined('CONSOLE') ? "sticker {$sticker_id} (links count: {$products_count})" : __('ab__stickers.pictogram.generated_pictogram', ['[sticker]' => "{$pictogram['name_for_admin']} ({$language['name']})", '[sticker_href]' => fn_url('ab__stickers.update?sticker_id=' . $pictogram['sticker_id']), '[count]' => $products_count]);
}
if ($is_commet) {
fn_set_progress('echo', $pictogram['name_for_admin'] . "({$language['name']})");
}
if ($languages_count == $current_language_i && !$for_preview) {
db_query('UPDATE ?:ab__stickers SET last_update_time_pictogram = ?i WHERE sticker_id = ?i', TIME, $sticker_id);
}
}
$first_language = false;
}
fn_rm($pictograms_tmp_dir);
sleep(1);
}
return [!empty($generated_for), $generated_for];
}

function fn_ab__stickers_generate_pictogram($pictogram, $lang_code, $product_id)
{
static $imagine;
static $pictograms_images_ids = [];
static $icons = [];
if (is_null($imagine)) {

$imagine = Tygh::$app['addons.ab__stickers.image'];
if (!$imagine instanceof InfoProvider) {
throw new RuntimeException('Can`t load any graphic library. Please contact your system administrator.');
}
if (!$imagine::getDriverInfo()->getSupportedFormats()->find(Format::ID_PNG) instanceof Format) {
throw new RuntimeException((new ReflectionClass($imagine))->getShortName() . ' graphic library doesn`t support PNG format. Please contact your system administrator.');
}
}
$pictogram_data = [
'sticker_id' => $pictogram['sticker_id'],
'product_id' => $product_id,
'lang_code' => $lang_code,
];
$pictogram_settings = $pictogram['appearance'];
$pictogram_size = max($pictogram_settings['full_size_image_size'], $pictogram_settings['small_size_image_size']) * 2;
if (isset($icons[$lang_code][$pictogram['sticker_id']])) {
$icon = $icons[$lang_code][$pictogram['sticker_id']];
} else {
$image_id = db_get_field('SELECT image_id FROM ?:ab__sticker_images WHERE sticker_id = ?i AND lang_code = ?s', $pictogram['sticker_id'], $lang_code);
$icon = fn_get_image_pairs($image_id, 'ab__stickers', ImagePairTypes::MAIN, true, true, $lang_code);
$icon = $icons[$lang_code][$pictogram['sticker_id']] = $icon['icon']['absolute_path'] ?? null;
}
$texts = fn_ab__stickers_pictograms_get_texts($pictogram['sticker_id'], null, $lang_code, !!$product_id);
$pictogram_hash = fn_ab__stickers_pictograms_calculate_hash($pictogram_settings, $texts, $icon, $pictogram_size, $product_id, $lang_code);
if ($pictogram_hash === false) {
return 0;
}
if (isset($pictograms_images_ids[$pictogram_hash])) {
$pictogram_data['image_id'] = $pictograms_images_ids[$pictogram_hash];
db_query('INSERT INTO ?:ab__sticker_pictograms ?e', $pictogram_data);
} else {
$palette = new RGB();
$box = new Box($pictogram_size, $pictogram_size);
$box_point_center = new Center($box);
$pictogram_background_color = fn_ab__stickers_pictograms_convert_rgba_to_rgb($pictogram_settings['sticker_bg'], [255, 255, 255]);
$pictogram_color = $palette->color($pictogram_background_color[0], $pictogram_background_color[1]);
$pictogram_imagine = $imagine->create($box, $pictogram_color);
if ($icon && is_file($icon)) {
$sticker = $imagine->open($icon);
$sticker->resize(new Box($pictogram_size, $pictogram_size));
$pictogram_imagine->paste($sticker, new ABPictogramPoint(0, 0), 100);
}
foreach ($texts as $text) {
$text['status'] = $text['status'] ?? ObjectStatuses::DISABLED;
if (isset($text['text']) && trim($text['text']) && $text['status'] === ObjectStatuses::ACTIVE) {
$text['text'] = fn_ab__stickers_pictograms_replace_text_with_placeholders($text['text'], $lang_code, $product_id);
$text_color = fn_ab__stickers_pictograms_convert_rgba_to_rgb($text['color']);
$text_font = $imagine->font(fn_ab__stickers_pictograms_get_font($text['font']['name']), $text['font']['size'], $palette->color($text_color[0], $text_color[1]));
$text_background_color = $palette->color('#000000', 0);
$text_stroke_params = [];
if ($text_stoke_width = (float) $text['stroke']['width']) {
$text_stroke_color = fn_ab__stickers_pictograms_convert_rgba_to_rgb($text['stroke']['color']);
$text_stroke_params = [
'stroke_width' => $text_stoke_width,
'stroke_color' => $palette->color($text_stroke_color[0], $text_stroke_color[1]),
];
}
$text_box = $text_font->box($text['text'], 0, $text_stroke_params);
$text_box_image = $imagine->create($text_box, $text_background_color);
$text_box_image->draw()->text($text['text'], $text_font, new ABPictogramPoint(0, 0), 0, null, $text_stroke_params);
if ((int) $text['rotation']) {
$text_box_image->rotate($text['rotation'], $text_background_color);
}
switch ($text['position']['horizontal']) {
case PictogramPositions::HORIZONTAL_LEFT:
$x = (int) $text['margin']['horizontal'];
break;
case PictogramPositions::HORIZONTAL_CENTER:
$x = $box_point_center->getX() - ($text_box_image->getSize()->getWidth() / 2);
break;
case PictogramPositions::HORIZONTAL_RIGHT:
$x = $box->getWidth() - $text_box_image->getSize()->getWidth();
$x -= (int) $text['margin']['horizontal'];
break;
default:
$x = 0;
break;
}
switch ($text['position']['vertical']) {
case PictogramPositions::VERTICAL_TOP:
$y = (int) $text['margin']['vertical'];
break;
case PictogramPositions::VERTICAL_MIDDLE:
$y = $box_point_center->getY() - ($text_box_image->getSize()->getHeight() / 2);
break;
case PictogramPositions::VERTICAL_BOTTOM:
$y = $box->getHeight() - $text_box_image->getSize()->getHeight();
$y -= (int) $text['margin']['vertical'];
break;
default:
$y = 0;
break;
}
$pictogram_imagine->paste($text_box_image, new ABPictogramPoint($x, $y), 100);
}
}
$pictogram_image_path = str_replace('/', DIRECTORY_SEPARATOR, Registry::get('config.dir.cache_misc') . 'tmp/ab__pictograms/');
$pictogram_image_name = md5($pictogram['sticker_id'] . '_' . $product_id . '_' . $lang_code . '_' . TIME) . '.png';
$pictogram_imagine->save($pictogram_image_path . $pictogram_image_name);
$pictograms_images_ids[$pictogram_hash] = $pictogram_image_id = db_query('INSERT INTO ?:ab__sticker_pictograms ?e', $pictogram_data);
fn_update_image_pairs([[
'name' => $pictogram_image_name,
'path' => $pictogram_image_path . $pictogram_image_name,
'size' => filesize($pictogram_image_path . $pictogram_image_name),
'upload_type' => FileUploadTypes::SERVER,
'is_high_res' => '',
]], [], [[
'pair_id' => '',
'type' => ImagePairTypes::MAIN,
'object_id' => 0,
'image_alt' => '',
]], $pictogram_image_id, 'ab__pictograms', [], true, $lang_code);
}
return 1;
}

function fn_ab__stickers_pictograms_convert_rgba_to_rgb($rgba, $default = [0, 0, 0])
{
$rgba = str_replace(' ', '', $rgba);
preg_match('/^rgba*\\((\\d{1,3}),\\s*(\\d{1,3}),\\s*(\\d{1,3}),*\\s*(\\d*(?:\\.\\d+)*?)\\)$/', $rgba, $pieces);
if (isset($pieces[4]) && trim($pieces[4]) !== '') {
$alpha = (float) $pieces[4] * 100;
} else {
$alpha = 100;
}
$rgb = $default;
if (isset($pieces[3])) {
$rgb = [$pieces['1'], $pieces['2'], $pieces['3']];
}
if ((int) $alpha === 0) {
$rgb = [0, 0, 0];
}
return [$rgb, (int) $alpha];
}

function fn_ab__stickers_pictograms_get_font($font_name, $only_name = false)
{
$font = AB_S_PICTOGRAMS_FONTS_DIRECTORY . str_replace('\\', DIRECTORY_SEPARATOR, $font_name);
if (is_file($font)) {
return $only_name ? str_replace(AB_S_PICTOGRAMS_FONTS_DIRECTORY, '', $font) : $font;
}
$default_font = fn_ab__stickers_pictograms_get_fonts(['default'])['default'][0]['path'] ?? '';
if ($default_font) {
return $only_name ? $default_font : AB_S_PICTOGRAMS_FONTS_DIRECTORY . $default_font;
}
return '';
}

function fn_ab__stickers_pictograms_replace_text_with_placeholders($text, $lang_code, $product_id)
{
$text = preg_replace_callback('/\[feature_(\d+)([_|a-z]+)*\]/', function ($match) use ($lang_code, $product_id) {
if ($product_id) {
$features = fn_get_product_features([
'product_id' => $product_id,
'feature_id' => $match[1],
'exclude_group' => true,
'exclude_empty_groups' => true,
'existent_only' => true,
'variants' => true,
'variants_selected_only' => true,
], 0, $lang_code);
} else {
$features = fn_ab__stickers_pictograms_get_default_features($match[1], $lang_code);
}
$feature = $features[0][$match[1]] ?? [];
$value = '';
if ($feature && in_array($feature['feature_type'], ProductFeatures::getSelectableList())) {
$value = $feature['variants'][$feature['variant_id']]['variant'] ?? '';
} elseif ($feature['feature_type'] === ProductFeatures::NUMBER_FIELD) {
$value = (string) ($feature['value_int'] + 0);
} else {
$value = $feature['value'];
}
if ($value) {
$params = explode('|', $match[2] ?? '');
$space = isset($params[1]) && $params[1] === 'space' ? ' ' : '';
if (isset($params[0]) && $params[0] === '_name') {
return trim($feature['description']);
}
if (isset($params[0]) && $params[0] === '_with_prefix_and_suffix' && isset($feature['prefix'])) {
$prefix = $feature['prefix'];
$suffix = $feature['suffix'];
return trim($prefix . $space . $value . $space . $suffix);
}
if (isset($params[0]) && $params[0] === '_with_prefix' && isset($feature['prefix'])) {
$prefix = $feature['prefix'];
return trim($prefix . $space . $value);
}
if (isset($params[0]) && $params[0] === '_with_suffix' && isset($feature['suffix'])) {
$suffix = $feature['suffix'];
return trim($value . $space . $suffix);
}
if (isset($params[0]) && $params[0] === '_prefix' && isset($feature['prefix'])) {
$prefix = $feature['prefix'];
return trim($prefix);
}
if (isset($params[0]) && $params[0] === '_suffix' && isset($feature['suffix'])) {
$suffix = $feature['suffix'];
return trim($suffix);
}
if (isset($params[0]) && trim($params[0])) {
return $match[0];
}
return trim($value);
}
return $match[0];
}, $text);
return $text;
}

function fn_ab__stickers_pictograms_get_image_id($sticker_id, $product_id, $lang_code = CART_LANGUAGE)
{
$image_id = db_get_field('SELECT image_id FROM ?:ab__sticker_pictograms WHERE sticker_id=?i AND product_id=?i AND lang_code=?s', $sticker_id, $product_id, $lang_code);
return $image_id ?: 0;
}

function fn_ab__stickers_pictograms_get_demo_products()
{
$products[] = [
'product_id' => 0,
];
return $products;
}

function fn_ab__stickers_pictograms_get_data($sticker_id, $lang_code)
{
$pictogram_data = db_get_row('SELECT ?:ab__stickers.pictogram_data, ?:ab__sticker_descriptions.pictogram_data as description FROM ?:ab__stickers LEFT JOIN ?:ab__sticker_descriptions ON (?:ab__stickers.sticker_id = ?:ab__sticker_descriptions.sticker_id AND ?:ab__sticker_descriptions.lang_code=?s) WHERE ?:ab__stickers.sticker_id=?i', $lang_code, $sticker_id);
return $pictogram_data;
}

function fn_ab__stickers_pictograms_get_texts($sticker_id, $text_key = null, $lang_code = DESCR_SL, $cache = true)
{
static $pictograms_texts;
if (!isset($pictograms_texts[$lang_code][$sticker_id])) {
$pictogram_texts = fn_ab__stickers_pictograms_get_data($sticker_id, $lang_code);
$default_texts = fn_ab__stickers_pictograms_get_default_texts();
if (!empty($pictogram_texts['pictogram_data']) && !empty($pictogram_texts['description'])) {
$pictograms_texts[$lang_code][$sticker_id] = array_replace_recursive($default_texts, fn_ab__stickers_decode_data($pictogram_texts['pictogram_data']), fn_ab__stickers_decode_data($pictogram_texts['description']));
} elseif (!empty($pictogram_texts['pictogram_data'])) {
$pictograms_texts[$lang_code][$sticker_id] = array_replace_recursive($default_texts, fn_ab__stickers_decode_data($pictogram_texts['pictogram_data']));
} else {
$pictograms_texts[$lang_code][$sticker_id] = fn_ab__stickers_pictograms_get_default_texts();
}
}
$texts = $pictograms_texts[$lang_code][$sticker_id];
if (!$cache) {
unset($pictograms_texts[$lang_code][$sticker_id]);
}
return $texts[$text_key] ?? $texts;
}

function fn_ab__stickers_pictograms_update_texts($sticker_id, $data, $lang_code = DESCR_SL)
{
$default_texts = fn_ab__stickers_pictograms_get_default_texts();
$old_data = fn_ab__stickers_pictograms_get_data($sticker_id, $lang_code);
$text_key = array_key_first($data);
$data_text = $data[$text_key]['text'] ?? false;
if (isset($data[$text_key]['text'])) {
unset($data[$text_key]['text']);
}
if (!empty($old_data['pictogram_data'])) {
$data = array_replace_recursive($default_texts, fn_ab__stickers_decode_data($old_data['pictogram_data']), $data);
}
$update_for_all_languages = isset($data['for_all_languages']) && $data['for_all_languages'] === YesNo::YES;
if (isset($data['for_all_languages'])) {
unset($data['for_all_languages']);
}
db_query('UPDATE ?:ab__stickers SET pictogram_data=?s WHERE sticker_id=?i', fn_ab__stickers_encode_data($data), $sticker_id);
if ($data_text !== false) {
$languages = Languages::get(['lang_code' => $lang_code]);
if ($update_for_all_languages) {
$languages = Languages::getAll();
}
foreach ($languages as $language) {
$old_text_data = fn_ab__stickers_pictograms_get_data($sticker_id, $language['lang_code']);
$new_text_data = [$text_key => ['text' => $data_text]];
if (!empty($old_text_data['description'])) {
$new_text_data = array_replace_recursive(fn_ab__stickers_decode_data($old_text_data['description']), $new_text_data);
}
db_query('UPDATE ?:ab__sticker_descriptions SET pictogram_data=?s WHERE sticker_id=?i AND lang_code=?s', fn_ab__stickers_encode_data($new_text_data), $sticker_id, $language['lang_code']);
}
}
}
function fn_ab__stickers_pictograms_get_default_texts()
{
$texts = [];
for ($i = 1; $i <= 2; $i++) {
$texts['text' . $i] = fn_get_schema('ab__stickers', 'pictogram_text');
}
return $texts;
}
function fn_ab__stickers_pictograms_get_default_features($feature_id, $lang_code)
{
$default_lang_code = 'en';
$raw_description = [
'en' => [
'description' => 'RAM',
'internal_name' => 'RAM',
'lang_code' => $lang_code,
'prefix' => '',
'suffix' => 'GB',
'value' => 16,
'value_int' => 16,
'feature_type' => ProductFeatures::NUMBER_FIELD
],
'uk' => [
'description' => 'RAM',
'internal_name' => 'RAM',
'lang_code' => $lang_code,
'prefix' => '',
'suffix' => fn_unicode_to_utf8('%u0413%u0411'),
'value' => 16,
'value_int' => 16,
'feature_type' => ProductFeatures::NUMBER_FIELD
],
'ru' => [
'description' => 'RAM',
'internal_name' => 'RAM',
'lang_code' => $lang_code,
'prefix' => '',
'suffix' => fn_unicode_to_utf8('%u0413%u0411'),
'value' => 16,
'value_int' => 16,
'feature_type' => ProductFeatures::NUMBER_FIELD
]
];
$features[0][$feature_id] = [
'feature_id' => $feature_id,
];
$features[0][$feature_id] = array_replace_recursive($features[0][$feature_id], $raw_description[$lang_code] ?? $raw_description[$default_lang_code] ?? []);
return $features;
}
function fn_ab__stickers_pictograms_calculate_hash($pictogram_settings, $texts, $icon, $pictogram_size, $product_id, $lang_code)
{
$hash = [];
$texts_join = [];
foreach ($texts as &$text) {
if (
isset($text['status'])
&& $text['status'] === ObjectStatuses::ACTIVE
&& isset($text['text'])
&& trim($text['text'])
) {
$texts_join[] = $text['text'];
unset($text['text']);
}
}
if (!empty($texts_join) || ($icon && is_file($icon))) {
$hash[] = $icon;
$hash[] = $pictogram_size;
$hash[] = fn_ab__stickers_encode_data(fn_ab__stickers_pictograms_convert_rgba_to_rgb($pictogram_settings['sticker_bg'], [255, 255, 255]), 'serialized');
$hash[] = fn_ab__stickers_encode_data($texts, 'serialized');
foreach ($texts_join as $text_join) {
$hash[] = fn_ab__stickers_pictograms_replace_text_with_placeholders($text_join, $lang_code, $product_id);
}
$hash = md5(implode('.', $hash));
}
return is_array($hash) ? false : $hash;
}
