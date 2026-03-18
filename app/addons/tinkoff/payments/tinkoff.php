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

/**
 * @var array                 $order_info
 * @var array                 $processor_data
 * @var array<string, string> $pp_response
 */

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Addons\Tinkoff\Payments\EACQClient;
use Tygh\Enum\OrderStatuses;
use Tygh\Enum\YesNo;
use Tygh\Tygh;

$client = new EACQClient(
    $order_info['payment_method']['processor_params']['terminal_key'],
    $order_info['payment_method']['processor_params']['password'],
    Tygh::$app['addons.rus_taxes.receipt_factory']
);
/** @var array{Success: bool, Details: string, PaymentURL: string, PaymentId: string} $response */
$response = $client->init($order_info, $processor_data['processor_params']);
if (!empty($response['Success'])) {
    $confirmation_url = $response['PaymentURL'];
    if (YesNo::toBool($processor_data['processor_params']['get_qr'])) {
        $qr_request_response = $client->getQr($response['PaymentId']);
        if (!empty($qr_request_response['Success'])) {
            $confirmation_url = $qr_request_response['Data'];
        }
    }
    fn_update_order_payment_info($order_info['order_id'], ['payment_id' => $response['PaymentId']]);
    fn_create_payment_form($confirmation_url, [], __('addons.tinkoff.tinkoff_payment'), true, 'get');
} else {
    if (!empty($response['ErrorCode']) && $response['ErrorCode'] === '309') {
        //phpcs:ignore
        $pp_response['reason_text'] = __('addons.tinkoff.receipt_object_is_required');
    } else {
        //phpcs:ignore
        $pp_response['reason_text'] = $response['Details'];
    }

    //phpcs:ignore
    $pp_response['order_status'] = OrderStatuses::FAILED;
}
