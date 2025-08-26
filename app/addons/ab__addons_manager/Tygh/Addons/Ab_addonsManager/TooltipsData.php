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
namespace Tygh\Addons\Ab_addonsManager;
use Tygh\Registry;
use Tygh\Http;
class TooltipsData
{
private static $api_url = 'https://docs.cs-cart.abt.team/tooltips.sql';
private static $api_timeout = 30;
private static $table = '?:ab__am_tooltips';
private static $storage_key = 'ab__am_tooltips';
public static function downloadTooltips($params = [], $override = true, $path = '')
{
Http::$logging = false;
$extra = [
'timeout' => self::$api_timeout,
'encoding' => 'gzip',
'headers' => [
'If-None-Match: ' . self::getEtag(),
],
];
$request_result = Http::get(self::$api_url, [], $extra);
switch (Http::getStatus()) {
case 200:
self::setEtag(self::parseHttpEtag());
self::updateTooltips($request_result);
break;
case 304:
break;
default:
self::setEtag('');
self::removeTooltips();
}
}
private static function getEtag()
{
return fn_get_storage_data(self::$storage_key . '_etag');
}
private static function setEtag($etag)
{
fn_set_storage_data(self::$storage_key . '_etag', $etag);
}
private static function parseHttpEtag()
{
$headers = Http::getHeaders();
if (preg_match('/etag: (.+)/', $headers, $m)) {
return str_replace('W/', '', $m[1]);
}
return '';
}
private static function updateTooltips($data)
{
if (!empty($data) && strpos($data, 'INSERT INTO ' . self::$table) !== false) {
$tmp_file = fn_create_temp_file();
fn_put_contents($tmp_file, $data);
if (file_exists($tmp_file)) {
self::removeTooltips();
db_import_sql_file($tmp_file, 16384, false, false);
unlink($tmp_file);
}
}
}
private static function removeTooltips()
{
db_query('TRUNCATE TABLE ' . self::$table);
}
public static function getTooltips()
{
$result = [];
$dispatch[] = Registry::get('runtime.controller') . '.' . Registry::get('runtime.mode');
if (Registry::get('runtime.mode') == 'add') {
$dispatch[] = Registry::get('runtime.controller') . '.update';
}
$data = db_get_hash_multi_array('SELECT t.addon, t.item, t.item_data, a.version
FROM ' . self::$table . " AS t
INNER JOIN ?:addons AS a ON (a.status = 'A' AND a.addon LIKE 'ab%\_\_%' AND a.addon = t.addon)
WHERE t.dispatch IN (?a) AND t.version_min <= a.version AND a.version <= t.version_max", ['addon', 'item'], $dispatch);
if (!empty($data)) {
$lang_code = in_array(CART_LANGUAGE, ['ru', 'uk']) ? 'ru' : 'en';
foreach ($data as $addon => $tooltips) {
foreach ($tooltips as $tooltip_key => $tooltip) {
$d = unserialize($tooltip['item_data']);
if (!empty($d)) {
$result[$addon][$tooltip_key] = [
'selector' => $d['selector'],
'url' => "https://docs.cs-cart.abt.team/{$lang_code}/{$addon}.doc/v{$tooltip['version']}#{$d['anchor']}",
];
}
}
}
}
return $result;
}
}
