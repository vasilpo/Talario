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

use Tygh\Enum\OrderDataTypes;

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'update' && !isset($_REQUEST['is_ajax'])) {
    $_cart = Tygh::$app['session']['cart'];

    if (!empty($_cart['order_id'])) {
        $old_ship_data = db_get_field('SELECT data FROM ?:order_data WHERE order_id = ?i AND type = ?s', $_cart['order_id'], OrderDataTypes::SHIPPING);
        if (!empty($old_ship_data)) {
            $old_ship_data = unserialize($old_ship_data);
            foreach ($old_ship_data as $shipping) {
                if ($shipping['module'] === 'sdek2' && !empty($shipping['office_id'])) {
                    Tygh::$app['session']['cart']['select_office'][$shipping['group_key']][$shipping['shipping_id']] = $shipping['office_id'];

                    Tygh::$app['view']->assign('old_ship_data', $old_ship_data);
                }
            }
        }
    }
}
