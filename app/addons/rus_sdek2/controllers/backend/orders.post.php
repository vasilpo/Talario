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
use Tygh\Addons\RusTaxes\Receipt\Item as ReceiptItem;
use Tygh\Common\OperationResult;
use Tygh\Addons\RusSdek2\Enum\DeliveryPointType;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\SiteArea;
use Tygh\Enum\YesNo;
use Tygh\Registry;
use Tygh\Shippings\Shippings;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $params */
$params = $_REQUEST;

$sdek_delivery = fn_get_schema('sdek', 'sdek_delivery', 'php', true);

/** @var \Tygh\Addons\RusSdek2\Services\SdekService $sdek_service */
$sdek_service = Tygh::$app['addons.rus_sdek2.sdek_service'];

/** @var \Tygh\Addons\RusSdek2\Services\SdekApiDataBuilder $sdek_api_data_builder */
$sdek_api_data_builder = Tygh::$app['addons.rus_sdek2.sdek_api_data_builder'];

// phpcs:ignore
$calendar_format = "d/m/Y";
if (Registry::get('settings.Appearance.calendar_date_format') === 'month_first') {
    // phpcs:ignore
    $calendar_format = "m/d/Y";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($params['order_id'])) {
        $order_info = fn_get_order_info($params['order_id'], false, true, true, true);
    } else {
        return [CONTROLLER_STATUS_OK, 'orders.manage'];
    }

    if ($mode === 'sdek_order_delivery') {
        if (empty($params['add_sdek_info'])) {
            return false;
        }

        /** @var \Tygh\Addons\RusTaxes\ReceiptFactory $receipt_factory */
        $receipt_factory = Tygh::$app['addons.rus_taxes.receipt_factory'];
        $receipt = $receipt_factory->createReceiptFromOrder(
            $order_info,
            CART_PRIMARY_CURRENCY,
            true,
            [ReceiptItem::TYPE_PRODUCT, ReceiptItem::TYPE_SURCHARGE, ReceiptItem::TYPE_SHIPPING]
        );

        foreach ($params['add_sdek_info'] as $shipment_id => $sdek_info) {
            [$_shipments,] = fn_get_shipments_info(
                ['order_id' => $params['order_id'], 'advanced_info' => true, 'shipment_id' => $shipment_id]
            );
            $shipment = reset($_shipments);

            $order_for_sdek = $sdek_api_data_builder->prepareCreateOrderData($order_info, $shipment, $sdek_info['order'], $receipt);

            $shipping_data = fn_get_shipping_info($shipment['shipping_id'], DESCR_SL);

            try {
                $sdek_client = new SdekApiClient($shipping_data['service_params']);

                $order_uuid = $sdek_client->createOrder($order_for_sdek);

                if (!empty($order_uuid)) {
                    $max_retries = 5;
                    $retries = 0;

                    while ($retries < $max_retries) {
                        try {
                            $cdek_number = $sdek_client->getSdekOrderTrackingNumber($order_uuid);
                        } catch (Exception $e) {
                            if ($max_retries - 1 === $retries) {
                                throw new Exception($e->getMessage());
                            }
                        }

                        if (!empty($cdek_number)) {
                            break;
                        }

                        $retries++;
                    }

                    if (!empty($cdek_number)) {
                        // Create schedule
                        if (!empty($sdek_info['schedule']['time_from']) && !empty($sdek_info['schedule']['time_to'])) {
                            $schedule = $sdek_api_data_builder->prepareCreateScheduleData(
                                $sdek_info['schedule'],
                                $sdek_info['order'],
                                $cdek_number,
                                $order_uuid
                            );

                            try {
                                $schedule_uuid = $sdek_client->createSchedule($schedule);

                                if (!empty($schedule_uuid)) {
                                    if (!empty($sdek_info['schedule']['delivery_recipient_cost'])) {
                                        $recipient_cost = $sdek_info['schedule']['delivery_recipient_cost'];
                                        unset($sdek_info['schedule']['delivery_recipient_cost']);
                                    }

                                    $call_recipient = $schedule;
                                    $call_recipient['order_id'] = $params['order_id'];
                                    $call_recipient['shipment_id'] = $shipment_id;
                                    $call_recipient['timestamp'] = TIME;
                                    $call_recipient['recipient_name'] = $sdek_info['schedule']['recipient_name'];
                                    $call_recipient['phone'] = $sdek_info['schedule']['phone'];
                                    $call_recipient['recipient_cost'] = $recipient_cost ?? '';

                                    if (
                                        !empty($sdek_delivery[$sdek_info['order']['tariff_code']]['terminals'])
                                        && !YesNo::toBool($sdek_delivery[$sdek_info['order']['tariff_code']]['terminals'])
                                    ) {
                                        $call_recipient['address'] = $sdek_info['order']['to_location']['address'];
                                    } else {
                                        $call_recipient['delivery_point'] = $sdek_info['order']['delivery_point'];
                                    }
                                }
                            } catch (Exception $e) {
                                fn_set_notification(NotificationSeverity::ERROR, __('error'), $e->getMessage());
                            }
                        }

                        // Create call courier
                        if (
                            !empty($sdek_info['call_courier']['intake_date'])
                            && !empty($sdek_info['call_courier']['intake_time_from'])
                            && !empty($sdek_info['call_courier']['intake_time_to'])
                        ) {
                            $sdek_info['call_courier']['intake_date'] = DateTime::createFromFormat(
                                $calendar_format,
                                $sdek_info['call_courier']['intake_date']
                            )->format('Y-m-d');

                            $intake_data = $sdek_api_data_builder->prepareCreateCallCourierData($sdek_info['call_courier'], $cdek_number);

                            try {
                                $call_courier_uuid = $sdek_client->createCallCourier($intake_data);

                                if (!empty($call_courier_uuid)) {
                                    $call_courier = [
                                        'order_id' => $params['order_id'],
                                        'shipment_id' => $shipment_id,
                                        'timestamp' => TIME,
                                        'intake_date' => $sdek_info['call_courier']['intake_date'],
                                        'intake_time_from' => $sdek_info['call_courier']['intake_time_from'],
                                        'intake_time_to' => $sdek_info['call_courier']['intake_time_to'],
                                        'lunch_time_from' => $sdek_info['call_courier']['lunch_time_from'],
                                        'lunch_time_to' => $sdek_info['call_courier']['lunch_time_to'],
                                        'comment' => $sdek_info['call_courier']['comment'],
                                    ];
                                }
                            } catch (Exception $e) {
                                fn_set_notification(NotificationSeverity::ERROR, __('error'), $e->getMessage());
                            }
                        }

                        $register_data = [
                            'order_id' => $params['order_id'],
                            'shipment_id' => $shipment_id,
                            'sdek_order_uuid' => $order_uuid,
                            'sdek_number' => $cdek_number,
                            // phpcs:ignore
                            'data' => date("Y-m-d", $shipment['shipment_timestamp']),
                            'data_json' => json_encode($order_for_sdek),
                            'timestamp' => TIME,
                            'status' => 'S',
                            'tariff_code' => $sdek_info['order']['tariff_code'],
                            'file_sdek' => $shipment_id . '/' . $params['order_id'] . '.pdf',
                            'notes' => $sdek_info['order']['comment'],
                            'use_product' => YesNo::NO,
                            'use_imposed' => '',
                            'barcode' => '',
                            'cash_delivery' => '0.00',
                        ];

                        db_query('UPDATE ?:shipments SET tracking_number = ?s WHERE shipment_id = ?i', $cdek_number, $shipment_id);

                        $force_notification = fn_get_notification_rules($params);

                        $shipment = [
                            'shipment_id'     => $shipment_id,
                            'timestamp'       => $shipment['shipment_timestamp'],
                            'shipping'        => db_get_field(
                                'SELECT shipping FROM ?:shipping_descriptions WHERE shipping_id = ?i AND lang_code = ?s',
                                $shipment['shipping_id'],
                                $order_info['lang_code']
                            ),
                            'tracking_number' => $cdek_number,
                            'carrier_info'    => Shippings::getCarrierInfo($shipment['carrier'], $cdek_number),
                            'comments'        => $shipment['comments'],
                            'products'        => $shipment['products'],
                        ];

                        /** @var \Tygh\Notifications\EventDispatcher $event_dispatcher */
                        $event_dispatcher = Tygh::$app['event.dispatcher'];

                        /** @var \Tygh\Notifications\Settings\Factory $notification_settings_factory */
                        $notification_settings_factory = Tygh::$app['event.notification_settings.factory'];
                        $notification_rules = $notification_settings_factory->create($force_notification);

                        $event_dispatcher->dispatch('order.shipment_updated', ['shipment' => $shipment, 'order_info' => $order_info], $notification_rules);

                        fn_set_notification(NotificationSeverity::NOTICE, __('notice'), __('rus_sdek2.shipment_create_success'));

                        if (
                            !empty($sdek_delivery[$sdek_info['order']['tariff_code']]['terminals'])
                            && !YesNo::toBool($sdek_delivery[$sdek_info['order']['tariff_code']]['terminals'])
                        ) {
                            $register_data['address'] = $sdek_info['order']['to_location']['address'];
                        } else {
                            $register_data['delivery_point'] = $sdek_info['order']['delivery_point'];
                        }

                        if (isset($sdek_info['use_product']) && !YesNo::toBool($sdek_info['use_product'])) {
                            $register_data['use_product'] = YesNo::YES;
                        }

                        if (isset($sdek_info['use_imposed']) && !YesNo::toBool($sdek_info['use_imposed'])) {
                            $register_data['use_imposed'] = YesNo::YES;
                        }

                        if (!empty($sdek_info['cash_delivery'])) {
                            $register_data['cash_delivery'] = $sdek_info['cash_delivery'];
                        }

                        $register_id = db_get_field(
                            'SELECT register_id FROM ?:rus_sdek2_register WHERE order_id = ?i AND shipment_id = ?i ',
                            $params['order_id'],
                            $shipment_id
                        );

                        if (empty($register_id)) {
                            $register_id = db_query('INSERT INTO ?:rus_sdek2_register ?e', $register_data);
                        } else {
                            db_query('UPDATE ?:rus_sdek2_register SET ?u WHERE register_id = ?i', $register_data, $register_id);
                        }

                        $packages = $order_for_sdek->packages;
                        foreach ($packages as $package) {
                            $package_items = $package->items;

                            foreach ($package_items as $package_item) {
                                $sdek_product = [
                                    'register_id' => $register_id,
                                    'order_id' => $params['order_id'],
                                    'shipment_id' => $shipment_id,
                                    'ware_key' => $package_item->ware_key,
                                    'product' => $package_item->name,
                                    'price' => $package_item->cost,
                                    'amount' => $package_item->amount,
                                    'weight' => $package_item->weight
                                ];

                                db_query('INSERT INTO ?:rus_sdek2_products ?e', $sdek_product);
                            }
                        }

                        if (!empty($call_recipient)) {
                            db_query('INSERT INTO ?:rus_sdek2_call_recipient ?e', $call_recipient);
                        }

                        if (!empty($call_courier)) {
                            db_query('INSERT INTO ?:rus_sdek2_call_courier ?e', $call_courier);
                        }
                    }
                }
            } catch (Exception $e) {
                fn_set_notification(NotificationSeverity::ERROR, __('error'), $e->getMessage());
            }
        }
    } elseif ($mode === 'sdek_order_status') {
        if (empty($params['shipment_id'])) {
            return [CONTROLLER_STATUS_OK, 'orders.manage'];
        }

        [$_shipments] = fn_get_shipments_info(['order_id' => $params['order_id'], 'advanced_info' => true, 'shipment_id' => $params['shipment_id']]);
        $shipment = reset($_shipments);

        $shipping_data = fn_get_shipping_info($shipment['shipping_id']);

        if ($shipping_data['service_params']) {
            $date_status = $sdek_service->orderStatus($shipping_data['service_params'], $params['order_id'], $params['shipment_id']);
            $sdek_service->addStatusOrders($date_status);
        }

        fn_set_notification(NotificationSeverity::NOTICE, __('notice'), __('rus_sdek2.shipment_status_updated'));
    } elseif ($mode === 'call_sdek') {
        foreach ($params['add_sdek_info'] as $shipment_id => $sdek_info) {
            [$_shipments] = fn_get_shipments_info(['order_id' => $params['order_id'], 'advanced_info' => true, 'shipment_id' => $shipment_id]);
            $shipment = reset($_shipments);
            $shipping_data = fn_get_shipping_info($shipment['shipping_id'], DESCR_SL);

            if (
                $shipment['tracking_number']
                && (
                    !empty($sdek_info['schedule']['date'])
                    || !empty($sdek_info['schedule']['time_from'])
                    || !empty($sdek_info['schedule']['time_to'])
                )
            ) {
                try {
                    $sdek_client = new SdekApiClient($shipping_data['service_params']);

                    $schedule = $sdek_api_data_builder->prepareCreateScheduleData(
                        $sdek_info['schedule'],
                        $sdek_info['order'],
                        $shipment['tracking_number']
                    );

                    $schedule_uuid = $sdek_client->createSchedule($schedule);

                    if (!empty($schedule_uuid)) {
                        $call_recipient = $schedule;
                        $call_recipient['order_id'] = $params['order_id'];
                        $call_recipient['shipment_id'] = $shipment_id;
                        $call_recipient['timestamp'] = TIME;
                        // FIXME: what are these params for?
                        $call_recipient['recipient_name'] = $sdek_info['schedule']['recipient_name'];
                        $call_recipient['phone'] = $sdek_info['schedule']['phone'];
                        $call_recipient['recipient_cost'] = '0.00';

                        if (
                            !empty($sdek_delivery[$sdek_info['order']['tariff_code']]['terminals'])
                            && !YesNo::toBool($sdek_delivery[$sdek_info['order']['tariff_code']]['terminals'])
                        ) {
                            $call_recipient['address'] = $sdek_info['order']['to_location']['address'];
                        } else {
                            $call_recipient['delivery_point'] = $sdek_info['order']['delivery_point'];
                        }

                        if (!empty($sdek_info['schedule']['delivery_recipient_cost'])) {
                            $call_recipient['recipient_cost'] = $sdek_info['schedule']['delivery_recipient_cost'];
                        }

                        $call_recipient = array_diff($call_recipient, ['', '0.00']);
                        $call_id = db_get_field(
                            'SELECT call_id FROM ?:rus_sdek2_call_recipient WHERE order_id = ?i AND shipment_id =?i ',
                            $params['order_id'],
                            $shipment_id
                        );
                        if (!empty($call_id)) {
                            db_query(
                                'UPDATE ?:rus_sdek2_call_recipient SET ?u WHERE order_id = ?i AND shipment_id =?i ',
                                $call_recipient,
                                $params['order_id'],
                                $shipment_id
                            );
                        } else {
                            db_query('INSERT INTO ?:rus_sdek2_call_recipient ?e', $call_recipient);
                        }
                    }

                    $date_status = $sdek_service->orderStatus($shipping_data['service_params'], $params['order_id'], $shipment_id);

                    $sdek_service->addStatusOrders($date_status);
                } catch (Exception $e) {
                    fn_set_notification(NotificationSeverity::ERROR, __('error'), $e->getMessage());
                }
            }

            if (
                $shipment['tracking_number']
                && (
                    !empty($sdek_info['call_courier']['intake_date'])
                    && !empty($sdek_info['call_courier']['intake_time_from'])
                    && !empty($sdek_info['call_courier']['intake_time_to'])
                )
            ) {
                try {
                    $sdek_client = new SdekApiClient($shipping_data['service_params']);

                    $sdek_info['call_courier']['intake_date'] = DateTime::createFromFormat(
                        $calendar_format,
                        $sdek_info['call_courier']['intake_date']
                    )->format('Y-m-d');

                    $intake_data = $sdek_api_data_builder->prepareCreateCallCourierData($sdek_info['call_courier'], $shipment['tracking_number']);

                    $call_courier_uuid = $sdek_client->createCallCourier($intake_data);

                    if (!empty($call_courier_uuid)) {
                        $total_weight = db_get_field(
                            'SELECT SUM(weight) FROM ?:rus_sdek2_products WHERE order_id = ?i AND shipment_id = ?i',
                            $params['order_id'],
                            $shipment_id
                        );
                        if (empty($total_weight)) {
                            $total_weight = SDEK2_DEFAULT_WEIGHT;
                        }

                        $call_courier = [
                            'order_id' => $params['order_id'],
                            'shipment_id' => $shipment_id,
                            'timestamp' => TIME,
                            'intake_date' => $sdek_info['call_courier']['intake_date'],
                            'intake_time_from' => $sdek_info['call_courier']['intake_time_from'],
                            'intake_time_to' => $sdek_info['call_courier']['intake_time_to'],
                            'weight' => $total_weight,
                        ];

                        $call_courier['lunch_time_from'] = !empty($sdek_info['call_courier']['lunch_time_from'])
                            ? $sdek_info['call_courier']['lunch_time_from']
                            : '';
                        $call_courier['lunch_time_to'] = !empty($sdek_info['call_courier']['lunch_time_to']) ? $sdek_info['call_courier']['lunch_time_to'] : '';
                        $call_courier['comment'] = !empty($sdek_info['call_courier']['comment']) ? $sdek_info['call_courier']['comment'] : '';

                        db_query('INSERT INTO ?:rus_sdek2_call_courier ?e', $call_courier);
                    }

                    $date_status = $sdek_service->orderStatus($shipping_data['service_params'], $params['order_id'], $shipment_id);

                    $sdek_service->addStatusOrders($date_status);
                } catch (Exception $e) {
                    fn_set_notification(NotificationSeverity::ERROR, __('error'), $e->getMessage());
                }
            }
        }
    }

    if ($mode === 'update_details') {
        $order_info = fn_get_order_info($params['order_id'], false, true, true);
        $force_notification = fn_get_notification_rules($params);

        if (!empty($params['update_shipping'])) {
            foreach ($params['update_shipping'] as $shipping) {
                foreach ($shipping as $shipment_id => $shipment_data) {
                    if ($shipment_data['carrier'] === 'sdek2' && !empty($shipment_id)) {
                        $d_shipment = db_get_row('SELECT * FROM ?:shipments WHERE shipment_id = ?i ', $shipment_id);
                        $products = db_get_hash_array(
                            'SELECT item_id, amount FROM ?:shipment_items WHERE order_id = ?i AND shipment_id = ?i ',
                            'item_id',
                            $params['order_id'],
                            $shipment_id
                        );

                        foreach ($products as $item_id => $product) {
                            $shipment_data['products'][$item_id] = $product['amount'];
                        }

                        $shipment = [
                            'shipment_id'     => $shipment_id,
                            'timestamp'       => $d_shipment['timestamp'],
                            'shipping'        => db_get_field(
                                'SELECT shipping FROM ?:shipping_descriptions WHERE shipping_id = ?i AND lang_code = ?s',
                                $d_shipment['shipping_id'],
                                $order_info['lang_code']
                            ),
                            'tracking_number' => $shipment_data['tracking_number'],
                            'carrier'         => $shipment_data['carrier'],
                            'comments'        => $d_shipment['comments'],
                            'items'           => $shipment_data['products'],
                        ];

                        /** @var \Tygh\Notifications\EventDispatcher $event_dispatcher */
                        $event_dispatcher = Tygh::$app['event.dispatcher'];

                        /** @var \Tygh\Notifications\Settings\Factory $notification_settings_factory */
                        $notification_settings_factory = Tygh::$app['event.notification_settings.factory'];
                        $notification_rules = $notification_settings_factory->create($force_notification);

                        $event_dispatcher->dispatch('order.shipment_updated', ['shipment' => $shipment, 'order_info' => $order_info], $notification_rules);
                    }
                }
            }
        }
    }

    $url = fn_url('orders.details&order_id=' . $params['order_id'], SiteArea::ADMIN_PANEL);
    if (defined('AJAX_REQUEST') && !empty($url)) {
        Registry::get('ajax')->assign('force_redirection', $url);
        exit;
    }

    return [CONTROLLER_STATUS_OK, $url];
}

