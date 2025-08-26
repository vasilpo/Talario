<?php
namespace Tygh\Payments\Processors;
use Tygh\Registry;
use Tygh\Http;
use Tygh\Payments\AlfabankDiscount;
require_once realpath(dirname(__FILE__) . '/../../include.php');
if (!defined('RBSPAYMENT_ENABLE_CART_OPTIONS')) {
define('RBSPAYMENT_ENABLE_CART_OPTIONS', false);
}
if (!defined('RBSPAYMENT_ENABLE_BACK_URL_SETTINGS')) {
define('RBSPAYMENT_ENABLE_BACK_URL_SETTINGS', false);
}
if (!defined('RBSPAYMENT_API_VERSION')) {
define('RBSPAYMENT_API_VERSION', 1);
}
class Alfabank
{
const ENABLE_CALLBACK = RBSPAYMENT_ENABLE_CALLBACK;
const ENABLE_CART_OPTIONS_SETTINGS = RBSPAYMENT_ENABLE_CART_OPTIONS;
const ENABLE_SSLVERIFY_FIELD = RBSPAYMENT_ENABLE_SSLVERIFY_FIELD;
const ENABLE_BACK_URL_SETTINGS = RBSPAYMENT_ENABLE_BACK_URL_SETTINGS;
const API_VERSION = RBSPAYMENT_API_VERSION;
protected $module_version = '2.8.8';
public $_url = '';
protected $_currency;
public $_login;
public $_password;
protected $_token;
protected $_two_staging = false;
protected $_enable_cacert = true;
protected $_cacert_path = null;
public $_logging = false;
protected $_send_order = false;
protected $_tax_system = 0;
protected $_tax_type = 0;
protected $_versionFfd;
protected $_paymentMethodType;
protected $_paymentObjectType;
protected $_response;
protected $_error_code = 0;
protected $_error_text = "";
protected $test_mode;
protected $backToShopUrl;
protected $backToShopUrlName;
public $callbackType;
public $currency_code2num = array('BYN' => '933', 'BHD' => '048', 'BYR' => '974', 'CAD' => '124', 'CNY' => '156', 'EUR' => '978', 'GBP' => '826', 'HKD' => '344', 'HUF' => '348', 'ILS' => '376', 'JPY' => '392', 'KGS' => '417', 'KRW' => '410', 'KZT' => '398', 'MDL' => '498', 'MYR' => '458', 'OMR' => '512', 'PHP' => '608', 'RON' => '946', 'RUB' => '643', 'RUR' => '810', 'SGD' => '702', 'UAH' => '980', 'USD' => '840', 'NGN' => '566', 'MZN' => '943', 'BGN' => '975', 'BZD' => '084', 'GHS' => '936', 'GNF' => '324', 'XOF' => '952', 'PLN' => '985', 'LSL' => '426', 'TZS' => '834', 'NZD' => '554', 'KHR' => '116', 'TRY' => '949', 'AMD' => '051', 'SAR' => '682', 'AED' => '784', 'COP' => '170', 'AUD' => '036', 'IDR' => '360', 'KWD' => '414', 'JOD' => '400', 'INR' => '356');
public function __construct($processor_data)
{
if (is_string($processor_data['processor_params'])) {
$params = @unserialize($processor_data['processor_params']);
if (!is_array($params)) {
throw new \Exception('Ошибка десериализации processor_params');
}
$processor_data['processor_params'] = $params;
}
$this->callbackType = defined('RBSPAYMENT_CALLBACK_TYPE') ? RBSPAYMENT_CALLBACK_TYPE : "DYNAMIC";
if (self::API_VERSION >= 2) {
$this->_token = $processor_data['processor_params']['token'];
if (!empty($processor_data['processor_params']['token'])) {
$decoded_credentials = base64_decode($processor_data['processor_params']['token']);
list($l, $p) = explode(':', $decoded_credentials);
$this->_login = $l;
$this->_password = $p;
}
} else {
$this->_login = $processor_data['processor_params']['login'];
$this->_password = $processor_data['processor_params']['password'];
}
if ($processor_data['processor_params']['mode'] == 'test' || $processor_data['processor_params']['mode'] == 'dev') {
$this->_url = RBSPAYMENT_TEST_URL;
$this->test_mode = true;
} else {
$this->_url = RBSPAYMENT_PROD_URL;
$this->test_mode = false;
if (defined('RBSPAYMENT_PROD_URL_ALTERNATIVE_DOMAIN') && defined('RBSPAYMENT_PROD_URL_ALT_PREFIX')) {
if (substr($this->_login, 0, strlen(RBSPAYMENT_PROD_URL_ALT_PREFIX)) == RBSPAYMENT_PROD_URL_ALT_PREFIX) {
$pattern = '/^https:\/\/[^\/]+/';
$this->_url = preg_replace($pattern, rtrim(RBSPAYMENT_PROD_URL_ALTERNATIVE_DOMAIN, '/'), $this->_url);
} else {
}
}
}
if (!empty($processor_data['processor_params']['two_staging'])) {
$this->_two_staging = true;
}
if (self::ENABLE_BACK_URL_SETTINGS) {
$this->backToShopUrl = $processor_data['processor_params']['backToShopUrl'];
$this->backToShopUrlName = $processor_data['processor_params']['backToShopUrlName'];
}
if (!empty($processor_data['processor_params']['enable_cacert']) && $processor_data['processor_params']['enable_cacert'] == 'Y') {
$this->_enable_cacert = true;
} else {
$this->_enable_cacert = false;
}
if (!empty($processor_data['processor_params']['send_order']) && $processor_data['processor_params']['send_order'] == 'Y') {
$this->_send_order = true;
}
$this->_tax_system = (!empty($processor_data['processor_params']['tax_system'])) ? $processor_data['processor_params']['tax_system'] : 0;
if (!empty($processor_data['processor_params']['logging']) && $processor_data['processor_params']['logging'] == 'Y') {
$this->_logging = true;
}
$this->_tax_type = (!empty($processor_data['processor_params']['tax_type'])) ? $processor_data['processor_params']['tax_type'] : 0;
$this->_versionFfd = (!empty($processor_data['processor_params']['versionFfd'])) ? $processor_data['processor_params']['versionFfd'] : "v1_05";
$this->_paymentMethodType = (!empty($processor_data['processor_params']['paymentMethodType'])) ? $processor_data['processor_params']['paymentMethodType'] : 1;
$this->_paymentObjectType = (!empty($processor_data['processor_params']['paymentObjectType'])) ? $processor_data['processor_params']['paymentObjectType'] : 1;
}
public function _updateGatewayCallback($login, $password, $action_address, $callback_addresses_string)
{
$headers = array(
'Content-Type:application/json',
'Authorization: Basic ' . base64_encode($login . ":" . $password)
);
$data['callbacks_enabled'] = true;
$data['callback_type'] = $this->callbackType;
$data['callback_operations'] = "deposited,approved,declinedByTimeout,reversed,refunded";
if ($this->callbackType == "STATIC") {
$data['callback_addresses'] = $callback_addresses_string;
}
$response = $this->_sendGatewayData(json_encode($data), $action_address, $headers);
return $response;
}
public function register($order_info, $protocol = 'current')
{
$order_id = $order_info['order_id'];
$orderNumber = $order_id . '_' . substr(md5($order_id . TIME), 0, 3);
$jsonParams = array(
'CMS: ' => PRODUCT_NAME . " " . PRODUCT_VERSION,
'Module-Version: ' => $this->module_version
);
if (defined('RBSPAYMENT_ENABLE_BACK_URL_SETTINGS')
&& RBSPAYMENT_ENABLE_BACK_URL_SETTINGS === true
&& !empty($this->backToShopUrl)
) {
$jsonParams['backToShopUrl'] = $this->backToShopUrl;
}
#BLOCK_PHONE_TRANSFER_START[builder]
if (!empty($order_info['phone'])) {
$jsonParams['phone'] = $this->cleanPhoneNumber($order_info['phone']);
}
#BLOCK_PHONE_TRANSFER_END
$args = array(
'userName' => $this->_login,
'password' => $this->_password,
'orderNumber' => $orderNumber,
'amount' => fn_format_price_by_currency($order_info['total']) * 100,
'returnUrl' => fn_url("payment_notification.return?payment=alfabank&action=return&ordernumber=$order_id", AREA, $protocol),
'failUrl' => fn_url("payment_notification.error?payment=alfabank&ordernumber=$order_id", AREA, $protocol),
'jsonParams' => json_encode($jsonParams),
);
#BLOCK_PHONE_TRANSFER_START[builder]
if (!empty($order_info['phone'])) {
$args['orderPayerData'] = json_encode(array(
"mobilePhone" => $this->cleanPhoneNumber($order_info['phone'])
));
}
#BLOCK_PHONE_TRANSFER_END
if (self::ENABLE_CALLBACK == true
&& $this->callbackType == "DYNAMIC") {
$args['dynamicCallbackUrl'] = fn_url("payment_notification.return?payment=alfabank&payment_id=" . $order_info['payment_id'] . "&action=callback", AREA, $protocol);
}
if (defined('RBSPAYMENT_MANDATORY_CURRENCY') && RBSPAYMENT_MANDATORY_CURRENCY === true) {
$numeric_code = $this->currency_code2num[CART_SECONDARY_CURRENCY];
if (!empty($numeric_code)) {
$args['currency'] = $numeric_code;
}
}
if (defined('RBSPAYMENT_SEND_CLIENT_FULL_INFO') && RBSPAYMENT_SEND_CLIENT_FULL_INFO === true) {
$billingPayerData = $this->_getBillingPayerData($order_info);
if(!empty($billingPayerData)) {
$args['billingPayerData'] = json_encode($billingPayerData);
}
}
if (!empty($order_info['user_id'])) {
$client_email = !empty($order_info['email']) ? $order_info['email'] : "";
$site_url = parse_url(fn_url(''), PHP_URL_HOST);
$args['clientId'] = md5($order_info['user_id'] . $client_email . $site_url);
}
if (self::ENABLE_CART_OPTIONS_SETTINGS && $this->_send_order) {
$product_taxes = array();
foreach ($order_info['taxes'] as $k => $v) {
$item_rate_value = (int)$v['rate_value'];
foreach ($v['applies']['items']['P'] as $c => $d) {
if ($item_rate_value == 20) {
$tax_type = 6;
} else if ($item_rate_value == 18) {
$tax_type = 3;
} else if ($item_rate_value == 10) {
$tax_type = 2;
} else if ($item_rate_value == 0) {
$tax_type = 1;
} else if ($item_rate_value == 7) {
$tax_type = 12;
} else if ($item_rate_value == 5) {
$tax_type = 10;
} else {
$tax_type = $this->_tax_type;
}
$product_taxes[$c] = $tax_type;
}
}
$args['taxSystem'] = $this->_tax_system;
$items = array();
$itemsCnt = 1;
$subtotal_discount = isset($order_info['subtotal_discount']) ? $order_info['subtotal_discount'] : 0;
$shipping_cost = isset($order_info['shipping_cost']) ? $order_info['shipping_cost'] : 0;
$payment_surcharge = isset($order_info['payment_surcharge']) ? $order_info['payment_surcharge'] : 0;
foreach ($order_info['products'] as $value) {
$q = isset($value['amount']) ? $value['amount'] : 1;
$p = isset($value['price']) ? $value['price'] * 100 : 0;
$tax_type = (!empty($product_taxes)) ? $product_taxes[$value['item_id']] : $this->_tax_type;
$item['positionId'] = $itemsCnt++;
$item['name'] = isset($value['product']) ? strip_tags($value['product']) : '';
if ($this->_versionFfd == 'v1_05') {
$item['quantity'] = array(
'value' => $q,
'measure' => RBSPAYMENT_MEASUREMENT_NAME
);
} else {
$item['quantity'] = array(
'value' => $q,
'measure' => RBSPAYMENT_MEASUREMENT_CODE
);
}
$item['itemAmount'] = $p * $q;
$item['itemCode'] = $value['product_code'] . $item['positionId'];
$item['tax'] = array(
'taxType' => $tax_type
);
$item['itemPrice'] = $p;
$attributes = array();
$attributes[] = array(
"name" => "paymentMethod",
"value" => $this->_paymentMethodType
);
$attributes[] = array(
"name" => "paymentObject",
"value" => $this->_paymentObjectType
);
$item['itemAttributes']['attributes'] = $attributes;
$items[] = $item;
}
if ($payment_surcharge > 0) {
$itemSurcharge['positionId'] = $itemsCnt++;
$itemSurcharge['name'] = !empty($order_info['payment_method']['surcharge_title']) ? $order_info['payment_method']['surcharge_title'] : __("addons.alfabank.payment_surcharge");
if ($this->_versionFfd == 'v1_05') {
$itemSurcharge['quantity'] = array(
'value' => 1,
'measure' => RBSPAYMENT_MEASUREMENT_NAME
);
} else {
$itemSurcharge['quantity'] = array(
'value' => 1,
'measure' => RBSPAYMENT_MEASUREMENT_CODE
);
}
$itemSurcharge['itemAmount'] = $itemSurcharge['itemPrice'] = $payment_surcharge * 100;
$itemSurcharge['itemCode'] = 'Surcharge';
$itemSurcharge['tax'] = array(
'taxType' => $tax_type
);
$attributes = array();
$attributes[] = array(
"name" => "paymentMethod",
"value" => $this->_paymentMethodType
);
$attributes[] = array(
"name" => "paymentObject",
"value" => 4
);
$itemSurcharge['itemAttributes']['attributes'] = $attributes;
$items[] = $itemSurcharge;
}
if ($shipping_cost > 0) {
$itemShipment['positionId'] = $itemsCnt;
$itemShipment['name'] = __("addons.alfabank.delivery");
if ($this->_versionFfd == 'v1_05') {
$itemShipment['quantity'] = array(
'value' => 1,
'measure' => RBSPAYMENT_MEASUREMENT_NAME
);
} else {
$itemShipment['quantity'] = array(
'value' => 1,
'measure' => RBSPAYMENT_MEASUREMENT_CODE
);
}
$itemShipment['itemAmount'] = $itemShipment['itemPrice'] = $shipping_cost * 100;
$itemShipment['itemCode'] = 'Delivery';
$itemShipment['tax'] = array(
'taxType' => $tax_type
);
$attributes = array();
$attributes[] = array(
"name" => "paymentMethod",
"value" => $this->_paymentMethodType
);
$attributes[] = array(
"name" => "paymentObject",
"value" => 4
);
$itemShipment['itemAttributes']['attributes'] = $attributes;
$items[] = $itemShipment;
}
$order_bundle = array(
'orderCreationDate' => time(),
'customerDetails' => array(
'email' => $order_info['email'],
'phone' => $this->cleanPhoneNumber($order_info['phone'])
),
'cartItems' => array('items' => $items)
);
$discountHelper = new AlfabankDiscount();
$discount = $discountHelper->discoverDiscount($args['amount'], $order_bundle['cartItems']['items']);
if ($discount > 0) {
$discountHelper->setOrderDiscount($discount);
$recalculatedPositions = $discountHelper->normalizeItems($order_bundle['cartItems']['items']);
$order_bundle['cartItems']['items'] = $recalculatedPositions;
}
$args['orderBundle'] = json_encode($order_bundle);
}
$action_adr = 'register.do';
if ($this->_two_staging) {
$action_adr = 'registerPreAuth.do';
}
$this->_response = $this->_sendGatewayData(http_build_query($args, '', '&'), $this->_url . $action_adr);
$this->_response = json_decode($this->_response, true);
if ($this->_logging) {
$args['password'] = '**removed from log**';
$this->writeLog([
'request_url' => $this->_url . $action_adr,
'request_data' => $args,
'response' => $this->_response
], 'API Request & Response');
}
if (!empty($this->_response['errorCode'])) {
$this->_error_code = $this->_response['errorCode'];
$this->_error_text = $this->_response['errorMessage'];
}
return $this->_response;
}
public function _sendGatewayData($data, $action_address, $headers = array())
{
$curl_opt = array(
CURLOPT_HTTPHEADER => $headers,
CURLOPT_VERBOSE => true,
CURLOPT_SSL_VERIFYHOST => false,
CURLOPT_URL => $action_address,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_POST => true,
CURLOPT_POSTFIELDS => $data,
CURLOPT_HEADER => true,
);
$ssl_verify_peer = false;
if (self::ENABLE_SSLVERIFY_FIELD === true) {
if ( $this->_enable_cacert === true && file_exists(realpath(dirname(__FILE__) . "/../../cacert.cer")) ) {
$ssl_verify_peer = true;
$curl_opt[CURLOPT_CAINFO] = realpath(dirname(__FILE__) . "/../../cacert.cer");
}
}
$curl_opt[CURLOPT_SSL_VERIFYPEER] = $ssl_verify_peer;
$ch = curl_init();
curl_setopt_array($ch, $curl_opt);
$response = curl_exec($ch);
if ($response === false) {
$error = array('errorCode' => 999, "errorMessage" => curl_error($ch));
return json_encode($error);
}
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);
return substr($response, $header_size);
}
public function correctBundleItem(&$item, $discount)
{
$item['itemAmount'] -= $discount;
$item['itemPrice'] = $item['itemAmount'] % $item['quantity']['value'];
if ($item['itemPrice'] != 0) {
$item['itemAmount'] += $item['quantity']['value'] - $item['itemPrice'];
};
$item['itemPrice'] = $item['itemAmount'] / $item['quantity']['value'];
return $item;
}
public function getOrder($transaction_id)
{
$data = array(
'userName' => $this->_login,
'password' => $this->_password,
'orderId' => $transaction_id
);
$data = http_build_query($data, '', '&');
$this->_response = $this->_sendGatewayData($data, $this->_url . 'getOrderStatus.do');
$this->_response = json_decode($this->_response, true);
if (!empty($this->_response['errorCode'])) {
$this->_error_code = $this->_response['errorCode'];
$this->_error_text = $this->_response['errorMessage'];
}
return $this->_response;
}
public function getOrderExtended($transaction_id)
{
$data = array(
'userName' => $this->_login,
'password' => $this->_password,
'orderId' => $transaction_id
);
$data = http_build_query($data, '', '&');
$this->_response = $this->_sendGatewayData($data, $this->_url . 'getOrderStatusExtended.do');
$this->_response = json_decode($this->_response, true);
if (!empty($this->_response['errorCode'])) {
$this->_error_code = $this->_response['errorCode'];
$this->_error_text = $this->_response['errorMessage'];
}
return $this->_response;
}
public function getErrorCode()
{
return $this->_error_code;
}
public function getErrorText()
{
return $this->_error_text;
}
public function isError()
{
return !empty($this->_error_code);
}
/**
* Логирует данные в файл, если включено логирование в настройках модуля.
*
* @param mixed  $data  Данные для логирования (строка, массив, объект).
* @param string $title Заголовок записи.
*/
public function writeLog($data, $title = '')
{
if (empty($this->_logging)) {
return;
}
$log_dir = fn_get_files_dir_path() . 'alfabank_logs/';
fn_mkdir($log_dir);
$file_path = $log_dir . 'cs_alfabank_' . date('Y-m') . '.log';
$header = "TIME: " . date('Y-m-d H:i:s') . " [" . $title . "]\n";
if (is_array($data) || is_object($data)) {
$body = print_r($data, true);
} else {
$body = (string) $data;
}
$entry = $header . $body . "\n" . str_repeat('=', 80) . "\n";
error_log($entry, 3, $file_path);
}
public static function convertSum($price)
{
$price = fn_format_price_by_currency($price, CART_PRIMARY_CURRENCY, CART_SECONDARY_CURRENCY);
$price = fn_format_rate_value($price, 'F', 2, '.', '', '');
return $price;
}
public function _getBillingPayerData($order_info) {
$billingPayerData = array();
$pattern = '/^[A-Za-z0-9\s\'"!#$%&@^~*+=\-_.,:;<>|，΄´–\/?\\\\{}()\[\]\n]+$/';
$billingAddress1 = isset($order_info['b_address']) ? $order_info['b_address'] : '';
$billingAddress2 = isset($order_info['b_address_2']) ? $order_info['b_address_2'] : '';
$billingCity = isset($order_info['b_city']) ? $order_info['b_city'] : '';
$billingPostcode = isset($order_info['b_zipcode']) ? $order_info['b_zipcode'] : '';
$billingCountry = isset($order_info['b_county']) ? $order_info['b_county'] : ''; //b_country_descr
$billingState = isset($order_info['b_state']) ? $order_info['b_state'] : ''; //b_state_descr
if (!empty($billingCity) && preg_match($pattern, $billingCity)) {
$billingPayerData['billingCity'] = $billingCity;
}
if (!empty($billingCountry) && preg_match($pattern, $billingCountry)) {
$billingPayerData['billingCountry'] = $billingCountry;
}
if (!empty($billingAddress1) && preg_match($pattern, $billingAddress1)) {
$billingPayerData['billingAddressLine1'] = $billingAddress1;
}
if (!empty($billingAddress2) && preg_match($pattern, $billingAddress2)) {
$billingPayerData['billingAddressLine2'] = $billingAddress2;
}
if (!empty($billingPostcode) && preg_match($pattern, $billingPostcode)) {
$billingPayerData['billingPostalCode'] = $billingPostcode;
}
if (!empty($billingState) && preg_match($pattern, $billingState)) {
$billingPayerData['billingState'] = $billingState;
}
return $billingPayerData;
}
public static function getVatOptions()
{
return array(
0 => __('addons.alfabank.vat_1'),
1 => __('addons.alfabank.vat_2'),
2 => __('addons.alfabank.vat_3'),
3 => __('addons.alfabank.vat_4'),
6 => __('addons.alfabank.vat_5'),
4 => __('addons.alfabank.vat_6'),
5 => __('addons.alfabank.vat_7'),
7 => __('addons.alfabank.vat_8'),
);
}
private function cleanPhoneNumber($telephone): string
{
return substr(preg_replace('/\D+/', '', $telephone), 0, 15);
}
}