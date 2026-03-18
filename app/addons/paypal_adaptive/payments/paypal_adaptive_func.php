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

use Tygh\Http;
use Tygh\Registry;
use Tygh\Embedded;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_paypal_adaptive_next($order_ids)
{
    $parent_order_id = fn_paypal_get_parent_order_id($order_ids);

    if (fn_paypal_adaptive_check_finish($parent_order_id)) {

        $route = 'route';
        if (count($order_ids) == 1) {
            $order_info = fn_get_order_info(reset($order_ids));
            $route = $order_info['repaid'] ? 'repay' : 'route';
        }

        fn_paypal_clear_queue($parent_order_id);
        fn_order_placement_routines($route, $parent_order_id, false);

    } else {
        $action = "paypal_adaptive.queue?order_id=$parent_order_id";
        fn_redirect(fn_url($action));
    }
}

function fn_paypal_adaptive_start_payments($order_ids)
{
    $idata = array();
    foreach($order_ids as $order_id) {
        $idata[] = array(
            'order_id' => $order_id,
            'type' => 'S',
            'data' => TIME,
        );
    }

    db_query("REPLACE INTO ?:order_data ?m", $idata);
}

function fn_paypal_adaptive_check_payment_complete($payment_details, $order_index)
{
    return isset($payment_details["paymentInfoList_paymentInfo($order_index)_senderTransactionStatus"]) && $payment_details["paymentInfoList_paymentInfo($order_index)_senderTransactionStatus"] == 'COMPLETED';
}

function fn_paypal_adaptive_check_payment_pending($payment_details, $order_index)
{
    return isset($payment_details["paymentInfoList_paymentInfo($order_index)_senderTransactionStatus"]) && $payment_details["paymentInfoList_paymentInfo($order_index)_senderTransactionStatus"] == 'PENDING';
}
