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

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Addons\Tinkoff\Enum\PaymentSessionStatuses;
use Tygh\Addons\Tinkoff\Enum\PayTypes;
use Tygh\Addons\Tinkoff\Payments\EACQClient;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\OrderStatuses;
use Tygh\Enum\SiteArea;

/**
 * @var array  $auth
 * @var string $mode
 */

if ($mode === 'get_notification') {
    $request_information = file_get_contents('php://input');
    if (empty($request_information)) {
        return [CONTROLLER_STATUS_NO_CONTENT];
    }
    $notification = json_decode($request_information, true);
    if (empty($notification['OrderId'])) {
        return [CONTROLLER_STATUS_NO_CONTENT];
    }

    /** @var int $order_id */
    $order_id = $notification['OrderId'];

    /** @var \Tygh\Lock\Factory $lock_factory */
    $lock_factory = Tygh::$app['lock.factory'];
    $lock = $lock_factory->createLock('tinkoff_change_order_status_' . $order_id, 30.0, false);
    if (!$lock->acquire()) {
        do {
            $lock->wait();
        } while (!$lock->acquire());
    }

    try {
        $order_info = fn_get_order_info($order_id);

        if ((int) ($order_info['total'] * 100) !== $notification['Amount']) {
            fn_log_event('general', 'runtime', ['message' => __('addons.tbank.log_amounts_do_not_match')]);

            $lock->release();
            exit('AMOUNTS DO NOT MATCH');
        }

        if (!empty($notification['PaymentId'])) {
            $client = new EACQClient(
                $order_info['payment_method']['processor_params']['terminal_key'],
                $order_info['payment_method']['processor_params']['password'],
                Tygh::$app['addons.rus_taxes.receipt_factory']
            );
            $response = $client->getState($notification['PaymentId']);
            /** @psalm-suppress PossiblyInvalidArrayOffset */
            if ($response['Status'] !== $notification['Status']) {
                $lock->release();
                exit('OK');
            }
        }

        $settled_order_statuses = fn_get_settled_order_statuses();

        if (
            (
                $notification['Status'] === PaymentSessionStatuses::AUTHORIZED
                && $order_info['payment_method']['processor_params']['pay_type'] === PayTypes::TWO_STEP
                || $notification['Status'] === PaymentSessionStatuses::CONFIRMED
            )
            && !in_array($order_info['status'], $settled_order_statuses)
        ) {
            $order_ids = [];

            if ($order_info['status'] === OrderStatuses::PARENT) {
                foreach (fn_get_suborders_info($order_info['order_id']) as $suborder_info) {
                    if (
                        !in_array($suborder_info['status'], $settled_order_statuses)
                        && fn_change_order_status((int) $suborder_info['order_id'], OrderStatuses::PAID)
                    ) {
                        $order_ids[] = (int) $suborder_info['order_id'];
                    }
                }

                if ($order_ids) {
                    $order_ids[] = $order_info['order_id'];
                }
            } elseif (fn_change_order_status($order_id, OrderStatuses::PAID)) {
                $order_ids[] = $order_id;
            }

            if ($order_ids) {
                db_query('DELETE FROM ?:user_session_products WHERE order_id IN (?n) AND type = ?s', $order_ids, SiteArea::STOREFRONT);
            }
        }

        if (in_array($notification['Status'], ['REVERSED', 'CANCELED', 'REJECTED', 'REFUNDED']) && in_array($order_info['status'], $settled_order_statuses)) {
            if ($order_info['status'] === OrderStatuses::PARENT) {
                foreach (fn_get_suborders_info($order_info['order_id']) as $suborder_info) {
                    if (in_array($suborder_info['status'], $settled_order_statuses)) {
                        fn_change_order_status((int) $suborder_info['order_id'], OrderStatuses::CANCELED);
                    }
                }
            } elseif (in_array($order_info['status'], $settled_order_statuses)) {
                fn_change_order_status((int) $order_info['order_id'], OrderStatuses::CANCELED);
            }

            fn_update_order_payment_info($order_id, ['addons.tinkoff.funds_were_transferred' => '']);
        }
    } catch (Exception $e) {
        fn_log_event('general', 'runtime', ['message' => __('addons.tbank.log_notification_exception', ['[error_msg]' => $e->getMessage()])]);
    } finally {
        $lock->release();
    }

    exit('OK');
}

