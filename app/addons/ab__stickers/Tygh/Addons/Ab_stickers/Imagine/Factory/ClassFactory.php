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
namespace Tygh\Addons\Ab_stickers\Imagine\Factory;
use Imagine\Exception\RuntimeException;
use Imagine\Image\Palette\Color\ColorInterface;
use Tygh\Addons\Ab_stickers\Imagine\Gd\Font;

class ClassFactory extends \Imagine\Factory\ClassFactory
{

const AB__STICKERS_HANDLE_GD = 'gd';

public function createFont($handle, $file, $size, ColorInterface $color)
{
switch ($handle) {
case self::AB__STICKERS_HANDLE_GD:
$gdInfo = static::getGDInfo();
if (!$gdInfo['FreeType Support']) {
throw new RuntimeException('GD is not compiled with FreeType support');
}
return $this->finalize(new Font($file, $size, $color));
default:
return parent::createFont($handle, $file, $size, $color);
}
}
}
