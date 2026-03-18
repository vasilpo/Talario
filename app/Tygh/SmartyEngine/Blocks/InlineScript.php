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

namespace Tygh\SmartyEngine\Blocks;

use Smarty\BlockHandler\BlockHandlerInterface;
use Smarty\Template;

class InlineScript implements BlockHandlerInterface
{
    /**
     * phpcs:ignore
     * @param array    $params   Params
     * @param string   $content  Content
     * @param Template $template Template
     * @param bool     $repeat   Repeat
     *
     * phpcs:ignore
     * @return mixed|string|null
     */
    //phpcs:ignore
    public function handle($params, $content, Template $template, &$repeat)
    {
        return smarty_block_inline_script($params, $content, $repeat);
    }

    /**
     * @return true
     */
    public function isCacheable(): bool
    {
        return true;
    }
}
