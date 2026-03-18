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

namespace Tygh\SmartyEngine\Functions;

use Smarty\FunctionHandler\Base;
use Smarty\Template;
use Tygh\Tools\SecurityHelper;

class ArrayToFields extends Base
{
    /**
     * Converts array to html hidden fields
     * phpcs:ignore
     * @param array    $params   Function args
     * @param Template $template Template
     *
     * @return string
     */
    //phpcs:ignore
    public function handle($params, Template $template)
    {
        $result = '';
        $pattern = '<input type="hidden" name="%s" value="%s" />' . "\n";
        foreach ($params['data'] as $name => $value) {
            if (empty($value)) {
                continue;
            }

            if (!empty($params['skip']) && in_array($name, $params['skip'])) {
                continue;
            }

            if (
                !empty($params['escape']) && in_array($name, $params['escape'])
                || !empty($params['escape_all'])
            ) {
                if (is_array($value)) {
                    foreach ($value as $index => &$data) {
                        $data = SecurityHelper::escapeHtml($data);
                    }
                } else {
                    $value = SecurityHelper::escapeHtml($value);
                }
            }

            if (is_array($value)) {
                foreach ($value as $index => $data) {
                    $result .= sprintf($pattern, $name . '[' . $index . ']', $data);
                }
            } else {
                $result .= sprintf($pattern, $name, $value);
            }
        }

        return $result;
    }
}