if ($mode === 'details') {
    $order_info = Tygh::$app['view']->getTemplateVars('order_info');

    $sdek_pvz = false;
    if (!empty($order_info['shipping'])) {
        foreach ($order_info['shipping'] as $shipping) {
            if (($shipping['module'] === 'sdek2') && !empty($shipping['office_id'])) {
                $sdek_pvz = $shipping['office_id'];
            }
        }
    }
    [$all_shipments] = fn_get_shipments_info(['order_id' => $params['order_id'], 'advanced_info' => true]);
    $use_shipments = fn_one_full_shipped($all_shipments);

    if (!empty($all_shipments)) {
        $sdek_shipments = $data_shipments = [];

        foreach ($all_shipments as $_shipment) {
            if ($_shipment['carrier'] === 'sdek2') {
                $sdek_shipments[] = $_shipment;
            }
        }

        if (!empty($sdek_shipments)) {
            $offices = [];
            $location = [
                'country' => !empty($order_info['s_country']) ? $order_info['s_country'] : $order_info['b_country'],
                'state' => !empty($order_info['s_state']) ? $order_info['s_state'] : $order_info['b_state'],
                'city' => !empty($order_info['s_city']) ? $order_info['s_city'] : $order_info['b_city'],
                'zipcode' => !empty($order_info['s_zipcode']) ? $order_info['s_zipcode'] : $order_info['b_zipcode']
            ];

            /** @var \Tygh\Addons\RusSdek2\Services\CitiesService $cities_service */
            $cities_service = Tygh::$app['addons.rus_sdek2.cities_service'];
            $rec_city_code = $cities_service->getCityId($location);

            $lastname = '';
            if (!empty($order_info['lastname'])) {
                $lastname = $order_info['lastname'];
            } elseif (!empty($order_info['s_lastname'])) {
                $lastname = $order_info['s_lastname'];
            } elseif (!empty($order_info['b_lastname'])) {
                $lastname = $order_info['b_lastname'];
            }
            $firstname = '';
            if (!empty($order_info['firstname'])) {
                $firstname = $order_info['firstname'];
            } elseif (!empty($order_info['s_firstname'])) {
                $firstname = $order_info['s_firstname'];
            } elseif (!empty($order_info['b_firstname'])) {
                $firstname = $order_info['b_firstname'];
            }

            $fio = $lastname . ' ' . $firstname;

            $phone = '';
            if (!empty($order_info['phone'])) {
                $phone = $order_info['phone'];
            } elseif (!empty($order_info['s_phone'])) {
                $phone = $order_info['s_phone'];
            } elseif (!empty($order_info['b_phone'])) {
                $phone = $order_info['b_phone'];
            }

            $data_status = [];

            foreach ($sdek_shipments as $shipment) {
                $data_sdek = db_get_row(
                    'SELECT * FROM ?:rus_sdek2_register WHERE order_id = ?i and shipment_id = ?i',
                    $shipment['order_id'],
                    $shipment['shipment_id']
                );

                $data_shipping = fn_get_shipping_info($shipment['shipping_id'], DESCR_SL);

                $module = db_get_field('SELECT module FROM ?:shipping_services WHERE service_id = ?i', $data_shipping['service_id']);

                if (!empty($data_shipping['service_params']) && ($module === 'sdek2')) {
                    if (!empty($data_sdek)) {
                        $cost = 0;

                        if (!empty($data_sdek['data_json'])) {
                            $data_json = json_decode($data_sdek['data_json'], true);

                            $delivery_recipient_cost = $data_json['delivery_recipient_cost']['value'];
                            if (!empty($delivery_recipient_cost)) {
                                $cost = (string) $delivery_recipient_cost;
                            }
                        }

                        if (!empty($cost)) {
                            $data_sdek['delivery_cost'] = $cost;
                        }

                        $data_sdek['comments'] = $data_sdek['notes'];

                        $data_shipments[$shipment['shipment_id']] = $data_sdek;

                        $data_status = db_get_row(
                            'SELECT status, timestamp FROM ?:rus_sdek2_status WHERE order_id = ?i AND shipment_id = ?i ORDER BY timestamp DESC',
                            $params['order_id'],
                            $shipment['shipment_id']
                        );
                    } else {
                        $cost = $order_info['display_shipping_cost'];
                        $order = $order_info;

                        if (!$use_shipments) {
                            $cost = $sdek_service->calculateCostByShipment($order_info, $data_shipping, $shipment);
                            $order['shipping_cost'] = $cost;
                            $order['total'] = $order['total'] - $order_info['display_shipping_cost'] + $cost;
                        }

                        /** @var \Tygh\Addons\RusTaxes\ReceiptFactory $receipt_factory */
                        $receipt_factory = Tygh::$app['addons.rus_taxes.receipt_factory'];
                        $receipt = $receipt_factory->createReceiptFromOrder(
                            $order,
                            CART_PRIMARY_CURRENCY,
                            true,
                            [
                                ReceiptItem::TYPE_PRODUCT,
                                ReceiptItem::TYPE_SURCHARGE,
                                ReceiptItem::TYPE_SHIPPING
                            ]
                        );

                        $shipping_receipt_item = $receipt->getItem(0, ReceiptItem::TYPE_SHIPPING);

                        if ($shipping_receipt_item) {
                            $cost = $shipping_receipt_item->getPrice();
                        }

                        $data_shipments[$shipment['shipment_id']] = [
                            'order_id' => $shipment['order_id'],
                            'comments' => $shipment['comments'],
                            'delivery_cost' => $cost,
                            'tariff_code' => $data_shipping['service_params']['tariff_code'],
                            'delivery_point' => $sdek_pvz,
                        ];
                        $data_shipments[$shipment['shipment_id']]['address'] = !empty($order_info['s_address'])
                            ? $order_info['s_address']
                            : $order_info['b_address'];
                    }

                    $data_shipments[$shipment['shipment_id']]['send_city_code'] = $data_shipping['service_params']['from_city_id'];
                    $data_shipments[$shipment['shipment_id']]['shipping'] = $shipment['shipping'];

                    $data_call_recipients = db_get_row(
                        'SELECT * FROM ?:rus_sdek2_call_recipient WHERE order_id = ?i and shipment_id = ?i',
                        $shipment['order_id'],
                        $shipment['shipment_id']
                    );

                    if (!empty($data_call_recipients)) {
                        $data_shipments[$shipment['shipment_id']]['new_schedules'] = $data_call_recipients;
                        $data_shipments[$shipment['shipment_id']]['new_schedules']['date'] = strtotime($data_call_recipients['date']);
                        $data_shipments[$shipment['shipment_id']]['delivery_point'] = $data_call_recipients['delivery_point'];
                        $data_shipments[$shipment['shipment_id']]['address'] = $data_call_recipients['address'];
                    } else {
                        $data_shipments[$shipment['shipment_id']]['new_schedules'] = [
                            'recipient_name' => $fio,
                            'phone' => $phone,
                            'date' => TIME,
                            'recipient_cost' => '0.00',
                            'time_from' => '',
                            'time_to' => '',
                        ];
                    }

                    $data_call_couriers = db_get_array(
                        'SELECT * FROM ?:rus_sdek2_call_courier WHERE order_id = ?i and shipment_id = ?i ORDER BY timestamp desc',
                        $shipment['order_id'],
                        $shipment['shipment_id']
                    );

                    $data_shipments[$shipment['shipment_id']]['call_couriers'][] = [
                        'date' => TIME,
                    ];

                    if (!empty($data_call_couriers)) {
                        $data_call_couriers = reset($data_call_couriers);
                        $data_call_couriers['date'] = $data_call_couriers['timestamp'];
                        $data_shipments[$shipment['shipment_id']]['call_couriers'] = array_merge(
                            $data_shipments[$shipment['shipment_id']]['call_couriers'],
                            $data_call_couriers
                        );
                    }

                    if (
                        !empty($sdek_delivery[$data_shipping['service_params']['tariff_code']]['terminals'])
                        && YesNo::toBool($sdek_delivery[$data_shipping['service_params']['tariff_code']]['terminals'])
                    ) {
                        $offices = [];
                        $type_terminals = DeliveryPointType::PVZ;
                        if (!empty($sdek_delivery[$data_shipping['service_params']['tariff_code']]['postamat'])) {
                            $type_terminals = DeliveryPointType::POSTAMAT;
                        }

                        if (!empty($rec_city_code)) {
                            try {
                                $sdek_client = new SdekApiClient($data_shipping['service_params']);

                                $offices = $sdek_client->getDeliveryPoints(['city_code' => $rec_city_code, 'type' => $type_terminals]);
                            } catch (Exception $e) {
                                fn_set_notification(NotificationSeverity::ERROR, __('error'), $e->getMessage());
                            }
                        }

                        $data_shipments[$shipment['shipment_id']]['offices'] = $offices;
                    }
                }
            }

            if (!empty($data_shipments)) {
                Tygh::$app['view']->assign('data_shipments', $data_shipments);
                Tygh::$app['view']->assign('sdek_pvz', $sdek_pvz);
                Tygh::$app['view']->assign('rec_city_code', $rec_city_code);
                Tygh::$app['view']->assign('order_id', $params['order_id']);
                Tygh::$app['view']->assign('data_status', $data_status);
            }
        }
    }
} elseif ($mode === 'sdek_get_ticket') {
    $order_id = (int) $params['order_id'];
    $shipment_id = (int) $params['shipment_id'];

    [$shipments,] = fn_get_shipments_info(['order_id' => $order_id, 'advanced_info' => true, 'shipment_id' => $shipment_id]);

    $shipment = reset($shipments);

    $ticket_result = fn_sdek_get_ticket_order($shipment, $order_id, $shipment_id);

    if ($ticket_result->isSuccess()) {
        fn_get_file($ticket_result->getData());
    } else {
        $ticket_result->showNotifications();
    }

    $url = fn_url('shipments.details?shipment_id=' . $shipment_id);

    if (defined('AJAX_REQUEST')) {
        Tygh::$app['ajax']->assign('force_redirection', $url);
        exit;
    }

    return [CONTROLLER_STATUS_OK, $url];
}

