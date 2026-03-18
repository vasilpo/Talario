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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'export_to_iif') {

        foreach ($_REQUEST['order_ids'] as $k => $v) {
            $orders[$k] = fn_get_order_info($v);
        }

        $order_certificates = array();

        foreach ($orders as $k => $v) {
            if (!empty($v['gift_certificates'])) {
                foreach ($v['gift_certificates'] as $key => $value) {
                    $order_certificates[$value['gift_cert_code']] = $value;
                }
            }
        }

        Tygh::$app['view']->assign('order_certificates', $order_certificates);
    }
}
