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

    if ($mode == 'update_details') {
        fn_save_order_log($_REQUEST['order_id'], $_SESSION['auth']['user_id'], 'rus_order_logs_order_changed', '', TIME);
    }
}

if ($mode == 'details') {
    $logs = fn_get_order_logs($_REQUEST['order_id']);
    Tygh::$app['view']->assign('logs', $logs);
    Registry::set('navigation.tabs.logs', array(
        'title' => __('logs'),
        'js' => true
    ));
}

if ($mode == 'update_order_logs') {
    if (defined('AJAX_REQUEST')) {
        $logs = fn_get_order_logs($_REQUEST['order_id']);
        Tygh::$app['view']->assign('logs', $logs);
        Tygh::$app['view']->display('addons/rus_order_logs/views/orders/components/order_logs.tpl');
        exit;
    }
}