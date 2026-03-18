<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

namespace Tygh\SmartyEngine\Filters\Output;

use Smarty\Filter\FilterInterface;
use Smarty\Template;
use Tygh\Embedded;
use Tygh\Registry;
use Tygh\Tools\Url;

class EmbeddedUrl implements FilterInterface
{
    /**
     * @param string   $code     Code
     * @param Template $template Template
     *
     * @return array|string|string[]|null
     */
    public function filter($code, Template $template)
    {
        $path = Registry::get('config.current_host') . Registry::get('config.current_path');

        // Transform 'href' attribute values of the 'a' elements, which:
        // - have 'href' attribute
        // - the 'href' value contains current path and host, or its a relative url
        // - do not have class attribute starting with 'cm-' prefix

        $pattern = '{'
            . '<(?:a)\s+'
            . '(?=[^>]*\bhref="([^"]*//' . $path . '[^"]*|(?!//)(?!https?)[^"]*)")'
            . '(?![^>]*\bclass="[^"]*cm-[^"]*")'
            . '[^>]*>'
            . '}Usi';

        $code = preg_replace_callback($pattern, static function ($matches) {
            return str_replace(
                $matches[1],
                Embedded::resolveUrl($matches[1]),
                $matches[0]
            );
        }, $code);

        // Transform relative 'src'attribute values

        $pattern = '{<[^>]+\bsrc="((?!//)(?!https?)[^"]+)"[^>]*>}Usi';

        $code = preg_replace_callback($pattern, static function ($matches) {
            return str_replace(
                $matches[1],
                Url::resolve($matches[1], Registry::get('config.current_location')),
                $matches[0]
            );
        }, $code);

        $area = \Tygh::$app['view']->getArea();

        if ($area[1] === 'mail') {
            // Transform URLs in the text

            $pattern = '{\bhttps?://' . $path . '[^\s<>"\']*(?=[^>]*<)}s';

            $code = preg_replace_callback($pattern, static function ($matches) {
                return Embedded::resolveUrl($matches[0]);
            }, $code);
        }

        return $code;
    }
}
