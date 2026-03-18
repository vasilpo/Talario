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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'update') {
    $payment_id = $_REQUEST['payment_id'];

    $selected_countries = fn_payments_by_country_get_payment_countries($payment_id);
    $all_countries = array_diff(fn_get_simple_countries(), $selected_countries);

    Tygh::$app['view']->assign('selected_countries', $selected_countries);
    Tygh::$app['view']->assign('all_countries', $all_countries);
}

if ($mode === 'manage') {
    Tygh::$app['view']->assign('all_countries', fn_get_simple_countries());
}

