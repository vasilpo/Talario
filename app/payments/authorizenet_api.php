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

use Tygh\AuthorizeNetApi\AuthorizeNet;
use Tygh\Enum\OrderStatuses;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/** @var \Composer\Autoload\ClassLoader $class_loader */
$class_loader = Registry::get('class_loader');

$class_loader->addPsr4(
    'Tygh\\AuthorizeNetApi\\',
    __DIR__ . '/authorizenet_api_files/AuthorizeNetApi'
);

if (!empty($processor_data) && !empty($order_info)) {
    $pp_response = [];
    $authorize_net = new AuthorizeNet($processor_data, $order_info);

    $response = $authorize_net->sendTransaction();
    list($transaction_id, $order_status) = $authorize_net->processResponse($response);

    if (!empty($transaction_id) && $order_status) {
        $pp_response['order_status'] = $order_status;
        $pp_response['transaction_id'] = $transaction_id;

        if ($order_status === OrderStatuses::PAID) {
            $pp_response['reason_text'] = __('transaction_approved');
        }
    } else {
        $pp_response['order_status'] = OrderStatuses::FAILED;
        $pp_response['reason_text'] = __('text_transaction_declined');
    }
}
