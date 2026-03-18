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
use Tygh\Registry;

class Script implements FilterInterface
{
    /**
     * @param string   $code     Code
     * @param Template $template Template
     *
     * phpcs:ignore
     * @return array|mixed|string|string[]
     */
    public function filter($code, Template $template)
    {
        if (defined('AJAX_REQUEST')) {
            return $code;
        }

        if ($template->getTemplateVars('block_rendering')) {
            if (!$template->getTemplateVars('block_parse_js')) {
                return $code;
            }
        }

        $pattern = '/\<script([^>]*)\>.*?\<\/script\>/s';
        if (preg_match_all($pattern, $code, $matches)) {
            if (Registry::get('runtime.inside_scripts')) {
                return $code;
            }

            $cache_name = $template->getTemplateVars('block_cache_name');

            $m = $matches[0];
            $m_attrs = $matches[1];

            $javascript = '';

            foreach ($m as $index => $match) {
                if (strpos($m_attrs[$index], 'data-no-defer') === false) {
                    $repeat = false;
                    smarty_block_inline_script([], $match, $repeat);

                    $code = str_replace($match, '<!-- Inline script moved to the bottom of the page -->', $code);
                    $javascript .= $match;
                }
            }

            if (!empty($cache_name)) {
                $cached_content = Registry::get($cache_name);
                if (!isset($cached_content['javascript'])) {
                    $cached_content['javascript'] = '';
                }
                $cached_content['javascript'] .= $javascript;

                Registry::set($cache_name, $cached_content, true);
            }
        }

        return $code;
    }
}
