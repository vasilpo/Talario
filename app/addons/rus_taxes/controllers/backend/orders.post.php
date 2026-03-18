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

defined('BOOTSTRAP') or die('Access denied');

/**
 * @psalm-var array{order_id: int} $params
 */
$params = $_REQUEST;

if ($mode === 'details') {
    $order_id = !empty($params['order_id']) ? $params['order_id'] : 0;
    $order_info = fn_get_order_info($order_id, false, true, true, false);

    $marking_extra_data = $order_info ? fn_rus_taxes_get_marking_data_variables_for_order($order_info) : [];

    foreach ($marking_extra_data as $k => $v) {
        Tygh::$app['view']->assign($k, $v);
    }
}
