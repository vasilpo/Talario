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
$schema['main']['cache_overrides_by_dispatch']['categories.view']['update_handlers'][] = 'ab__stickers';
$schema['main']['cache_overrides_by_dispatch']['categories.view']['update_handlers'][] = 'ab__sticker_descriptions';
$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'ab__stickers';
$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'ab__sticker_descriptions';
$schema['main']['cache_overrides_by_dispatch']['products.quick_view']['update_handlers'][] = 'ab__stickers';
$schema['main']['cache_overrides_by_dispatch']['products.quick_view']['update_handlers'][] = 'ab__sticker_descriptions';
$schema['products']['cache']['update_handlers'][] = 'ab__stickers';
$schema['products']['cache']['update_handlers'][] = 'ab__sticker_descriptions';
return $schema;
