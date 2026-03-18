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
namespace Tygh\Addons\Abt_unitheme2\SmartyEngine\Filters\Pre;
use Smarty\Filter\FilterInterface;
use Smarty\Template;
class AddHook implements FilterInterface
{
public function filter($code, Template $template)
{
if (strpos($template->template_resource, 'views/product_filters/update.tpl') !== false) {
$code = preg_replace('/(<\/fieldset>(.+)<div class="hidden" id="content_tab_categories)/sU', '{hook name="abt__ut2:update_filters"}{/hook}' . PHP_EOL . '$1', $code);
} elseif (strpos($template->template_resource, 'addons/banners/views/banners/components/banners_search_form.tpl') !== false) {
$code = str_replace('{/capture}', '{hook name="abt__ut2:banners_search_form"}{/hook}' . PHP_EOL . '{/capture}', $code);
}
return $code;
}
}
