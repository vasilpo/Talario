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

use Tygh\Addons\PaypalCheckout\Payments\PaypalCheckout;
use Tygh\Addons\PaypalCheckout\ServiceProvider;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\UserTypes;
use Tygh\Enum\YesNo;
use Tygh\Registry;

/**
 * Installs PayPal Complete Payments payment processor.
 *
 * @return void
 */
function fn_paypal_checkout_install()
{
    /** @var \Tygh\Database\Connection $db */
    $db = Tygh::$app['db'];

    if (
        $db->getField(
            'SELECT type FROM ?:payment_processors WHERE processor_script = ?s',
            PaypalCheckout::getScriptName()
        )
    ) {
        return;
    }

    $db->query(
        'INSERT INTO ?:payment_processors ?e',
        [
            'processor'          => __('paypal_checkout.paypal_checkout'),
            'processor_script'   => PaypalCheckout::getScriptName(),
            'processor_template' => 'addons/paypal_checkout/views/orders/components/payments/paypal_checkout.tpl',
            'admin_template'     => 'paypal_checkout.tpl',
            'callback'           => YesNo::YES,
            'type'               => 'P',
            'addon'              => PaypalCheckout::getPaymentName(),
        ]
    );
}

/**
 * Disables PayPal Complete Payments payment methods upon add-on uninstallation.
 *
 * @return void
 */
function fn_paypal_checkout_uninstall()
{
    /** @var \Tygh\Database\Connection $db */
    $db = Tygh::$app['db'];

    $processor_id = $db->getField(
        'SELECT processor_id FROM ?:payment_processors WHERE processor_script = ?s',
        PaypalCheckout::getScriptName()
    );

    if (!$processor_id) {
        return;
    }

    $db->query('DELETE FROM ?:payment_processors WHERE processor_id = ?i', $processor_id);
    $db->query(
        'UPDATE ?:payments SET ?u WHERE processor_id = ?i',
        [
            'processor_id'     => 0,
            'processor_params' => '',
            'status'           => ObjectStatuses::DISABLED,
        ],
        $processor_id
    );
}

/**
 * The "save_log" hook handler.
 *
 * Actions performed:
 * - Adds PayPal Debug ID response header value to the logged HTTP request.
 *
 * @param string                $type                Log type
 * @param string                $action              Event action
 * @param string                $data                Request data
 * @param int                   $user_id             Logged in user ID
 * @param array<string, string> $content             Logged data
 * @param string                $event_type          Event type
 * @param string                $object_primary_keys Object primary key names
 *
 * @return void
 */
function fn_paypal_checkout_save_log($type, $action, $data, $user_id, array &$content, $event_type, $object_primary_keys)
{
    if (
        $type !== 'requests'
        || $action !== 'http'
    ) {
        return;
    }

    /** @var string $request_debug_id */
    $request_debug_id = Registry::ifGet('runtime.paypal_checkout.debug_id', '');
    if (!$request_debug_id) {
        return;
    }

    $content['paypal_checkout.debug_id'] = $request_debug_id;
    Registry::del('runtime.paypal_checkout.debug_id');
}

/**
 * The "create_shipment_post" hook handler.
 *
 * Actions performed:
 * - Sends shipments details to PayPal.
 *
 * @param array<string, string> $shipment_data      Shipment data
 * @param int                   $shipment_id        Shipment identifier
 * @param int                   $group_key          Cart products group key
 * @param bool                  $all_products       Whether to use all products to create the new shipment
 * @param array{C: bool}        $force_notification Force notification users
 * @param array                 $order_info         Order info
 *
 * @psalm-param array{
 *       paypal_checkout_send_shipment_info: string,
 *       carrier: string,
 *       tracking_number: string,
 *  } $shipment_data
 * @psalm-param array{
 *      order_id: int,
 *      payment_id: int,
 *      payment_info: array{
 *          "paypal_checkout.capture_id": string,
 *          "paypal_checkout.order_id": string
 *      },
 *      payment_method: array{processor_params: array<string>}
 * } $order_info
 *
 * @return void
 */
function fn_paypal_checkout_update_shipment_before_send_notification(
    array $shipment_data,
    $shipment_id,
    $group_key,
    $all_products,
    array $force_notification,
    array $order_info
) {
    fn_paypal_checkout_send_shipment_info($shipment_data, $order_info, $force_notification);
}

/**
 * The "create_shipment_post" hook handler.
 *
 * Actions performed:
 * - Sends shipments details to PayPal.
 *
 * @param array<string, string> $shipment_data      Shipment data
 * @param int                   $shipment_id        Created shipment identifier
 * @param int                   $group_key          Group number
 * @param bool                  $all_products       Whether to use all products to create the new shipment
 * @param array{C: bool}        $force_notification Force notification users
 * @param array<string, string> $old_shipment_data  Array of old shipment data.
 *
 * @psalm-param array{
 *       paypal_checkout_send_shipment_info: string,
 *       carrier?: string,
 *       tracking_number: string,
 *  } $shipment_data
 * @psalm-param array{
 *       order_id: int,
 *       carrier: string,
 *       tracking_number: string
 * } $old_shipment_data
 *
 * @return void
 */
