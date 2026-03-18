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

use Tygh\Payments\Processors\Sberbank;

if (defined('PAYMENT_NOTIFICATION')) {

    $order_id = 0;
    if (!empty($_REQUEST['ordernumber'])) {
        $order_id = $_REQUEST['ordernumber'];
    }

    $order_info = fn_get_order_info($order_id);
    if (empty($processor_data) && !empty($order_info)) {
        $processor_data = fn_get_processor_data($order_info['payment_id']);
    }

    if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
        Sberbank::writeLog($_REQUEST, 'sberbank_request.log');
    }

    if (!empty($order_info) && ($mode == 'return' || $mode == 'error')) {

        $pp_response = array(
            'order_status' => 'F'
        );

        if ($order_info['payment_info']['transaction_id'] != $_REQUEST['orderId']) {
            $pp_response['reason_text'] = __("addons.rus_sberbank.wrong_transaction_id");

        } else {
            $sberbank = new Sberbank($processor_data);
            $response = $sberbank->getOrderExtended($order_info['payment_info']['transaction_id']);

            if ($sberbank->isError()) {
                $pp_response = array(
                    'order_status' => 'F',
                    'reason_text' => $response['errorMessage']
                );

            } elseif ($response['orderStatus'] == 2) {

                $pp_response = [
                    'order_status'    => 'P',
                    'card_number'     => $response['cardAuthInfo']['pan'],
                    'cardholder_name' => $response['cardAuthInfo']['cardholderName'],
                    'expiry_month'    => substr($response['cardAuthInfo']['expiration'], 0, 4),
                    'expiry_year'     => substr($response['cardAuthInfo']['expiration'], 0, -2),
                    'bank'            => $response['bankInfo']['bankName'],
                    'ip_address'      => $response['ip'],
                ];
            } else {
                $pp_response = array(
                    'order_status' => 'F',
                    'reason_text' => $response['actionCodeDescription'],
                    'ip_address' => $response['ip'],
                );
            }
        }

        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('route', $order_id, false);
    }

    exit;

} else {
    $sberbank = new Sberbank($processor_data);

    $response = $sberbank->register($order_info);

    if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
        Sberbank::writeLog($response, 'sberbank.log');
    }

    if (!$sberbank->isError()) {

        $pp_response = array(
            'transaction_id' => $response['orderId']
        );

        fn_update_order_payment_info($order_id, $pp_response);
        fn_create_payment_form($response['formUrl'], array(), 'Сбербанк Онлайн', true, 'GET');

    } else {
        $pp_response['order_status'] = 'F';
        $pp_response['reason_text'] = $sberbank->getErrorText();

        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('route', $order_id, false);
    }

}

