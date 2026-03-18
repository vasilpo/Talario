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

$cart = $_SESSION['cart'];

if ($mode == 'checkout') {
    $phone = '';
    if (!empty($cart['user_data']['phone'])) {
        $phone = $cart['user_data']['phone'];

    } elseif (!empty($cart['user_data']['b_phone'])) {
        $phone = $cart['user_data']['b_phone'];

    } elseif (!empty($cart['user_data']['s_phone'])) {
        $phone = $cart['user_data']['s_phone'];
    }

    $phone_normalize =  fn_rus_payments_normalize_phone($phone);

    $payment_id = (!empty($cart['payment_method_data']['payment_id'])) ? $cart['payment_method_data']['payment_id'] : 0;
    $processor_script = db_get_field("SELECT processor_script FROM ?:payments INNER JOIN ?:payment_processors USING (processor_id) WHERE payment_id = ?i", $payment_id);

    if (($processor_script == 'account.php') && !empty($cart['payment_method_data']['processor_params']['fields_account'])) {
        $account_params = fn_rus_payments_account_fields($cart['payment_method_data']['processor_params']['fields_account'], $cart['user_data']);

        if (!empty($account_params)) {
            Tygh::$app['view']->assign('account_params', $account_params);
        }
    }
    Tygh::$app['view']->assign('phone_normalize', $phone_normalize);
}
