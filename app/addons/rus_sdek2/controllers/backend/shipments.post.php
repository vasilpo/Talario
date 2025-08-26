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

use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $params */
$params = $_REQUEST;

/** @var \Tygh\Addons\RusSdek2\Services\SdekService $sdek_service */
$sdek_service = Tygh::$app['addons.rus_sdek2.sdek_service'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'sdek_delivery') {
        $post_fix = '';
        if (!empty($params['sdek_order_id'])) {
            $post_fix .= '&sdek_order_id=' . $params['sdek_order_id'];
        }
        if (!empty($params['period'])) {
            $post_fix .= '&period=' . $params['period'];
        }
        if (!empty($params['time_from'])) {
            $post_fix .= '&time_from=' . $params['time_from'];
        }
        if (!empty($params['time_to'])) {
            $post_fix .= '&time_to=' . $params['time_to'];
        }
        if (!empty($params['status'])) {
            $post_fix .= '&status=' . $params['status'];
        }

        $suffix = '.sdek_delivery' . $post_fix;

        return [CONTROLLER_STATUS_OK, 'shipments' . $suffix];
    }

    if ($mode === 'update_status') {
        if (!empty($params['shipment_ids'])) {
            foreach ($params['shipment_ids'] as $shipment_id) {
                $order_id = $params['sdek_ids'][$shipment_id];

                [$_shipments] = fn_get_shipments_info(['order_id' => $order_id, 'advanced_info' => true, 'shipment_id' => $shipment_id]);
                $shipment = reset($_shipments);

                $shipping_data = fn_get_shipping_info($shipment['shipping_id'], DESCR_SL);
                $date_status = $sdek_service->orderStatus($shipping_data['service_params'], $order_id, $shipment_id);
                $sdek_service->addStatusOrders($date_status);
            }

            fn_set_notification(NotificationSeverity::NOTICE, __('notice'), __('rus_sdek2.text_update_status'));
        }
    }
}

$navigation_sections = Registry::get('navigation.dynamic.sections');
$navigation_sections['shipments'] = [
    'title' => __('shipments'),
    'href' => fn_url('shipments.manage'),
];
$navigation_sections['sdek_delivery'] = [
    'title' => __('rus_sdek2.delivery_orders'),
    'href' => fn_url('shipments.sdek_delivery')
];

if ($sdek_service->deliveryCheckOrders() && $mode !== 'details') {
    Registry::set('navigation.dynamic.sections', $navigation_sections);
}

if ($mode === 'manage') {
    if ($sdek_service->deliveryCheckOrders()) {
        Registry::set('navigation.dynamic.active_section', 'shipments');
        Tygh::$app['view']->assign('shipments_sdek', 'Y');
    }
} elseif ($mode === 'sdek_delivery') {
    $shipping = db_get_array(
        'SELECT b.service_params '
        . 'FROM ?:shipping_services as a '
        . 'LEFT JOIN ?:shippings as b '
            . 'ON a.service_id = b.service_id '
        . 'WHERE a.module = ?s AND a.status = ?s',
        'sdek2',
        ObjectStatuses::ACTIVE
    );
    $data_status = $data = [];

    $data['period'] = !empty($params['period']) ? $params['period'] : 'A';
    [$data['time_from'], $data['time_to']] = fn_create_periods($_REQUEST);
    if ($data['period'] === 'A') {
        $data['time_from'] = date('Y-01-1 00:00:00');
        $data['time_to'] = date('Y-m-d 23:59:59', $data['time_to']);
    } else {
        $data['time_from'] = date('Y-m-d 00:00:00', $data['time_from']);
        $data['time_to'] = date('Y-m-d 23:59:59', $data['time_to']);
    }

    $params['time_from'] = $data['time_from'];
    $params['time_to'] = $data['time_to'];
    $data['time_from'] = strtotime((string) $data['time_from']);
    $data['time_to'] = strtotime((string) $data['time_to']);
    $data['sdek_order_id'] = (!empty($params['sdek_order_id'])) ? $params['sdek_order_id'] : '';
    [$data_status, $search] = $sdek_service->getStatus($params, Registry::get('settings.Appearance.admin_elements_per_page'));

    $sdek_history = db_get_array('SELECT * FROM ?:rus_sdek2_history_status');
    if (empty($sdek_history)) {
        return [CONTROLLER_STATUS_OK, 'shipments.manage'];
    }

    Registry::set('navigation.dynamic.active_section', 'sdek_delivery');

    Tygh::$app['view']->assign('period', $data);
    Tygh::$app['view']->assign('data_status', $data_status);
    Tygh::$app['view']->assign('search', $search);
}

if ($mode === 'update_status') {
    if (!empty($params['data_update'])) {
        foreach ($params['data_update'] as $shipment_id => $order_id) {
            [$_shipments] = fn_get_shipments_info(['order_id' => $order_id, 'advanced_info' => true, 'shipment_id' => $shipment_id]);
            $shipment = reset($_shipments);

            $shipping_data = fn_get_shipping_info($shipment['shipping_id'], DESCR_SL);
            $date_status = $sdek_service->orderStatus($shipping_data['service_params'], $order_id, (int) $shipment_id);
            $sdek_service->addStatusOrders($date_status);
        }

        fn_set_notification(NotificationSeverity::NOTICE, __('notice'), __('rus_sdek2.text_update_status'));
    }

    return [CONTROLLER_STATUS_OK, 'shipments.sdek_delivery'];
}

if ($mode === 'update_all_status') {
    // phpcs:ignore
    $t_date = date("Y-m-d", TIME);

    $data['period'] = !empty($params['period']) ? $params['period'] : 'A';
    [$data['time_from'], $data['time_to']] = fn_create_periods($_REQUEST);
    if ($data['period'] === 'A') {
        $data['time_from'] = date('Y-01-01 00:00:00');
        $data['time_to'] = date('Y-m-d 23:59:59', $data['time_to']);
    } else {
        $data['time_from'] = date('Y-m-d 00:00:00', $data['time_from']);
        $data['time_to'] = date('Y-m-d 23:59:59', $data['time_to']);
    }

    $shipping = db_get_array(
        'SELECT b.service_params '
        . 'FROM ?:shipping_services as a '
        . 'LEFT JOIN ?:shippings as b '
        . 'ON a.service_id = b.service_id '
        . 'WHERE a.module = ?s AND a.status = ?s',
        'sdek2',
        ObjectStatuses::ACTIVE
    );

    foreach ($shipping as $d_shipping) {
        $service_params = unserialize($d_shipping['service_params']);
        if (!empty($service_params['authlogin']) && !empty($service_params['authpassword'])) {
            $shipping_params[$service_params['authlogin']]['Account'] = $service_params['authlogin'];
            $shipping_params[$service_params['authlogin']]['Secure'] = md5($t_date . '&' . $service_params['authpassword']);
            $shipping_params[$service_params['authlogin']]['Date'] = $t_date;
            $shipping_params[$service_params['authlogin']]['ChangePeriod']['DateFirst'] = $data['time_from'];
            $shipping_params[$service_params['authlogin']]['ChangePeriod']['DateLast'] = $data['time_to'];
        }
    }

    foreach ($shipping_params as $data_shipping) {
        $d_status = $sdek_service->orderStatus($data_shipping);
        $sdek_service->addStatusOrders($d_status);
    }

    fn_set_notification(NotificationSeverity::NOTICE, __('notice'), __('rus_sdek2.text_update_status'));

    return [CONTROLLER_STATUS_OK, 'shipments.sdek_delivery'];
}
