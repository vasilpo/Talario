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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_define('GC_PRODUCTS_PER_PAGE', 5);

if ($mode == 'print') {

    $order_info = fn_get_order_info($_REQUEST['order_id']);

    if (isset($order_info['gift_certificates'][$_REQUEST['gift_cert_cart_id']])) {

        $stored_products = array();
        if (!empty($order_info['products'])) {
            foreach ($order_info['products'] as $id => $v) {
                if (isset($v['extra']['parent']['certificate']) && $v['extra']['parent']['certificate'] == $_REQUEST['gift_cert_cart_id']) {
                    $stored_products[$id] = $v;
                }
            }
        }

        echo(fn_show_postal_card($order_info['gift_certificates'][$_REQUEST['gift_cert_cart_id']], $stored_products));
        exit;
    }
}
