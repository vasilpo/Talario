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

use Tygh\Addons\TinkoffMultiparty\Enum\PaymentSessionStatuses;
use Tygh\Addons\TinkoffMultiparty\Enum\PayTypes;
use Tygh\Addons\TinkoffMultiparty\Payments\EACQMultipartyClient;
use Tygh\Enum\UserTypes;
use Tygh\Enum\YesNo;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */
/** @var array $auth */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'details') {
    /** @var array $order_info */
    $order_info = Tygh::$app['view']->getTemplateVars('order_info');
    $is_tinkoff_multiparty_payment = isset($order_info['payment_method']['processor_params']['is_tinkoff_multiparty']);
    $is_payment_id_exists = isset($order_info['payment_info']['payment_id']);
    if ($is_tinkoff_multiparty_payment && $is_payment_id_exists) {
        $payment_info = $order_info['payment_info'];
        $processor_params = $order_info['payment_method']['processor_params'];
        $client = new EACQMultipartyClient(
            $processor_params['terminal_key'],
            $processor_params['password'],
            Tygh::$app['addons.rus_taxes.receipt_factory'],
            Tygh::$app['addons.tinkoff_multiparty.payouts_manager_service']
        );

        $response = $client->getState($order_info['payment_info']['payment_id']);
        if (!empty($response['Success'])) {
            fn_update_order_payment_info($order_info['order_id'], ['addons.tinkoff_multiparty.payment_status' => $response['Status']]);
            $order_info['payment_info']['addons.tinkoff_multiparty.payment_status'] = $response['Status'];
        }

        $is_show_transfer_funds_button = isset($processor_params['pay_type'])
            && $processor_params['pay_type'] === PayTypes::TWO_STEP
            && (
                isset($payment_info['addons.tinkoff_multiparty.payment_status'])
                && $payment_info['addons.tinkoff_multiparty.payment_status'] === PaymentSessionStatuses::AUTHORIZED
                && (
                    !isset($response['Status'])
                    || ($response['Status'] === PaymentSessionStatuses::AUTHORIZED)
                )
                || (isset($response['Status']) && $response['Status'] === PaymentSessionStatuses::AUTHORIZED)
            );

        $payment_statuses_not_refund = PaymentSessionStatuses::getStatusesNotRefund();
        if (!empty($order_info['parent_order_id'])) {
            $payment_statuses_not_refund = array_merge($payment_statuses_not_refund, [PaymentSessionStatuses::AUTHORIZED]);
        }

        $is_show_cancel_funds_button = isset($payment_info['addons.tinkoff_multiparty.payment_status'])
            && !in_array($payment_info['addons.tinkoff_multiparty.payment_status'], $payment_statuses_not_refund)
            && (!isset($response['Status']) || !in_array($response['Status'], $payment_statuses_not_refund))
            || (isset($response['Status']) && !in_array($response['Status'], $payment_statuses_not_refund));

        if (
            !empty($payment_info['addons.tinkoff_multiparty.funds_were_refunded'])
            || !UserTypes::isAdmin($auth['user_type'])
            || YesNo::isFalse($auth['is_root'])
        ) {
            $is_show_cancel_funds_button = false;
        }

        Tygh::$app['view']->assign('is_show_transfer_funds_button', $is_show_transfer_funds_button);
        Tygh::$app['view']->assign('is_show_cancel_funds_button', $is_show_cancel_funds_button);
        Tygh::$app['view']->assign('is_tinkoff_multiparty_payment', $is_tinkoff_multiparty_payment);
        Tygh::$app['view']->assign('order_info', $order_info);
    }
}
