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
$schema['abt__ut2'] = [
'permissions' => ['GET' => 'abt__ut2.settings.view', 'POST' => 'abt__ut2.settings.manage'],
'modes' => [
'settings' => [
'permissions' => [
'GET' => 'abt__ut2.settings.view',
],
],
'update_settings' => [
'permissions' => [
'POST' => 'abt__ut2.settings.manage',
],
],
'microdata' => [
'permissions' => [
'GET' => 'abt__ut2.settings.view',
],
],
'update_microdata' => [
'permissions' => [
'POST' => 'abt__ut2.settings.manage',
],
],
'demodata' => [
'permissions' => [
'GET' => 'abt__ut2.settings.view',
],
],
'update_demodata' => [
'permissions' => [
'POST' => 'abt__ut2.settings.manage',
],
],
'help' => [
'permissions' => [
'GET' => 'abt__ut2.settings.view',
],
],
],
];
return $schema;
