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

class IncludeExt extends Base
{
    /**
     * Includes template with ability to pass parameters as array.
     * Does not capture variables from global/parent scope unless passed explicitly.
     * phpcs:ignore
     * @param array    $params   Function args
     * @param Template $template Template
     *
     * @return string
     *
     * @throws \Smarty\Exception Exception.
     */
    //phpcs:ignore
    public function handle($params, Template $template)
    {
        /** @see Smarty::createTemplate() $tpl */
        $tpl = $template->getSmarty()->createTemplate($params['file']);
        $tpl->parent = null;
        unset($params['file']);

        $tpl->assign($params['params_array']);
        unset($params['params_array']);

        if (!empty($params)) {
            $tpl->assign($params);
        }

        $tpl->assign([
            'ldelim' => $template->getSmarty()->getLeftDelimiter(),
            'rdelim' => $template->getSmarty()->getRightDelimiter(),
        ]);

        $content = $tpl->fetch();

        if (!empty($params['assign'])) {
            $template->assign($params['assign'], $content);

            return '';
        }

        return $content;
    }
}
