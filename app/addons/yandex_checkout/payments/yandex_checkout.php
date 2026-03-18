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

use Tygh\Addons\YandexCheckout\Payments\YandexCheckout;
use Tygh\Addons\YandexCheckout\ServiceProvider;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @var array                 $order_info
 * @var array                 $processor_data
 * @var array<string, string> $pp_response
 */
$payment = new YandexCheckout(
    $order_info['payment_method']['processor_params']['shop_id'],
    $order_info['payment_method']['processor_params']['scid'],
    ServiceProvider::getReceiptService()
);

try {
    $response = $payment->createPayment($order_info, $processor_data['processor_params']);
    $confirmation_url = $response->getConfirmation()->getConfirmationUrl();

    fn_update_order_payment_info($order_info['order_id'], ['payment_id' => $response->getId()]);
    fn_create_payment_form($confirmation_url, [], __('yandex_checkout.yandex_checkout'), true, 'get');
} catch (Exception $exception) {
    $payment->getLogger()->logException($exception);
    $pp_response['reason_text'] = $exception->getMessage();
    $pp_response['order_status'] = 'F';
}
