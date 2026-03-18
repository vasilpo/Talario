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

class Notes implements BlockHandlerInterface
{
    /**
     * phpcs:ignore
     * @param array    $params   Params
     * @param string   $content  Content
     * @param Template $template Template
     * @param bool     $repeat   Repeat
     *
     * @return void
     */
    //phpcs:ignore
    public function handle($params, $content, Template $template, &$repeat)
    {
        static $notes = [];

        if (!empty($params['assign'])) {
            $template->assign($params['assign'], $notes, false);
        } elseif (!empty($params['clear'])) {
            $notes = [];
        } else {
            $key = empty($params['title']) ? '_note_' : $params['title'];

            if (!empty($params['unique']) && !empty($notes[$key])) {
                return;
            }

            if (!isset($notes[$key])) {
                $notes[$key] = '';
            }
            $notes[$key] .= $content;
        }
    }

    /**
     * @return true
     */
    public function isCacheable(): bool
    {
        return true;
    }
}
