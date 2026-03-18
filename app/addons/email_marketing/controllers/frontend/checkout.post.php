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
use Tygh\Enum\YesNo;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'customer_info' || $mode == 'update_steps') {

        if (!empty(Tygh::$app['session']['cart']['user_data']['email'])) {
            $name = fn_em_get_subscriber_name();
            $email = Tygh::$app['session']['cart']['user_data']['email'];

            $subscriber_data = fn_em_get_subscriber_data($email);
            if (!empty($subscriber_data) && $subscriber_data['name'] != $name) {
                fn_em_update_subscriber(array(
                    'name' => $name
                ), $subscriber_data['subscriber_id']);
            }
        }
    }

    return;
}

if ($mode == 'checkout') {
    $user_email = isset($_REQUEST['user_email']) ? $_REQUEST['user_email'] : '';

    if (
        Registry::get('addons.email_marketing.em_show_on_checkout') === YesNo::YES
        && (
            !empty($user_email)
            || !empty(Tygh::$app['session']['cart']['user_data']['email'])
        )
        && !fn_em_is_email_subscribed($user_email ?: Tygh::$app['session']['cart']['user_data']['email'])
    ) {
        Tygh::$app['view']->assign('show_subscription_checkbox', true);
    }
}
