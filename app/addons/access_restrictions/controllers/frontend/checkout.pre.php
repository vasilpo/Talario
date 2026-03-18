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

$cart = & Tygh::$app['session']['cart'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'place_order') {
        if (!empty($_REQUEST['payment_info']) && !empty($_REQUEST['payment_info']['card_number'])) {
            if (fn_card_number_is_blocked($_REQUEST['payment_info']['card_number'])) {
                fn_set_notification('E', __('error'), __('text_cc_number_is_blocked', array(
                    '[cc_number]' => $_REQUEST['payment_info']['card_number']
                )));

                return array(CONTROLLER_STATUS_REDIRECT, 'checkout.checkout');
            }
        }
    }

    if ($mode == 'update_steps' && !empty($_REQUEST['update_step']) && $_REQUEST['update_step'] != 'step_one') {
        if (!empty($cart['user_data']) && fn_email_is_blocked($cart['user_data'])) {
            return array(CONTROLLER_STATUS_REDIRECT, 'checkout.customer_info');
        }
    }

    return;
}

