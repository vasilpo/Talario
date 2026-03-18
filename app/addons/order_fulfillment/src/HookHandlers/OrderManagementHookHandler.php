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

namespace Tygh\Addons\OrderFulfillment\HookHandlers;

use Tygh\Tygh;

class OrderManagementHookHandler
{
    /**
     * The 'place_order_manually_post' hook handler.
     *
     * Action performed:
     *    - Removes specified parameter for session for blocking creation the temporary product group.
     *
     * @see \fn_place_order_manually()
     *
     * @return void
     */
    public function onPlaceOrderManuallyPost()
    {
        if (!isset(Tygh::$app['session']['place_order'])) {
            return;
        }

        unset(Tygh::$app['session']['place_order']);
    }
}
