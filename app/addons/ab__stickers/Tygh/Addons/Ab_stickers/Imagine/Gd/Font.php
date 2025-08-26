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
namespace Tygh\Addons\Ab_stickers\Imagine\Gd;
use Imagine\Driver\InfoProvider;
use Imagine\Gd\DriverInfo;
use Imagine\Image\AbstractFont;
use Tygh\Addons\Ab_stickers\Imagine\Factory\ClassFactory;

final class Font extends AbstractFont implements InfoProvider
{

private $classFactory;

public static function getDriverInfo($required = true)
{
return DriverInfo::get($required);
}

public function box($string, $angle = 0, $params = [])
{
self::getDriverInfo()->requireFeature(DriverInfo::FEATURE_TEXTFUNCTIONS);
$fontfile = $this->file;
if ($fontfile && DIRECTORY_SEPARATOR === '\\') {
$fontfileRealpath = realpath($fontfile);
if ($fontfileRealpath !== false) {
$fontfile = $fontfileRealpath;
}
}
$angle = -1 * $angle;
$info = imageftbbox($this->size, $angle, $fontfile, $string);
$xs = [$info[0], $info[2], $info[4], $info[6]];
$ys = [$info[1], $info[3], $info[5], $info[7]];
$width = abs(max($xs) - min($xs));
$height = abs(max($ys) - min($ys));
$height += round(($height * 15 / 100) + ($params['stroke_width'] ?? 0));
$width += round(($this->size * 20 / 100) + ($params['stroke_width'] ?? 0));
return $this->getClassFactory()->createBox($width, $height);
}

public function getClassFactory()
{
if ($this->classFactory === null) {
$this->classFactory = new ClassFactory();
}
return $this->classFactory;
}
}
