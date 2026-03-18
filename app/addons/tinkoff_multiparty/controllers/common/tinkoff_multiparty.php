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

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Addons\TinkoffMultiparty\Enum\PaymentSessionStatuses;
use Tygh\Addons\TinkoffMultiparty\Enum\PayTypes;
use Tygh\Addons\TinkoffMultiparty\Payments\EACQMultipartyClient;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\OrderStatuses;
use Tygh\Enum\SiteArea;
use Tygh\Enum\YesNo;

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
        $processor_params = $order_info['payment_method']['processor_params'];

        if ((int) ($order_info['total'] * 100) !== $notification['Amount']) {
            fn_log_event('general', 'runtime', ['message' => __('addons.tbank_multiparty.log_amounts_do_not_match')]);

            $lock->release();
            exit('AMOUNTS DO NOT MATCH');
        }

        $client = new EACQMultipartyClient(
            $processor_params['terminal_key'],
            $processor_params['password'],
            Tygh::$app['addons.rus_taxes.receipt_factory'],
            Tygh::$app['addons.tinkoff_multiparty.payouts_manager_service']
        );

        if (!empty($notification['PaymentId'])) {
            $response = $client->getState($notification['PaymentId']);
            /** @psalm-suppress PossiblyInvalidArrayOffset */
            if ($response['Status'] !== $notification['Status']) {
                $lock->release();
                exit('OK');
            }
        }

        $settled_order_statuses = fn_get_settled_order_statuses();

        if (
            $notification['Status'] === PaymentSessionStatuses::AUTHORIZED
            && $order_info['payment_method']['processor_params']['pay_type'] === PayTypes::TWO_STEP
            || $notification['Status'] === PaymentSessionStatuses::CONFIRMED
        ) {
            if (
                $notification['Status'] === PaymentSessionStatuses::AUTHORIZED
                && !empty($processor_params['pay_type'])
                && $processor_params['pay_type'] === PayTypes::TWO_STEP
                && YesNo::isTrue($order_info['is_parent_order'])
            ) {
                /** @var array<string, int|array<string, array<string>>> $order_info */
                $client->transferFunds($order_info);
            }

            if (!in_array($order_info['status'], $settled_order_statuses)) {
                $order_ids = [];

                if ($order_info['status'] === OrderStatuses::PARENT) {
                    foreach (fn_get_suborders_info($order_id) as $suborder_info) {
                        if (
                            !in_array($suborder_info['status'], $settled_order_statuses)
                            && fn_change_order_status((int) $suborder_info['order_id'], OrderStatuses::PAID)
                        ) {
                            $order_ids[] = (int) $suborder_info['order_id'];
                        }
                    }

                    $order_ids[] = $order_ids ? $order_id : [];
                } elseif (fn_change_order_status($order_id, OrderStatuses::PAID)) {
                    $order_ids[] = $order_id;
                }

                if ($order_ids) {
                    db_query('DELETE FROM ?:user_session_products WHERE order_id IN (?n) AND type = ?s', $order_ids, SiteArea::STOREFRONT);
                }
            }
        }

        if (in_array($notification['Status'], PaymentSessionStatuses::getStatusesForOrderCanceled())) {
            if ($order_info['status'] === OrderStatuses::PARENT) {
                foreach (fn_get_suborders_info($order_id) as $suborder_info) {
                    if (in_array($suborder_info['status'], $settled_order_statuses)) {
                        fn_change_order_status((int) $suborder_info['order_id'], OrderStatuses::CANCELED);
                    }
                }
            } elseif (in_array($order_info['status'], $settled_order_statuses)) {
                fn_change_order_status($order_id, OrderStatuses::CANCELED);
            }
        }
    } catch (Exception $e) {
        fn_log_event('general', 'runtime', ['message' => __('addons.tbank_multiparty.log_notification_exception', ['[error_msg]' => $e->getMessage()])]);
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
        $client = new EACQMultipartyClient(
            $order_info['payment_method']['processor_params']['terminal_key'],
            $order_info['payment_method']['processor_params']['password'],
            Tygh::$app['addons.rus_taxes.receipt_factory'],
            Tygh::$app['addons.tinkoff_multiparty.payouts_manager_service']
        );
        $response = $client->getState($order_info['payment_info']['payment_id']);
        if (!empty($response['Success'])) {
            fn_update_order_payment_info($order_id, ['addons.tinkoff_multiparty.payment_status' => $response['Status']]);
            if (in_array($response['Status'], PaymentSessionStatuses::getStatusesForOrderPaid())) {
                if ($order_info['status'] === OrderStatuses::PARENT) {
                    foreach (fn_get_suborders_info($order_info['order_id']) as $suborder_info) {
                        fn_change_order_status((int) $suborder_info['order_id'], OrderStatuses::PAID);
                    }
                } else {
                    fn_change_order_status((int) $order_info['order_id'], OrderStatuses::PAID);
                }
            }
            if (in_array($response['Status'], PaymentSessionStatuses::getStatusesForOrderCanceled())) {
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
        fn_log_event('general', 'runtime', ['message' => __('addons.tbank_multiparty.log_notification_exception', ['[error_msg]' => $e->getMessage()])]);
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

    fn_set_notification(NotificationSeverity::ERROR, __('addons.tinkoff_multiparty.payment_failed'), $_REQUEST['Message']);
    fn_update_order_payment_info($order_id, ['addons.tinkoff_multiparty.payment_message' => $_REQUEST['Message']]);
    fn_order_placement_routines('route', $order_info['order_id'], false);
    //TODO Remove extra notification about transaction canceled by customer.
}
