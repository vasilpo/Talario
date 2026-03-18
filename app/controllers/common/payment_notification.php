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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'process_embedded') {
        if (!empty($_REQUEST['data'])) {
            $data = json_decode($_REQUEST['data'], true);
            if (!empty($data)) {
                fn_create_payment_form($data['submit_url'], $data['data'], $data['payment_name'], $data['exclude_empty_values'], $data['method']);
            }
        }
        exit;
    }
}

if (!empty($_REQUEST['payment'])) {
    define('PAYMENT_NOTIFICATION', true);

    $payment = fn_basename($_REQUEST['payment']);

    if (fn_check_prosessor_status($payment)) {
        $payment_script = fn_get_processor_script_path($payment . '.php');

        if (in_array($mode, array('checkout_redirect', 'index_redirect'))) {
            fn_order_placement_routines($mode);
        } elseif (is_file($payment_script)) {
            include($payment_script);
        }
    }
}
