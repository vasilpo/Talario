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

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'details') {
    $params = $_REQUEST;
    if (!empty($params['order_id'])) {
        $data_status = [];
        list($_shipments) = fn_get_shipments_info(['order_id' => $params['order_id'], 'advanced_info' => true]);
        if (!empty($_shipments)) {
            foreach ($_shipments as $key => $shipment) {
                if ($shipment['carrier'] === 'sdek2') {
                    $d_status = db_get_array(
                        'SELECT a.* FROM ?:rus_sdek2_status as a WHERE a.order_id = ?i AND a.shipment_id = ?i',
                        $params['order_id'],
                        $shipment['shipment_id']
                    );

                    if (!empty($d_status)) {
                        $data_status[$key] = $d_status;
                    }
                }
            }

            if (!empty($data_status)) {
                Tygh::$app['view']->assign('data_status', $data_status);
                $navigation_tabs = Registry::get('navigation.tabs');
                $navigation_tabs['sdek_information'] = [
                    'title' => __('shipping_information'),
                    'js' => true,
                    'href' => 'orders.details?order_id=' . $params['order_id'] . '&selected_section=sdek_information'
                ];
                Registry::set('navigation.tabs', $navigation_tabs);
            }
        }
    }
}
