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


if ($mode == 'update' && !isset($_REQUEST['is_ajax'])) {

    $_cart = \Tygh::$app['session']['cart'];

    if (!empty($_cart['order_id'])) {
        $old_ship_data = db_get_field("SELECT data FROM ?:order_data WHERE order_id = ?i AND type = ?s", $_cart['order_id'], 'L');
        if (!empty($old_ship_data)) {
            $old_ship_data = unserialize($old_ship_data);
            foreach($old_ship_data as $group_key => $shipping) {
                if ($shipping['module'] == 'sdek' && !empty($shipping['office_id'])) {

                    \Tygh::$app['session']['cart']['select_office'][$shipping['group_key']][$shipping['shipping_id']] = $shipping['office_id'];

                    Tygh::$app['view']->assign('old_ship_data', $old_ship_data);
                    
                }
            }
        }
    }
}
