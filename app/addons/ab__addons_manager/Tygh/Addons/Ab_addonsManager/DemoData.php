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
use Tygh\Common\OperationResult;
use Tygh\Registry;
use Tygh\Http;
class DemoData
{
private static $api_url = 'https://docs.cs-cart.abt.team/demo/';
private static $api_timeout = 30;
public static function getFile($params = [], $override = true, $path = '')
{
$result = new OperationResult();
try {
if (empty($params['file'])) {
throw new \Exception('Filename is empty');
}
if (empty($params['addon'])) {
throw new \Exception('Addon is empty');
}
if (empty($path)) {
$path = Registry::get('config.dir.var') . "ab__data/{$params['addon']}/";
}
$file = basename($params['file']);
$directory = fn_normalize_path($path . '/' . basename($file, '.zip'));
if (file_exists($directory) && $override === false) {
throw new \Exception('Directory is exist! (' . $directory . ')');
}
fn_rm($directory, true);
fn_mkdir($directory);
$file_path = $directory . '/' . $file;
$data = [
'path' => $directory,
'download_time' => 0,
'decompress_time' => 0,
];
$download_time = microtime(true);
Http::$logging = false;
$request_result = Http::post(self::$api_url . 'get_file/', $params, [
'timeout' => self::$api_timeout,
'binary_transfer' => true,
'write_to_file' => $file_path,
]);
Http::$logging = true;
$data['download_time'] = microtime(true) - $download_time;
if (empty($request_result) || !filesize($file_path)) {
fn_rm($file_path);
throw new \Exception('File not found');
}
$decompress_time = microtime(true);
fn_decompress_files($file_path, dirname($file_path));
$data['decompress_time'] = microtime(true) - $decompress_time;
fn_rm($file_path);
$result->setData($data);
$result->setSuccess(true);
} catch (\Exception $e) {
$result->addError($e->getCode(), $e->getMessage());
}
return $result;
}
}
