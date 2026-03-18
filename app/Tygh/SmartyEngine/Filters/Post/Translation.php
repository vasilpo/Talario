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

namespace Tygh\SmartyEngine\Filters\Post;

use Smarty\Filter\FilterInterface;
use Smarty\Template;

class Translation implements FilterInterface
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
        /* @see \Tygh\SmartyEngine\Modifiers\Translate::compile */
        if (preg_match_all('/getModifierCallback\(\"__\"\)\(\"([\w\.]*?)\"/i', $code, $matches)) {
            return "<?php\n\Tygh\Languages\Helper::preloadLangVars(array('" . implode("','", $matches[1]) . "'));\n?>\n" . $code;
        }

        return $code;
    }
}
