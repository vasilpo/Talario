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

use Tygh\Addons\Tinkoff\Payments\EACQClient;
use Tygh\Enum\NotificationSeverity;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /** @var string $mode */
    if (
        $mode === 'transfer_funds'
        && !empty($_REQUEST['order_id'])
    ) {
        $order_info = fn_get_order_info($_REQUEST['order_id']);
        if (empty($order_info)) {
            return [CONTROLLER_STATUS_NO_CONTENT];
        }

        $client = new EACQClient(
            $order_info['payment_method']['processor_params']['terminal_key'],
            $order_info['payment_method']['processor_params']['password'],
            Tygh::$app['addons.rus_taxes.receipt_factory']
        );

        $response = $client->confirm($order_info);
        if (empty($response['Success'])) {
            $client->handleError($response);
        } else {
            fn_set_notification(NotificationSeverity::NOTICE, __('notice'), __('addons.tinkoff.funds_were_transferred'));
            fn_update_order_payment_info(
                $order_info['order_id'],
                [
                    'addons.tinkoff.payment_status' => $response['Status'],
                    'addons.tinkoff.funds_were_transferred' => __('yes')
                ]
            );
        }

        if (isset($_REQUEST['redirect_url'])) {
            return [CONTROLLER_STATUS_REDIRECT, $_REQUEST['redirect_url']];
        }
        return [CONTROLLER_STATUS_REDIRECT, 'orders.details?order_id=' . $order_info['order_id']];
    }

    return [CONTROLLER_STATUS_OK];
}
