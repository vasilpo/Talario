<?php
require_once realpath(dirname(__FILE__) . '/Tygh/include.php');
if (!defined('BOOTSTRAP')) { die('Access denied'); }
function fn_alfabank_install()
{
fn_alfabank_uninstall();
$_data = array(
'processor' => 'Alfabank',
'processor_script' => 'alfabank.php',
'processor_template' => 'views/orders/components/payments/cc_outside.tpl',
'admin_template' => 'alfabank.tpl',
'callback' => 'Y',
'type' => 'P',
'addon' => 'alfabank'
);
db_query("INSERT INTO ?:payment_processors ?e", $_data);
}
function fn_alfabank_uninstall()
{
db_query("DELETE FROM ?:payment_processors WHERE processor_script = ?s", "alfabank.php");
}
function fn_alfabank_normalize_phone($phone)
{
$phone_normalize = '';
if (!empty($phone)) {
if (strpos('+', $phone) === false && $phone[0] == '8') {
$phone[0] = '7';
}
$phone_normalize = str_replace(array(' ', '(', ')', '-'), '', $phone);
}
return $phone_normalize;
}
function fn_alfabank_can_refund_order($order_info) {
if($order_info['payment_method']['processor'] == 'Alfabank'
&& !empty($order_info['payment_info']['gateway_status'])
&& ($order_info['payment_info']['gateway_status'] != "CREATED")
&& !empty($order_info['payment_info']['transaction_id'])
&& (defined('RBSPAYMENT_ENABLE_ACTION_BUTTON') && RBSPAYMENT_ENABLE_ACTION_BUTTON === true) //todo global DEFINED
&& TRUE //todo Add status check
) {
$out = true;
}
return $out;
}
/**
* Хук вызывается после сохранения настроек способа оплаты
*
* @param array $processor_data Данные способа оплаты, включая настройки
* @param int $payment_id ID способа оплаты
*/
function fn_alfabank_update_payment_post($processor_data, $payment_id)
{
if (!empty($processor_data['payment']) && $processor_data['payment'] === 'Alfabank') {
try {
$params = @unserialize($processor_data['processor_params']);
if (!is_array($params)) {
throw new \Exception('Failed to unserialize processor parameters.');
}
$login = $params['login'] ?? '';
$password = $params['password'] ?? '';
$mode = $params['mode'] ?? 'test'; // can be 'test' or 'live'
if (!$login || !$password) {
throw new \Exception('Login or password not set in Alfabank settings.');
}
$processor = new \Tygh\Payments\Processors\Alfabank($processor_data);
if ($processor->_logging) {
$processor->writeLog([
'payment_id' => $payment_id,
'params' => $params
], 'Alfabank settings saved');
}
$base_url = $processor->_url;
if ($mode == 'test') {
$gate_url = str_replace("payment/rest", "mportal/mvc/public/merchant/update", $base_url);
if (defined('RBSPAYMENT_TEST_URL_ALTERNATIVE_DOMAIN')) {
$pattern = '/^https:\/\/[^\/]+/';
$gate_url = preg_replace($pattern, rtrim(RBSPAYMENT_TEST_URL_ALTERNATIVE_DOMAIN, '/'), $gate_url);
}
} else {
$gate_url = str_replace("payment/rest", "mportal/mvc/public/merchant/update", $base_url);
if (defined('RBSPAYMENT_PROD_URL_ALTERNATIVE_DOMAIN')) {
$pattern = '/^https:\/\/[^\/]+/';
$gate_url = preg_replace($pattern, rtrim(RBSPAYMENT_PROD_URL_ALTERNATIVE_DOMAIN, '/'), $gate_url);
}
}
$gate_url .= substr($login, 0, -4);
$callback_url = fn_url("payment_notification.return?payment=alfabank&payment_id={$payment_id}&action=callback", 'C');
$res = $processor->_updateGatewayCallback($login, $password, $gate_url, $callback_url);
if ($processor->_logging) {
$processor->writeLog([
'url' => $gate_url,
'callback' => $callback_url,
'response' => $res
], 'Callback URL updated');
}
} catch (\Exception $e) {
$processor = new \Tygh\Payments\Processors\Alfabank($processor_data); // recreate to access logger
if ($processor->_logging) {
$processor->writeLog([
'error' => $e->getMessage()
], 'Error updating callback URL');
}
}
}
}
