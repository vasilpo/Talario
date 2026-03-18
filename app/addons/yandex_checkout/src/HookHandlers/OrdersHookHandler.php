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

namespace Tygh\Addons\YandexCheckout\HookHandlers;

use Tygh\Addons\YandexCheckout\ServiceProvider;

class OrdersHookHandler
{
    /**
     * The `change_order_status_post` hook handler.
     *
     * Actions performed:
     *     - Creates full payment receipt for YooKassa
     *     - Creates full pre-payment and full payment receipts for YooKassa for Marketplaces
     *     - Creates withdrawals for YooKassa for Marketplaces
     *
     * @param int                                             $order_id           Order identifier
     * @param string                                          $status_to          New order status (one char)
     * @param string                                          $status_from        Old order status (one char)
     * @param array<string, bool>                             $force_notification Array with notification rules
     * @param bool                                            $place_order        True, if this function has been called inside of fn_place_order function
     * @param array<string, int|array<string, array<string>>> $order_info         Order information
     * @param array<string>                                   $edp_data           Downloadable products data
     *
     * @see \fn_change_order_status()
     *
     * @return void
     */
    public function onChangeOrderStatusPost(
        $order_id,
        $status_to,
        $status_from,
        array $force_notification,
        $place_order,
        array $order_info,
        array $edp_data
    ) {
        $order_service = ServiceProvider::getOrderService();
        $order_service->performOperationsConnectedToOrderStatusChange($order_info, $status_to);
    }
}
