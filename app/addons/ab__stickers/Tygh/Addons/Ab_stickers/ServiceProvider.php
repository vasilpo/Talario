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
namespace Tygh\Addons\Ab_stickers;
use Exception;
use Imagine\Image\ImagineInterface;
use Imagine\Imagick\Imagine;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use stdClass;
use Tygh\Registry;

class ServiceProvider implements ServiceProviderInterface
{

public function register(Container $app)
{
$settings = Registry::get('addons.ab__stickers');

$app['addons.ab__stickers.repository'] = function (Container $app) use ($settings) {
return new StickersRepository($settings, $app['db'], $app['storefront'], $app['session']['auth'], AREA);
};

$app['addons.ab__stickers.vendors_repository'] = function (Container $app) use ($settings) {
return new VendorsStickersRepository($settings, $app['db'], $app['storefront'], $app['session']['auth'], AREA);
};

$app['addons.ab__stickers.image'] = function () {
$driver = 'auto';
if ($driver === 'auto') {
try {
return new Imagine();
} catch (Exception $e) {
try {
return new \Tygh\Addons\Ab_stickers\Imagine\Gd\Imagine();
} catch (Exception $e) {
return new stdClass();
}
}
} else {
switch ($driver) {
case 'gd':
return new \Tygh\Addons\Ab_stickers\Imagine\Gd\Imagine();
case 'imagick':
return new Imagine();
}
}
return new stdClass();
};
}
}
