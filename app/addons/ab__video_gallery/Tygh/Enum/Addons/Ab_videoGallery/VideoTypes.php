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
namespace Tygh\Enum\Addons\Ab_videoGallery;

class VideoTypes
{

const YOUTUBE = 'Y';

const VIMEO = 'V';

const HREF = 'H';

const RESOURCE = 'R';

public static function getList(array $exclude = [])
{
$types = [
self::YOUTUBE => self::YOUTUBE,
self::VIMEO => self::VIMEO,
self::HREF => self::HREF,
self::RESOURCE => self::RESOURCE,
];
fn_set_hook('ab__video_gallery_get_video_types', $types);
foreach ($exclude as $_tmp) {
unset($types[$_tmp]);
}
return $types;
}

public static function getAll()
{
return self::getList();
}
}
