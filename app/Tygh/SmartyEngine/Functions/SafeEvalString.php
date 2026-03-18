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

use Exception;
use Smarty\FunctionHandler\Base;
use Smarty\Template;
use Tygh\SmartyEngine\StrongSecurity;

class SafeEvalString extends Base
{
    /**
     * Evaluates string which contains smarty syntax and falls back to custom error message instead fatal error
     *
     * @param array{var: string} $params   Function args
     * @param Template           $template Template
     *
     * @return string
     */
    //phpcs:ignore
    public function handle($params, Template $template)
    {
        $smarty = $template->getSmarty();

        try {
            $security = new StrongSecurity($smarty);
            $smarty->enableSecurity($security);

            $contents = $smarty->fetch('string:' . $params['var']);
        } catch (Exception $e) {
            $contents = $e->getMessage();
        }

        $smarty->disableSecurity();

        return $contents;
    }
}
