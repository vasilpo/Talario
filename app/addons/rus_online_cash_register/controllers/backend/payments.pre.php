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

use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/** @var string $mode */

if ($mode == 'update') {
    $payment_id = isset($_REQUEST['payment_id']) ? $_REQUEST['payment_id'] : 0;

    Tygh::$app['view']->assign('cash_register_payments', fn_rus_online_cash_register_get_external_payments());
    Tygh::$app['view']->assign('cash_register_payment_id', fn_rus_online_cash_register_get_payment_external_id($payment_id));
    Tygh::$app['view']->assign('cash_register_sno', Settings::instance()->getVariants('rus_online_cash_register', 'sno'));
} elseif ($mode == 'manage') {
    Tygh::$app['view']->assign('cash_register_payments', fn_rus_online_cash_register_get_external_payments());
    Tygh::$app['view']->assign('cash_register_sno', Settings::instance()->getVariants('rus_online_cash_register', 'sno'));
}
