<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2023   *
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
use Tygh\Registry;
function fn_settings_variants_addons_appearance_default_image_previewer()
{
$previewers_path = Registry::get('config.dir.root') . '/js/tygh/previewers';
$previewers = fn_get_dir_contents($previewers_path, false, true, 'js');
if (Registry::get('addons.ab__image_previewers.status') === 'A') {
$ab__previewers_path = Registry::get('config.dir.root') . '/js/addons/' . 'ab__image_previewers' . '/previewers';
$ab__previewers = fn_get_dir_contents($ab__previewers_path, false, true, 'js');
$previewers = array_merge($previewers, $ab__previewers);
}
$previewer_variants = [];
foreach ($previewers as $previewer) {
$is_ab__previewer = isset($ab__previewers_path) && strpos($previewer, 'ab__') !== false;
$previewer_path = $is_ab__previewer
? $ab__previewers_path
: $previewers_path;
$previewer_description = $is_ab__previewer
? __(fn_basename($previewer, '.previewer.js'))
: fn_get_file_description($previewer_path . '/' . $previewer, 'previewer-description');
$previewer_variants[fn_basename($previewer, '.previewer.js')] = $previewer_description;
}
return $previewer_variants;
}
