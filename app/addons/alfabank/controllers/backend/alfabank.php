<?php
use Tygh\Registry;
/**
* @copyright 2023 idabi.dev
*/
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', '1');
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$order_id = $_REQUEST['order_id'];
$order_info = fn_get_order_info($order_id);
$processor_data = fn_get_processor_data($order_info['payment_id']);
$PaymentProcessor = new Tygh\Payments\Processors\Alfabank($processor_data);
if ($mode == 'checkGatewayState') {
$response = $PaymentProcessor->getOrderExtended($order_info['payment_info']['transaction_id']);
Registry::get('view');
Registry::get('ajax')->assign('result', $response['paymentAmountInfo']['paymentState']);
exit();
}
if (!empty($processor_data['processor_params']['token'])) {
$decoded_credentials = base64_decode($processor_data['processor_params']['token']);
list($l, $p) = explode(':', $decoded_credentials);
$parameters['userName'] = $l;
$parameters['password'] = $p;
} else {
$parameters['userName'] = $processor_data['processor_params']['login'];
$parameters['password'] = $processor_data['processor_params']['password'];
}
if ($mode == 'payment_refund') {
$amount = $_REQUEST['amount'];
$parameters['orderId'] = trim($_REQUEST['order_id']);
$parameters['orderId'] = $order_info['payment_info']['transaction_id'];
$parameters['amount'] = 0;
$msg = __("addons.alfabank.success_full_refund_message");
if ($action == "partial") {
$parameters['amount'] = ceil($amount * 100);
$msg = __("addons.alfabank.success_refund_message") . " " . $amount;
}
$response = $PaymentProcessor->_sendGatewayData($parameters, $PaymentProcessor->_url . "refund.do");
$response = json_decode($response, true);
if ($response['errorMessage'] != "Success") {
fn_set_notification('E', fn_get_lang_var('error'), $response['errorMessage']);
} else {
fn_set_notification('N', fn_get_lang_var('notice'), $msg);
}
$gose = $PaymentProcessor->getOrderExtended($order_info['payment_info']['transaction_id']);
$pp_response = array(
'gateway_status' => $gose['paymentAmountInfo']['paymentState'],
'gateway_approved' => $gose['paymentAmountInfo']['approvedAmount'] / 100,
'gateway_deposited' => $gose['paymentAmountInfo']['depositedAmount'] / 100,
'gateway_refunded' => $gose['paymentAmountInfo']['refundedAmount'] / 100,
);
fn_update_order_payment_info($order_id, $pp_response);
return array(CONTROLLER_STATUS_OK, 'orders.details?order_id=' . $order_id);
}
if ($mode == 'payment_deposit') {
$amount = $_REQUEST['amount'];
$parameters['orderId'] = trim($_REQUEST['order_id']);
$parameters['orderId'] = $order_info['payment_info']['transaction_id'];
$parameters['amount'] = 0;
$msg = __("addons.alfabank.success_full_deposit_message");
if ($action == "partial") {
$parameters['amount'] = ceil($amount * 100);
$msg = __("addons.alfabank.success_deposit_message") . " " . $amount;
}
$response = $PaymentProcessor->_sendGatewayData($parameters, $PaymentProcessor->_url . "deposit.do");
$response = json_decode($response, true);
if ($response['errorMessage'] != "Success") {
fn_set_notification('E', fn_get_lang_var('error'), $response['errorMessage']);
} else {
fn_set_notification('N', fn_get_lang_var('notice'), $msg);
}
$gose = $PaymentProcessor->getOrderExtended($order_info['payment_info']['transaction_id']);
$pp_response = array(
'gateway_status' => $gose['paymentAmountInfo']['paymentState'],
'gateway_approved' => $gose['paymentAmountInfo']['approvedAmount'] / 100,
'gateway_deposited' => $gose['paymentAmountInfo']['depositedAmount'] / 100,
'gateway_refunded' => $gose['paymentAmountInfo']['refundedAmount'] / 100,
);
fn_update_order_payment_info($order_id, $pp_response);
return array(CONTROLLER_STATUS_OK, 'orders.details?order_id=' . $order_id);
}
if ($mode == 'payment_reverse') {
$parameters['orderId'] = trim($_REQUEST['order_id']);
$parameters['orderId'] = $order_info['payment_info']['transaction_id'];
$response = $PaymentProcessor->_sendGatewayData($parameters, $PaymentProcessor->_url . "reverse.do");
$response = json_decode($response, true);
$msg = __("addons.alfabank.success_reverse_message");
if ($response['errorMessage'] != "Success") {
fn_set_notification('E', fn_get_lang_var('error'), "(" . $response['errorCode'] . ")" . $response['errorMessage']);
} else {
fn_set_notification('N', fn_get_lang_var('notice'), $msg);
}
$gose = $PaymentProcessor->getOrderExtended($order_info['payment_info']['transaction_id']);
$pp_response = array(
'gateway_status' => $gose['paymentAmountInfo']['paymentState'],
'gateway_approved' => $gose['paymentAmountInfo']['approvedAmount'] / 100,
'gateway_deposited' => $gose['paymentAmountInfo']['depositedAmount'] / 100,
'gateway_refunded' => $gose['paymentAmountInfo']['refundedAmount'] / 100,
);
fn_update_order_payment_info($order_id, $pp_response);
return array(CONTROLLER_STATUS_OK, 'orders.details?order_id=' . $order_id);
}
if ($mode == 'doAction') {
$order_id = $_REQUEST['order_id'];
$amount = $_REQUEST['amount'] * 100;
$action = $_REQUEST['action'];
Registry::get('ajax')->assign('action', $action);
$order_info = fn_get_order_info($order_id);
$processor_data = fn_get_processor_data($order_info['payment_id']);
$PaymentProcessor = new Tygh\Payments\Processors\Alfabank($processor_data);
$parameters['orderId'] = trim($_REQUEST['order_id']);
$parameters['orderId'] = $order_info['payment_info']['transaction_id'];
if ($action == "check_gateway_status") {
$gose = $PaymentProcessor->getOrderExtended($order_info['payment_info']['transaction_id']);
$pp_response = array(
'gateway_status' => $gose['paymentAmountInfo']['paymentState'],
'gateway_approved' => $gose['paymentAmountInfo']['approvedAmount'] / 100,
'gateway_deposited' => $gose['paymentAmountInfo']['depositedAmount'] / 100,
'gateway_refunded' => $gose['paymentAmountInfo']['refundedAmount'] / 100,
);
fn_update_order_payment_info($order_id, $pp_response);
Registry::get('ajax')->assign('result', $gose['paymentAmountInfo']['paymentState']);
Registry::get('ajax')->assign('message', "");
exit();
}
}
if ($mode == 'payment_management') {
$order_id = $_REQUEST['order_id'];
$order_info = fn_get_order_info($order_id);
return array(CONTROLLER_STATUS_OK, 'orders.details?order_id=' . $order_id);
}
} else {
if ($mode == 'payment_management') {
$order_id = $_REQUEST['order_id'];
$order_info = fn_get_order_info($order_id);
$gateway_order_id = $order_info['payment_info']['transaction_id'];
$gateway_order_status = !empty($order_info['payment_info']['gateway_status']) ? $order_info['payment_info']['gateway_status'] : "CREATED";
$view = Tygh::$app['view'];
$view->assign('order_info', $order_info);
$view->assign('order_id', $order_id);
$view->assign('gateway_order_id', $gateway_order_id);
$view->assign('gateway_order_status', $gateway_order_status);
}
}
