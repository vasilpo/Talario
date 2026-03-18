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

class CartHookHandler
{
    /**
     * The `post_add_to_cart` hook handler.
     *
     * Action performed:
     *    - Deny unsetting product groups in the cart, at placing order moment.
     *
     * @param array<string>       $product_data Product data.
     * @param array<string, bool> $cart         Cart data.
     *
     * @see fn_add_product_to_cart()
     *
     * @return void
     */
    public function onPostAddToCart(array $product_data, array &$cart)
    {
        //phpcs:ignore
        if (!empty(Tygh::$app['session']['place_order'])) {
            $cart['deny_unsetting_product_group'] = true;
        }
    }

    /**
     * The `delete_cart_product` hook handler.
     *
     * Action performed:
     *    - Deny unsetting product groups in the cart, at placing order moment.
     *
     * @param array<string, bool> $cart Cart data.
     *
     * @see fn_delete_cart_product()
     *
     * @return void
     */
    public function onDeleteCartProduct(array &$cart)
    {
        //phpcs:ignore
        if (!empty(Tygh::$app['session']['place_order'])) {
            $cart['deny_unsetting_product_group'] = true;
        }
    }
}
