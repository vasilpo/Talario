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

class CheckoutHookHandler
{
    /**
     * The `checkout_place_orders_pre_route` hook handler.
     *
     * Action performed:
     *     - Removes specified parameter for session for blocking creation the temporary product group.
     *
     * @param array<string> $cart   Cart information.
     * @param array<string> $auth   Authentication data.
     * @param array<string> $params Request parameters.
     *
     * @see \fn_checkout_place_order()
     *
     * @param-out array<array-key, mixed> $cart
     *
     * @return void
     */
    public function onCheckoutPlaceOrdersPreRoute(array &$cart, array $auth, array $params)
    {
        if (!isset(Tygh::$app['session']['place_order'])) {
            return;
        }

        unset(Tygh::$app['session']['place_order']);
        $cart['calculate_shipping'] = true;
    }
}
