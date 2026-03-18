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

namespace Tygh\SmartyEngine\Modifiers;

use Smarty\Compile\Modifier\Base;
use Smarty\Compiler\Template;

class Translate extends Base
{
    /**
     * phpcs:ignore
     * @param array    $params   Params
     * @param Template $compiler Compiler
     *
     * @return string
     */
    //phpcs:ignore
    public function compile($params, Template $compiler)
    {
        $var = $params[0];
        $_params = $params[1] ?? '[]';
        $lang_code = $params[2] ?? '$_smarty_tpl->getSmarty()->getLanguage()';

        $compiler->setRawOutput(true);

        /* @see \Tygh\SmartyEngine\Filters\Post\Translation::filter */
        return '$_smarty_tpl->getSmarty()->getModifierCallback("__")' . "({$var}, {$_params}, {$lang_code})";
    }
}
