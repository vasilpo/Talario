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

class TemplateIds implements FilterInterface
{
    /**
     * @param string   $code     Code
     * @param Template $template Template
     *
     * phpcs:ignore
     * @return array|mixed|string|string[]|null
     */
    public function filter($code, Template $template)
    {
        $pattern = '/(\<head\>.*?)(\<span[^<>]*\>|\<\/span\>|\<img[^<>]*\>|\<!--[\w]*--\>)+?(.*?\<\/head\>)/is';
        while (preg_match($pattern, $code, $match)) {
            $code = str_replace($match[0], $match[1] . $match[3], $code);
        }
        $pattern = '/\<span[^<>]*\>|\<\/span\>|\<img[^<>]*\>|\<!--[\w]*--\>/is';
        $glob_pattern = '/\<script[^<>]*\>.*?\<\/script\>/is';
        if (preg_match_all($glob_pattern, $code, $matches)) {
            //phpcs:ignore
            foreach ($matches[0] as $k => $m) {
                $replace_script = preg_replace($pattern, '', $matches[0][$k]);
                $code = str_replace($matches[0][$k], $replace_script, $code);
            }
        }

        static $template_ids;

        if (!isset($template_ids)) {
            $template_ids = [];
        }

        $pattern = '/\[(tpl_id) ([^ ]*)\]((?:(?>[^\[]+)|\[(?!\1[^\]]*\]))*?)\[\/\1\]/is';
        while (preg_match($pattern, $code, $matches)) {
            $id = 'te' . md5($matches[2]);
            if (empty($template_ids[$matches[2]])) {
                $template_ids[$matches[2]] = 1;
            } else {
                $template_ids[$matches[2]]++;
                $id .= '_' . $template_ids[$matches[2]];
            }
            $code = preg_replace($pattern, $id . '${3}' . $id, $code, 1);
        }

        return $code;
    }
}
