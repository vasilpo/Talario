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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'delete' || $mode === 'm_delete') {
        if (!empty($_REQUEST['order_id'])) {
            $data_orders = db_get_array('SELECT * FROM ?:rus_sdek2_register WHERE order_id =?i', $_REQUEST['order_id']);

            foreach ($data_orders as $_order) {
                $shipment_id = $_order['shipment_id'];
                [$shipments,] = fn_get_shipments_info(['advanced_info' => true, 'shipment_id' => $shipment_id]);
                $shipment = reset($shipments);

                $shipping_data = fn_get_shipping_info($shipment['shipping_id'], DESCR_SL);

                /** @var \Tygh\Addons\RusSdek2\Services\SdekService $sdek_service */
                $sdek_service = Tygh::$app['addons.rus_sdek2.sdek_service'];

                $order_uuid = $sdek_service->getSdekOrderUUIDByTrackingNumber($shipment['tracking_number']);

                if (!empty($order_uuid)) {
                    try {
                        $sdek_client = new SdekApiClient($shipping_data['service_params']);

                        $sdek_client->deleteOrder($order_uuid);
                    } catch (Exception $e) {
                        fn_set_notification(NotificationSeverity::ERROR, __('error'), $e->getMessage());
                    }
                }

                $param_search = db_quote(' WHERE order_id = ?i AND shipment_id = ?i ', $_REQUEST['order_id'], $shipment_id);
                db_query('DELETE FROM ?:rus_sdek2_products ?p ', $param_search);
                db_query('DELETE FROM ?:rus_sdek2_register ?p ', $param_search);
                db_query('DELETE FROM ?:rus_sdek2_status ?p ', $param_search);
                db_query('DELETE FROM ?:rus_sdek2_history_status ?p ', $param_search);
                db_query('DELETE FROM ?:rus_sdek2_call_recipient ?p ', $param_search);
                db_query('DELETE FROM ?:rus_sdek2_call_courier ?p ', $param_search);
            }
        }

        return [CONTROLLER_STATUS_OK, fn_url('orders.manage')];
    }
}