/**
 * Requests shipment receipt creation.
 *
 * @param array $shipment    Shipment data
 * @param int   $order_id    Order identifier
 * @param int   $shipment_id Shipment identifier
 *
 * @return \Tygh\Common\OperationResult Receipt creation result.
 *                                      Contains file path in its data
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_sdek_get_ticket_order(array $shipment, $order_id, $shipment_id)
{
    $result = new OperationResult(true);

    $download_file_dir = fn_get_files_dir_path() . '/sdek' . '/' . $shipment_id . '/';
    $download_file_path = $download_file_dir . $order_id . '.pdf';

    if (is_file($download_file_path)) {
        $result->setData($download_file_path);
        return $result;
    }

    $shipping_data = fn_get_shipping_info($shipment['shipping_id'], DESCR_SL);

    /** @var \Tygh\Addons\RusSdek2\Services\SdekService $sdek_service */
    $sdek_service = Tygh::$app['addons.rus_sdek2.sdek_service'];

    $order_uuid = $sdek_service->getSdekOrderUUIDByTrackingNumber($shipment['tracking_number']);

    if (empty($order_uuid)) {
        $result->setSuccess(false);
        $result->addError(0, __('rus_sdek2.order_uuid_not_found_by_tracking_number'));
    } else {
        try {
            $sdek_client = new SdekApiClient($shipping_data['service_params']);

            $invoice_uuid = $sdek_client->createInvoice($order_uuid);

            if (!empty($invoice_uuid)) {
                $max_retries = 5;
                $retries = 0;

                while ($retries < $max_retries) {
                    try {
                        $invoice_body = $sdek_client->downloadInvoice($invoice_uuid);
                    } catch (Exception $e) {
                        if ($max_retries - 1 === $retries) {
                            $result->setSuccess(false);
                            $result->addError(0, $e->getMessage());
                        }
                    }

                    if (!empty($invoice_body)) {
                        break;
                    }

                    // We have to wait until order info is available on SDEK side
                    sleep(3);

                    $retries++;
                }

                if (empty($invoice_body)) {
                    $result->setSuccess(false);
                    $result->addError(0, __('rus_sdek2.empty_receipt_print_response'));
                } else {
                    fn_rm($download_file_dir);
                    fn_mkdir($download_file_dir);

                    fn_put_contents($download_file_path, $invoice_body);
                    $result->setData($download_file_path);
                }
            }
        } catch (Exception $e) {
            $result->setSuccess(false);
            $result->addError(0, $e->getMessage());
        }
    }

    return $result;
}
