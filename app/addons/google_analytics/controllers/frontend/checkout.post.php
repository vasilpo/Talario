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

if ($mode == 'complete') {
    $orders_info = array();
    $order_info = Tygh::$app['view']->getTemplateVars('order_info');
    if (!fn_allowed_for('MULTIVENDOR') || (fn_allowed_for('MULTIVENDOR') && $order_info['is_parent_order'] == 'N')) {
        $orders_info[0] = $order_info;
        $orders_info[0]['ga_company_name'] = fn_get_company_name($order_info['company_id']);
    } else {
        $order_ids = explode(',', $order_info['child_ids']);
        foreach ($order_ids as $k => $order_id) {
            $_order_info = fn_get_order_info($order_id);
            $orders_info[$k] = $_order_info;
            $orders_info[$k]['ga_company_name'] = fn_get_company_name($_order_info['company_id']);
        }
    }
    Tygh::$app['view']->assign('orders_info', $orders_info);
}
