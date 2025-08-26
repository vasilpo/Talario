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
defined('BOOTSTRAP') or die('Access denied');
$schema['central']['ab__addons']['items']['ab__stickers'] = [
'attrs' => ['class' => 'is-addon'],
'href' => 'ab__stickers.manage',
'position' => 10,
'subitems' => [
'ab__stickers.settings' => [
'href' => 'addons.update&addon=ab__stickers',
'position' => 0,
],
'ab__stickers.manage' => [
'href' => 'ab__stickers.manage',
'position' => 1000,
],
'ab__stickers.pictogram.fonts' => [
'href' => 'ab__stickers.fonts',
'position' => 2000,
],
'ab__stickers.cron_jobs' => [
'href' => 'ab__stickers.generate',
'position' => 3000,
],
'ab__stickers.demodata' => [
'href' => 'ab__stickers.demodata',
'position' => 4000,
],
'ab__stickers.help' => [
'href' => 'ab__stickers.help',
'position' => 10000,
],
],
];
$schema['top']['administration']['items']['import_data']['subitems']['ab__stickers_import'] = [
'href' => 'exim.import?section=ab__stickers',
'position' => 1000,
];
$schema['top']['administration']['items']['export_data']['subitems']['ab__stickers_export'] = [
'href' => 'exim.export?section=ab__stickers',
'position' => 1000,
];
return $schema;
