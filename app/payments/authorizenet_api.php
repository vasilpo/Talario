<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

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
