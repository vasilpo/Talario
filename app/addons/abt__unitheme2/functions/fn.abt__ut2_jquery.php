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
use Tygh\Registry;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
function fn_abt__ut2_get_jquery_version($type = 'main')
{
static $versions;
if (empty($versions)) {
foreach (glob(Registry::get('config.dir.root') . '/js/lib/jquery/jquery-*.min.js') as $jq) {
if (preg_match('#jquery-migrate-#', $jq)) {
preg_match('#([0-9]{1,2}.[0-9]{1,2}.[0-9]{1,2})#', basename($jq), $matches);
if (!empty($matches[0])) {
$versions['migrate'] = $matches[0];
}
} else {
preg_match('#([0-9]{1,2}.[0-9]{1,2}.[0-9]{1,2})#', basename($jq), $matches);
if (!empty($matches[0])) {
$versions['main'] = $matches[0];
}
}
}
}
return $versions[$type];
}
