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
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $params */
$params = $_REQUEST;

/** @var \Tygh\Addons\RusSdek2\Services\SdekService $sdek_service */
$sdek_service = Tygh::$app['addons.rus_sdek2.sdek_service'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $suffix = '.manage';

    if ($mode === 'manage') {
        $post_fix = '';
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

        $suffix = '.manage' . $post_fix;
    }

    return [CONTROLLER_STATUS_OK, 'sdek_status' . $suffix];
}

if ($mode === 'manage') {
    // phpcs:ignore
    $t_date = date("Y-m-d", TIME);
    $shipping = db_get_array(
        'SELECT b.service_params FROM ?:shipping_services as a LEFT JOIN ?:shippings as b ON a.service_id = b.service_id WHERE a.module = ?s',
        'sdek2'
    );
    $data_status = $data = [];

    $data['period'] = !empty($params['period']) ? $params['period'] : 'A';
    [$data['time_from'], $data['time_to']] = fn_create_periods($_REQUEST);
    if ($data['period'] === 'A') {
        // phpcs:ignore
        $data['time_from'] = date("Y-01-1 00:00:00");
        // phpcs:ignore
        $data['time_to'] = date("Y-m-d 23:59:59", $data['time_to']);
    } else {
        // phpcs:ignore
        $data['time_from'] = date("Y-m-d 00:00:00", $data['time_from']);
        // phpcs:ignore
        $data['time_to'] = date("Y-m-d 23:59:59", $data['time_to']);
    }

    foreach ($shipping as $d_shipping) {
        $service_params = unserialize($d_shipping['service_params']);
        if (!empty($service_params['authlogin']) && !empty($service_params['authpassword'])) {
            $shipping_params = [
                'Account' => $service_params['authlogin'],
                'Secure' => md5($t_date . '&' . $service_params['authpassword']),
                'Date' => $t_date,
                'ChangePeriod' => [
                    'DateFirst' => $data['time_from'],
                    'DateLast' => $data['time_to']
                ]
            ];

            $d_status = $sdek_service->orderStatus($shipping_params);
            $sdek_service->addStatusOrders($d_status);
        } else {
            fn_set_notification(NotificationSeverity::ERROR, __('notice'), __('rus_sdek2.account_password_error'));
        }
    }

    $params['time_from'] = $data['time_from'];
    $params['time_to'] = $data['time_to'];
    $data['time_from'] = strtotime((string) $data['time_from']);
    $data['time_to'] = strtotime((string) $data['time_to']);
    [$data_status, $search] = $sdek_service->getStatus($params, Registry::get('settings.Appearance.admin_elements_per_page'));
    Tygh::$app['view']->assign('period', $data);
    Tygh::$app['view']->assign('data_status', $data_status);
    Tygh::$app['view']->assign('search', $search);
}
