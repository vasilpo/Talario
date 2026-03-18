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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'paypal_ipn') {
        if (!empty($_REQUEST['custom'])) {

            list($result, $order_ids, $data) = fn_pp_validate_ipn_payload($_REQUEST);

            if ($result == 'VERIFIED') {
                fn_define('ORDER_MANAGEMENT', true);
                foreach($order_ids as $order_id) {
                    fn_process_paypal_ipn($order_id, $data);
                    // unlock order after processing IPN
                    fn_pp_set_orders_lock($order_id, false);
                }
            }
        }
        exit;
    }
}
