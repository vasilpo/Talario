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

$_cart = & Tygh::$app['session']['cart'];

if ((($mode == 'update') && !isset($_REQUEST['is_ajax'])) || ($mode == 'edit')) {
    if (!empty($_cart['order_id'])) {
        $old_ship_data = db_get_field("SELECT data FROM ?:order_data WHERE order_id = ?i AND type = ?s", $_cart['order_id'], 'L');
        if (!empty($old_ship_data)) {
            $old_ship_data = unserialize($old_ship_data);

            foreach($old_ship_data as $group_key => $shipping) {
                if (!empty($shipping['module']) && ($shipping['module'] == 'pickpoint')) {
                    if (!empty($shipping['data']['pickpoint_postamat'])) {
                        $_cart['pickpoint_office'][$shipping['group_key']][$shipping['shipping_id']] = $shipping['data']['pickpoint_postamat'];
                    }

                    Tygh::$app['view']->assign('pickpoint_postamat', $_cart['pickpoint_office']);
                }
            }
        }
    }
}

if ($mode == 'update_shipping') {
    if (!empty($_REQUEST['pickpoint_id']) && !empty($_REQUEST['shipping_id'])) {
        $shipping_id = $_REQUEST['shipping_id'];
        $group_key = (!empty($_REQUEST['group_key'])) ? $_REQUEST['group_key'] : 0;

        foreach ($_cart['product_groups'] as &$product_group) {
            if (!empty($product_group['shippings'][$shipping_id]) && ($product_group['shippings'][$shipping_id]['module'] == 'pickpoint')) {
                $shipping = $product_group['shippings'][$shipping_id];
                if ($shipping['group_key'] == $group_key) {
                    $pickpoint_postamat['pickpoint_id'] = $_REQUEST['pickpoint_id'];
                    $pickpoint_postamat['address_pickpoint'] = $_REQUEST['address_pickpoint'];
                    $product_group['shippings'][$shipping_id]['data']['pickpoint_postamat'] = $pickpoint_postamat;
                    $_cart['pickpoint_office'][$shipping['group_key']][$shipping['shipping_id']] = $pickpoint_postamat;
                }
            }
        }
    }

    if (!empty($_cart['pickpoint_office'])) {
        Tygh::$app['view']->assign('pickpoint_postamat', $_cart['pickpoint_office']);
    }
}
