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

use Tygh\Addons\RusSdek2\Shippings\SdekApiClient;
use Tygh\Enum\NotificationSeverity;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $params */
$params = $_REQUEST;

if ($mode === 'delete' || $mode === 'm_delete') {
    if (!empty($params['shipment_ids'])) {
        foreach ($params['shipment_ids'] as $shipment_id) {
            [$shipments,] = fn_get_shipments_info(['advanced_info' => true, 'shipment_id' => $shipment_id]);
            $shipment = reset($shipments);

            if ($shipment['carrier'] === 'sdek2') {
                $shipping_data = fn_get_shipping_info($shipment['shipping_id'], DESCR_SL);

                /** @var \Tygh\Addons\RusSdek2\Services\SdekService $sdek_service */
                $sdek_service = Tygh::$app['addons.rus_sdek2.sdek_service'];

                $order_uuid = $sdek_service->getSdekOrderUUIDByTrackingNumber($shipment['tracking_number']);

                if (!empty($order_uuid)) {
                    try {
                        $sdek_client = new SdekApiClient($shipping_data['service_params']);

                        $deleted_order_uuid = $sdek_client->deleteOrder($order_uuid);

                        if (!empty($deleted_order_uuid)) {
                            $param_search = db_quote(
                                ' WHERE order_id = ?i AND shipment_id = ?i ',
                                $shipment['order_id'],
                                $shipment_id
                            );
                            db_query('DELETE FROM ?:rus_sdek2_products ?p ', $param_search);
                            db_query('DELETE FROM ?:rus_sdek2_register ?p ', $param_search);
                            db_query('DELETE FROM ?:rus_sdek2_status ?p ', $param_search);
                            db_query('DELETE FROM ?:rus_sdek2_history_status ?p ', $param_search);
                            db_query('DELETE FROM ?:rus_sdek2_call_recipient ?p ', $param_search);
                            db_query('DELETE FROM ?:rus_sdek2_call_courier ?p ', $param_search);
                        }
                    } catch (Exception $e) {
                        fn_set_notification(NotificationSeverity::ERROR, __('error'), $e->getMessage());
                    }
                }
            }
        }
    }

    $sdek_history = db_get_array('SELECT COUNT(*) FROM ?:rus_sdek2_history_status');
    if (empty($sdek_history)) {
        return [CONTROLLER_STATUS_OK, 'shipments.manage'];
    }
}

if ($mode === 'details') {
    if (!empty($_REQUEST['shipment_id'])) {
        $shipment_id = $_REQUEST['shipment_id'];
        [$shipments,] = fn_get_shipments_info(['advanced_info' => true, 'shipment_id' => $shipment_id]);

        $shipment = reset($shipments);

        if ($shipment['carrier'] !== 'sdek2') {
            return;
        }

        $data_call_recipients = db_get_array(
            'SELECT * FROM ?:rus_sdek2_call_recipient WHERE order_id = ?i and shipment_id = ?i',
            $shipment['order_id'],
            $shipment['shipment_id']
        );

        $data_call_couriers = db_get_array(
            'SELECT * FROM ?:rus_sdek2_call_courier WHERE order_id = ?i and shipment_id = ?i',
            $shipment['order_id'],
            $shipment['shipment_id']
        );

        $data_call_recipient = [];
        if (!empty($data_call_recipients)) {
            $data_call_recipient = reset($data_call_recipients);

            if (!empty($data_call_recipient['time_from']) && !empty($data_call_recipient['time_to'])) {
                $data_call_recipient['period'] = __(
                    'rus_sdek2.time_work_period',
                    ['[time_from]' => $data_call_recipient['time_from'], '[time_to]' => $data_call_recipient['time_to']]
                );
            }
        }

        $data_call_courier = [];
        if (!empty($data_call_couriers)) {
            $data_call_courier = reset($data_call_couriers);

            if (!empty($data_call_courier['intake_time_from']) && !empty($data_call_courier['intake_time_to'])) {
                $data_call_courier['period'] = __(
                    'rus_sdek2.time_work_period',
                    ['[time_from]' => $data_call_courier['intake_time_from'], '[time_to]' => $data_call_courier['intake_time_to']]
                );
            }

            if (!empty($data_call_courier['lunch_time_from']) && !empty($data_call_courier['lunch_time_to'])) {
                $data_call_courier['period_lunch'] = __(
                    'rus_sdek2.time_lunch_period',
                    ['[lunch_time_from]' => $data_call_courier['lunch_time_from'], '[lunch_time_to]' => $data_call_courier['lunch_time_to']]
                );
            }
        }

        $status = db_get_row(
            'SELECT status, city, timestamp FROM ?:rus_sdek2_history_status'
            . ' WHERE order_id = ?i AND shipment_id = ?i'
            . ' ORDER BY timestamp DESC',
            $shipment['order_id'],
            $shipment['shipment_id']
        );

        $data_shipment = db_get_row(
            'SELECT * FROM ?:rus_sdek2_register WHERE order_id = ?i AND shipment_id = ?i',
            $shipment['order_id'],
            $shipment['shipment_id']
        );

        if (!empty($data_shipment)) {
            Tygh::$app['view']->assign('sdek_shipment_created', true);
        }

        Tygh::$app['view']->assign('data_call_recipient', $data_call_recipient);
        Tygh::$app['view']->assign('data_call_courier', $data_call_courier);
        Tygh::$app['view']->assign('order_id', $shipment['order_id']);
        Tygh::$app['view']->assign('status', $status);
    }
}
