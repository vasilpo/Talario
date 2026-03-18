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
 * @var string                $mode
 */

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Addons\TinkoffMultiparty\Payments\EACQMultipartyClient;
use Tygh\Enum\OrderStatuses;
use Tygh\Tygh;

$client = new EACQMultipartyClient(
    $order_info['payment_method']['processor_params']['terminal_key'],
    $order_info['payment_method']['processor_params']['password'],
    Tygh::$app['addons.rus_taxes.receipt_factory'],
    Tygh::$app['addons.tinkoff_multiparty.payouts_manager_service']
);
/** @var array{Success: bool, Details: string, PaymentURL: string, PaymentId: string} $response */
$response = $client->init($order_info, $processor_data['processor_params'], $mode);
if (!empty($response['Success'])) {
    $confirmation_url = $response['PaymentURL'];
    fn_update_order_payment_info($order_info['order_id'], ['payment_id' => $response['PaymentId']]);
    fn_create_payment_form($confirmation_url, [], __('addons.tinkoff_multiparty.tinkoff_payment'), true, 'get');
} else {
    if (!empty($response['ErrorCode']) && $response['ErrorCode'] === '309') {
        //phpcs:ignore
        $pp_response['reason_text'] = __('addons.tinkoff_multiparty.receipt_object_is_required');
    } else {
        //phpcs:ignore
        $pp_response['reason_text'] = $response['Details'];
    }

    //phpcs:ignore
    $pp_response['order_status'] = OrderStatuses::FAILED;
}
