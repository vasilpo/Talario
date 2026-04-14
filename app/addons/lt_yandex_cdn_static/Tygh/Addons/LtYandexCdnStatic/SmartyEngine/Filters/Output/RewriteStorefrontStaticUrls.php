<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

namespace Tygh\Addons\LtYandexCdnStatic\SmartyEngine\Filters\Output;

use Smarty\Filter\FilterInterface;
use Smarty\Template;
use Tygh\Registry;

class RewriteStorefrontStaticUrls implements FilterInterface
{
    /**
     * @param string   $code     Code
     * @param Template $template Template
     *
     * @return string
     */
    public function filter($code, Template $template): string
    {
        $cdn_base_url = \fn_lt_yandex_cdn_static_get_storefront_cdn_base_url();

        if ($cdn_base_url === '') {
            return $code;
        }

        $origin_base_url = rtrim((string) Registry::get('config.current_location'), '/');
        $files_relative_path = trim((string) \fn_get_rel_dir(Registry::get('config.dir.files')), '/');

        if ($origin_base_url === '' || $files_relative_path === '') {
            return $code;
        }

        $replacements = [
            $origin_base_url . '/js/'                 => $cdn_base_url . '/js/',
            $origin_base_url . '/design/themes/'      => $cdn_base_url . '/design/themes/',
            $origin_base_url . '/' . $files_relative_path . '/' => $cdn_base_url . '/' . $files_relative_path . '/',
        ];

        return strtr($code, $replacements);
    }
}
