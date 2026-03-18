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

// rus_build_unisender

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($mode == 'update') {
        $email = $_REQUEST['user_data']['email'];
        $subscriber_id = fn_unisender_get_subscriber_id($email);

        if (!empty($auth['user_id'])) {
            $_REQUEST['user_data']['user_id'] = $auth['user_id'];
        }

        if (!empty($_REQUEST['unisender_lists']) && !fn_is_empty($_REQUEST['unisender_lists'])) {
            if (empty($subscriber_id)) {
                $subscriber_id = fn_unisender_add_subscriber($email);
            }

            fn_unisender_subscribe($_REQUEST['user_data'], reset($_REQUEST['unisender_lists']), true);
        } else {
            if (!empty($subscriber_id)) {
                fn_unisender_unsubscribe($subscriber_id);
            }
        }
    }
}

if ($mode == 'add' || $mode == 'update') {
    Tygh::$app['view']->assign('unisender_page_mailing_lists', fn_unisender_get_enabled_lists());
}

if ($mode == 'update') {
    $email = db_get_field("SELECT email FROM ?:users WHERE user_id = ?i", $_SESSION['auth']['user_id']);
    $mailing_lists = fn_unisender_get_user_lists($email);
    Tygh::$app['view']->assign('unisender_user_mailing_lists', $mailing_lists);
}
