<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2024   *
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
$schema['products']['items']['vendor'] = [
'position' => 199,
'is_group' => 'Y',
'items' => [
'show_name_as_link' => [
'type' => 'checkbox',
'position' => 100,
'value' => [
'desktop' => 'Y',
'tablet' => 'Y',
'mobile' => 'Y',
],
],
'show_logo' => [
'type' => 'checkbox',
'position' => 200,
'value' => [
'desktop' => 'Y',
'tablet' => 'Y',
'mobile' => 'Y',
],
],
'truncate_short_description' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 300,
'value' => [
'desktop' => 90,
'tablet' => 90,
'mobile' => 90,
],
],
'show_phone' => [
'type' => 'checkbox',
'position' => 400,
'value' => [
'desktop' => 'Y',
'tablet' => 'Y',
'mobile' => 'Y',
],
],
'show_ask_question_link' => [
'type' => 'checkbox',
'position' => 500,
'value' => [
'desktop' => 'Y',
'tablet' => 'Y',
'mobile' => 'Y',
],
'is_addon_dependent' => 'Y',
],
],
];
$schema['vendor'] = [
'position' => 11000,
'items' => [
'truncate_short_description' => [
'type' => 'input',
'class' => 'input-small cm-value-integer',
'position' => 100,
'value' => [
'desktop' => 180,
'tablet' => 180,
'mobile' => 180,
],
],
'show_ask_question_link' => [
'type' => 'checkbox',
'position' => 200,
'value' => [
'desktop' => 'Y',
'tablet' => 'Y',
'mobile' => 'Y',
],
],
],
];
return $schema;
