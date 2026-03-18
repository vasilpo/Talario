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
namespace Tygh\Addons\Ab_videoGallery\SmartyEngine\Filters\Pre;
use Smarty\Filter\FilterInterface;
use Smarty\Template;
class AddStorefrontHooks implements FilterInterface
{
public function filter($code, Template $template)
{
if (strpos($template->template_resource, 'views/products/components/product_icon.tpl') !== false) {
$code = preg_replace('/\{\s*if\s*\$product\.image_pairs/i', '{hook name="ab__vg:product_main_icon"}{/hook}' . PHP_EOL . '$0', $code);
$code = preg_replace('/\{\s*if\s*\$product\.main_pair\s*}/i', '{hook name="ab__vg:product_main_pair"}{/hook}' . PHP_EOL . '$0', $code);
}
return $code;
}
}