function fn_paypal_checkout_update_shipment_post(
    array $shipment_data,
    $shipment_id,
    $group_key,
    $all_products,
    array $force_notification,
    array $old_shipment_data
) {
    if (!isset($shipment_data['tracking_number'])) {
        return;
    }

    $is_tracking_number_changed = $shipment_data['tracking_number'] !== $old_shipment_data['tracking_number'];

    if (
        empty($old_shipment_data['order_id'])
        || !$is_tracking_number_changed
    ) {
        return;
    }

    //phpcs:ignore
    if ($order_info = fn_get_order_info($old_shipment_data['order_id'])) {
        $shipment_data['carrier'] = $shipment_data['carrier'] ?? $old_shipment_data['carrier'];

        fn_paypal_checkout_send_shipment_info($shipment_data, $order_info, $force_notification);
    }
}

/**
 * @param array<string, string> $shipment_data      Shipment data
 * @param array<string, string> $order_info         Order info
 * @param array{C: bool}        $force_notification Force notification users
 *
 * @psalm-param array{
 *     paypal_checkout_send_shipment_info: string,
 *     carrier: string,
 *     tracking_number: string,
 * } $shipment_data
 * @psalm-param array{
 *     order_id: int,
 *     payment_id: int,
 *     payment_info: array{
 *         "paypal_checkout.capture_id": string,
 *         "paypal_checkout.order_id": string
 *     },
 *     payment_method: array{processor_params: array<string>}
 * } $order_info
 *
 * @return bool
 */
function fn_paypal_checkout_send_shipment_info(array $shipment_data, array $order_info, array $force_notification)
{
    if (
        !fn_check_payment_script(PaypalCheckout::getScriptName(), $order_info['order_id'])
        || empty($shipment_data['paypal_checkout_send_shipment_info'])
        || YesNo::isFalse($shipment_data['paypal_checkout_send_shipment_info'])
    ) {
        return false;
    }

    $carrier = $shipment_data['carrier'];
    $tracking_number = $shipment_data['tracking_number'];
    $paypal_capture_id = $order_info['payment_info']['paypal_checkout.capture_id'] ?? null;
    $payment_id = $order_info['payment_id'];
    $processor_params = $order_info['payment_method']['processor_params'];
    $paypal_order_id = $order_info['payment_info']['paypal_checkout.order_id'] ?? null;
    $notify_payer = $force_notification[UserTypes::CUSTOMER] ?? false;

    if (empty($paypal_order_id)) {
        fn_set_notification(NotificationSeverity::ERROR, __('error'), __('paypal_checkout.shipment_info_was_not_sent.no_order_id'));

        return false;
    }
    if (empty($tracking_number)) {
        fn_set_notification(NotificationSeverity::ERROR, __('error'), __('paypal_checkout.shipment_info_was_not_sent.tracking_number_empty'));

        return false;
    }
    if (empty($carrier)) {
        fn_set_notification(NotificationSeverity::ERROR, __('error'), __('paypal_checkout.shipment_info_was_not_sent.carrier_empty'));

        return false;
    }
    if (empty($paypal_capture_id)) {
        fn_set_notification(NotificationSeverity::ERROR, __('error'), __('paypal_checkout.shipment_info_was_not_sent.no_capture_id'));

        return false;
    }

    $processor_factory = ServiceProvider::getProcessorFactory();
    $processor = $processor_factory->getByPaymentId(
        $payment_id,
        $processor_params
    );

    $result = $processor->addTracking(
        $paypal_order_id,
        $tracking_number,
        $paypal_capture_id,
        'OTHER',
        $carrier,
        $notify_payer
    );

    if ($result->isFailure()) {
        $result->showNotifications();
        return false;
    }

    return true;
}

/**
 * The "api_update_shipment_pre" hook handler.
 *
 * Actions performed:
 * - Adds "paypal_checkout_send_shipment_info" params to a valid list
 *
 * @param int                       $id                  Shipment id
 * @param array<string, string>     $params              Update params
 * @param array<string, string|int> $data                Response data
 * @param int                       $status              Response status code
 * @param bool                      $valid_params        True if params valid or false otherwise
 * @param array<string>             $allowed_params_list Array of allowed params
 *
 * @return void
 */
function fn_paypal_checkout_api_update_shipment_pre($id, array $params, array $data, $status, $valid_params, array &$allowed_params_list)
{
    $allowed_params_list[] = 'paypal_checkout_send_shipment_info';
}