if ($mode === 'success') {
    if (!isset($_REQUEST['OrderId'])) {
        return [CONTROLLER_STATUS_DENIED];
    }

    /** @var int $order_id */
    $order_id = $_REQUEST['OrderId'];

    /** @var \Tygh\Lock\Factory $lock_factory */
    $lock_factory = Tygh::$app['lock.factory'];
    $lock = $lock_factory->createLock('tinkoff_change_order_status_' . $order_id, 30.0, false);
    if (!$lock->acquire()) {
        do {
            $lock->wait();
        } while (!$lock->acquire());
    }

    try {
        $order_info = fn_get_order_info($order_id);
        if (empty($order_info)) {
            return [CONTROLLER_STATUS_DENIED];
        }
        Tygh::$app['session']['confirming_order'] = true;
        if (!fn_is_order_allowed($order_id, $auth)) {
            return [CONTROLLER_STATUS_DENIED];
        }
        if (in_array($order_info['status'], fn_get_settled_order_statuses())) {
            fn_order_placement_routines('route', $order_info['order_id'], false);
        }
        if (!isset($_REQUEST['PaymentId']) || $order_info['payment_info']['payment_id'] !== $_REQUEST['PaymentId']) {
            return [CONTROLLER_STATUS_DENIED];
        }
        $client = new EACQClient(
            $order_info['payment_method']['processor_params']['terminal_key'],
            $order_info['payment_method']['processor_params']['password'],
            Tygh::$app['addons.rus_taxes.receipt_factory']
        );
        $response = $client->getState($order_info['payment_info']['payment_id']);
        if (!empty($response['Success'])) {
            fn_update_order_payment_info($order_id, ['addons.tinkoff.payment_status' => $response['Status']]);
            if (in_array($response['Status'], ['AUTHORIZED', 'CONFIRMED'])) {
                if ($order_info['status'] === OrderStatuses::PARENT) {
                    foreach (fn_get_suborders_info($order_info['order_id']) as $suborder_info) {
                        fn_change_order_status((int) $suborder_info['order_id'], OrderStatuses::PAID);
                    }
                } else {
                    fn_change_order_status((int) $order_info['order_id'], OrderStatuses::PAID);
                }
            }
            if (in_array($response['Status'], ['REJECTED', 'CANCELED', 'REVERSED', 'REFUNDED'])) {
                if ($order_info['status'] === OrderStatuses::PARENT) {
                    foreach (fn_get_suborders_info($order_info['order_id']) as $suborder_info) {
                        fn_change_order_status((int) $suborder_info['order_id'], OrderStatuses::CANCELED);
                    }
                } else {
                    fn_change_order_status((int) $order_info['order_id'], OrderStatuses::CANCELED);
                }
            }
        }
        fn_order_placement_routines('route', $order_info['order_id'], false);
    } catch (Exception $e) {
        fn_log_event('general', 'runtime', ['message' => __('addons.tbank.log_notification_exception', ['[error_msg]' => $e->getMessage()])]);
    } finally {
        $lock->release();
    }
}

if ($mode === 'fail') {
    if (!isset($_REQUEST['OrderId'])) {
        return [CONTROLLER_STATUS_DENIED];
    }
    $order_id = $_REQUEST['OrderId'];
    $order_info = fn_get_order_info($order_id);
    if (empty($order_info)) {
        return [CONTROLLER_STATUS_DENIED];
    }
    Tygh::$app['session']['confirming_order'] = true;
    if (!fn_is_order_allowed($order_id, $auth)) {
        return [CONTROLLER_STATUS_DENIED];
    }

    fn_set_notification(NotificationSeverity::ERROR, __('addons.tinkoff.payment_failed'), $_REQUEST['Message']);
    fn_update_order_payment_info($order_id, ['addons.tinkoff.payment_message' => $_REQUEST['Message']]);
    fn_order_placement_routines('route', $order_info['order_id'], false);
    //TODO Remove extra notification about transaction canceled by customer.
}
