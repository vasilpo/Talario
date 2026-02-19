<?php

use Tygh\Payments\Processors\Alfabank;
use Tygh\Tygh;

$processor_data = $processor_data ?? [];
$order_info = $order_info ?? [];
$order_id = isset($order_id) ? (int) $order_id : 0;
$mode = $mode ?? '';

if (defined('PAYMENT_NOTIFICATION')) {
    if (
        isset($_REQUEST['action'])
        && $_REQUEST['action'] === 'callback'
    ) {
        $order_id = 0;
        $gateway_id = $_REQUEST['orderId'] ?? ($_REQUEST['mdOrder'] ?? null);
        if (empty($processor_data)) {
            $payment_id = isset($_REQUEST['payment_id']) ? (int) $_REQUEST['payment_id'] : 0;
            if ($payment_id > 0) {
                $processor_data = fn_get_processor_data($payment_id);
            }
        }
        $PaymentProcessor = new Alfabank($processor_data);
        $response = $PaymentProcessor->getOrderExtended($gateway_id);
        $order_id = (int) explode("_", $response['orderNumber'])[0];
        $order_info = fn_get_order_info($order_id);
        if ($order_info['payment_info']['transaction_id'] != $gateway_id) {
            exit;
        }
        if ($PaymentProcessor->_logging) {
            $PaymentProcessor->writeLog($response, 'Callback response received');
        }
        if ($PaymentProcessor->isError()) {
            $pp_response['reason_text'] = $response['errorMessage'];
        } elseif ($response['orderStatus'] == 1 || $response['orderStatus'] == 2) {
            $pp_response = array(
                'order_status' => $processor_data['processor_params']['confirmed_order_status'],
                'gateway_status' => $response['paymentAmountInfo']['paymentState'],
                'gateway_approved' => $response['paymentAmountInfo']['approvedAmount'] / 100,
                'gateway_deposited' => $response['paymentAmountInfo']['depositedAmount'] / 100,
                'gateway_refunded' => $response['paymentAmountInfo']['refundedAmount'] / 100,
            );
        } elseif ($response['orderStatus'] == 4) {
            $is_part_refunded = $response['paymentAmountInfo']['approvedAmount'] === $response['amount']
                && $response['paymentAmountInfo']['refundedAmount'] != 0;
            $is_full_refunded = $response['paymentAmountInfo']['approvedAmount']
                === $response['paymentAmountInfo']['refundedAmount'];
            if ($is_full_refunded) {
                $refund_amount = $response['amount'] / 100;
                $refund_massage = 'REFUNDED_FULL_MESSAGE ' . $refund_amount;
            } elseif ($is_part_refunded) {
                $refund_amount = $response['paymentAmountInfo']['refundedAmount'] / 100;
                $refund_massage = 'REFUNDED_MESSAGE ' . $refund_amount;
            }
            $pp_response = array(
                'gateway_status' => $response['paymentAmountInfo']['paymentState'],
                'gateway_approved' => $response['paymentAmountInfo']['approvedAmount'] / 100,
                'gateway_deposited' => $response['paymentAmountInfo']['depositedAmount'] / 100,
                'gateway_refunded' => $response['paymentAmountInfo']['refundedAmount'] / 100,
            );
            fn_update_order_payment_info($order_id, $pp_response);
        } elseif ($response['orderStatus'] == 3) {
            $is_part_cancel = $response['paymentAmountInfo']['approvedAmount'] > 0
                && $response['paymentAmountInfo']['approvedAmount'] < $response['amount'];
            $is_full_cancel = $response['paymentAmountInfo']['approvedAmount'] === 0;
            if ($is_full_cancel) {
                $cancel_amount = '';
                $cancel_massage = 'CANCEL_FULL_MESSAGE ' . $cancel_amount;
            } elseif ($is_part_cancel) {
                $cancel_amount = $response['amount'] - $response['paymentAmountInfo']['approvedAmount'];
                $cancel_massage = 'CANCEL_MESSAGE ' . ($cancel_amount / 100);
            }
            $pp_response = array(
                'gateway_status' => $response['paymentAmountInfo']['paymentState'],
                'gateway_approved' => $response['paymentAmountInfo']['approvedAmount'] / 100,
                'gateway_deposited' => $response['paymentAmountInfo']['depositedAmount'] / 100,
                'gateway_refunded' => $response['paymentAmountInfo']['refundedAmount'] / 100,
            );
            fn_update_order_payment_info($order_id, $pp_response);
        } else {
            $pp_response = array(
                'order_status' => 'F',
                'reason_text' => $response['actionCodeDescription'],
                'ip_address' => $response['ip'],
            );
        }
        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('save', $order_id, false);
        exit;
    }
    if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'return') {
        $order_id = 0;
        if (!empty($_REQUEST['ordernumber'])) {
            $order_id = (int) $_REQUEST['ordernumber'];
        }
        $order_info = fn_get_order_info($order_id);
        if (empty($processor_data) && !empty($order_info)) {
            $processor_data = fn_get_processor_data($order_info['payment_id']);
        }
        if (!empty($order_info)) {
            $pp_response = array(
                'order_status' => 'F'
            );
            if ($order_info['payment_info']['transaction_id'] != $_REQUEST['orderId']) {
                $pp_response['reason_text'] = __("addons.alfabank.wrong_transaction_id");
            } else {
                $PaymentProcessor = new Alfabank($processor_data);
                $response = $PaymentProcessor->getOrderExtended($order_info['payment_info']['transaction_id']);
                if ($PaymentProcessor->_logging) {
                    $PaymentProcessor->writeLog($response, 'Return response');
                }
                if ($PaymentProcessor->isError()) {
                    $pp_response['reason_text'] = $response['errorMessage'];
                } elseif ($response['orderStatus'] == 1 || $response['orderStatus'] == 2) {
                    $pp_response = array(
                        'order_status' => $processor_data['processor_params']['confirmed_order_status'],
                        'gateway_amount' => $response['paymentAmountInfo']['totalAmount'],
                        'gateway_status' => $response['paymentAmountInfo']['paymentState'],
                    );
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
    }
} else {
    $PaymentProcessor = new Alfabank($processor_data);
    $response = $PaymentProcessor->register($order_info);
    if (!$PaymentProcessor->isError()) {
        $pp_response = array(
            'transaction_id' => $response['orderId']
        );
        fn_update_order_payment_info($order_id, $pp_response);
        fn_clear_cart(Tygh::$app['session']['cart']);
        fn_create_payment_form($response['formUrl'], array(), '', true, 'GET');
    } else {
        $pp_response['order_status'] = 'F';
        $pp_response['reason_text'] = $PaymentProcessor->getErrorText();
        fn_finish_payment($order_id, $pp_response);
        fn_order_placement_routines('route', $order_id, false);
    }
}
