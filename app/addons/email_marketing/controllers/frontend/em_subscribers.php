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

    // Add email to maillist
    if ($mode == 'update') {

        if (!empty($_REQUEST['subscribe_email'])) {

            fn_em_subscribe_email($_REQUEST['subscribe_email'], array(
                'name' => fn_em_get_subscriber_name()
            ));
        }
    }

    return array(CONTROLLER_STATUS_REDIRECT);
}

if ($mode == 'unsubscribe') {

    if (!empty($_REQUEST['unsubscribe_key'])) {
        
        fn_em_unsubscribe($_REQUEST['unsubscribe_key']);

        Tygh::$app['view']->assign('notification_msg', __('email_marketing.text_unsubscribe_successful'));
        $msg = Tygh::$app['view']->fetch('addons/email_marketing/common/notification.tpl');
        fn_set_notification('I', __('email_marketing.unsubscribe_successful'), $msg);
    }
    return array(CONTROLLER_STATUS_REDIRECT, fn_url());

} elseif ($mode == 'confirm') {

    if (!empty($_REQUEST['ekey'])) {
        $email = fn_get_object_by_ekey($_REQUEST['ekey'], 'E');
        if (!empty($email) && fn_em_confirm_subscription($email)) {
            Tygh::$app['view']->assign('notification_msg', __('email_marketing.text_subscription_confirmed_2'));
            $msg = Tygh::$app['view']->fetch('addons/email_marketing/common/notification.tpl');
            fn_set_notification('I', __('email_marketing.subscription_confirmed_2'), $msg);
        } else {
            fn_set_notification('E', __('error'), __('text_ekey_not_valid'));
        }
    }
    
    return array(CONTROLLER_STATUS_REDIRECT, fn_url());
}
