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
return [
'ab__stickers.settings' => [
'href' => 'addons.update&addon=ab__stickers',
'title' => __('ab__stickers.settings'),
],
'ab__stickers.manage' => [
'href' => 'ab__stickers.manage',
'title' => __('ab__stickers.manage'),
],
'ab__stickers.fonts' => [
'href' => 'ab__stickers.fonts',
'title' => __('ab__stickers.pictogram.fonts'),
],
'ab__stickers.cron_jobs' => [
'href' => 'ab__stickers.generate',
'title' => __('ab__stickers.cron_jobs'),
],
];
