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

class LiveEditorWrapper implements FilterInterface
{
    /**
     * @param string   $code     Code
     * @param Template $template Template
     *
     * @return array|string|string[]|null
     */
    public function filter($code, Template $template)
    {
        $pattern = '/\<(input|img|div)[^>]*?(\[lang name\=([\w\-\.]+?)\](.*?)\[\/lang\])[^>]*?\>/';
        if (preg_match_all($pattern, $code, $matches)) {
            foreach ($matches[0] as $k => $m) {
                $phrase_replaced = str_replace($matches[2][$k], $matches[5][$k] ?? '', $matches[0][$k]);
                $langvar = $matches[3][$k];
                $langvar_value = addslashes(htmlentities(__($langvar, ['skip_live_editor' => true])));

                if (strpos($m, 'class="') !== false) {
                    $class_added = str_replace(
                        'class="',
                        'data-ca-live-edit="langvar::' . $langvar . '"' .
                        ' data-ca-live-edit-original-value="' . $langvar_value . '"' .
                        ' class="cm-live-editor-need-wrap ',
                        $phrase_replaced
                    );
                } else {
                    $class_added = str_replace(
                        $matches[1][$k],
                        $matches[1][$k] .
                        ' data-ca-live-edit="langvar::' . $langvar . '"' .
                        ' data-ca-live-edit-original-value="' . $langvar_value . '"' .
                        ' class="cm-live-editor-need-wrap"',
                        $phrase_replaced
                    );
                }

                if ($matches[1][$k] === 'div') {
                    $code = str_replace($matches[0][$k], $phrase_replaced, $code);
                } else {
                    $code = str_replace($matches[0][$k], $class_added, $code);
                }
            }
        }

        $pattern = '/(\<(textarea|option)[^<]*?)\>(\[lang name\=([\w\-\.]+?)\](.*?)\[\/lang\])[^>]*?\>/is';
        if (preg_match_all($pattern, $code, $matches)) {
            foreach ($matches[0] as $k => $m) {
                $phrase_replaced = str_replace($matches[3][$k], $matches[6][$k], $matches[0][$k]);
                $langvar = $matches[4][$k];
                $langvar_value = addslashes(htmlentities(__($langvar, ['skip_live_editor' => true])));

                if (strpos($m, 'class="') !== false) {
                    $class_added = str_replace(
                        'class="',
                        'data-ca-live-edit="langvar::' . $langvar . '"' .
                        ' data-ca-live-edit-original-value="' . $langvar_value . '"' .
                        ' class="cm-live-editor-need-wrap ',
                        $phrase_replaced
                    );
                } else {
                    $class_added = str_replace(
                        '<' . $matches[2][$k],
                        '<' . $matches[2][$k] .
                        ' data-ca-live-edit="langvar::' . $langvar . '"' .
                        ' data-ca-live-edit-original-value="' . $langvar_value . '"' .
                        ' class="cm-live-editor-need-wrap"',
                        $phrase_replaced
                    );
                }
                $code = str_replace($matches[0][$k], $class_added, $code);
            }
        }

        $pattern = '/<title>(.*?)<\/title>/is';
        $pattern_inner = '/\[(lang) name\=([\w\-\.]+?)\](.*?)\[\/\1\]/is';
        preg_match($pattern, $code, $matches);
        $phrase_replaced = $matches[0];
        if ($phrase_replaced) {
            $phrase_replaced = preg_replace($pattern_inner, '$3', $phrase_replaced);
            $code = str_replace($matches[0], $phrase_replaced, $code);
        }
        // remove translation tags from elements attributes
        $pattern = '/(\<[^<>]*\=[^<>]*)(\[lang name\=([\w\-\.]+?)\](.*?)\[\/lang\])[^<>]*?\>/is';
        while (preg_match($pattern, $code, $matches)) {
            $phrase_replaced = preg_replace($pattern_inner, '$3', $matches[0]);
            $code = str_replace($matches[0], $phrase_replaced, $code);
        }

        $pattern = '/(?<=>)[^<]*?\[(lang) name\=([\w\-\.]+?)\](.*?)\[\/\1\]/is';
        $pattern_inner = '/\[(lang) name\=([\w\-\.]+?)\]((?:(?>[^\[]+)|\[(?!\1[^\]]*\]))*?)\[\/\1\]/is';
        while (preg_match($pattern, $code, $matches)) {
            $phrase_replaced = $matches[0];
            while (preg_match($pattern_inner, $phrase_replaced)) {
                $phrase_replaced = preg_replace_callback($pattern_inner, static function ($matches) {
                    $langvar = $matches[2];
                    $langvar_value = addslashes(htmlentities(__($langvar, ['skip_live_editor' => true])));

                    return '<var class="live-edit-wrap">' .
                        '<span class="cm-icon-live-edit icon-live-edit ty-icon-live-edit"></span>' .
                        '<var data-ca-live-edit="langvar::' . $langvar . '"' .
                        ' data-ca-live-edit-original-value="' . $langvar_value . '"' .
                        ' class="cm-live-edit live-edit-item"' .
                        '>' . $matches[3] . '</var>' .
                        '</var>';
                }, $phrase_replaced);
            }
            $code = str_replace($matches[0], $phrase_replaced, $code);
        }

        $pattern = '/\[(lang) name\=([\w\-\.]+?)\](.*?)\[\/\1\]/';
        $replacement = '$3';
        $code = preg_replace($pattern, $replacement, $code);
        return $code;
    }
}
