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
    return;
}

//
// View page details
//
if ($mode == 'update') {
    if (!empty($_REQUEST['user_type']) && ($_REQUEST['user_type'] == 'C')) {

        if (fn_check_permissions('unisender', 'send_sms', 'admin', 'GET')) {
            Tygh::$app['view']->assign('show_tab_send_sms', true);
            Registry::set('navigation.tabs.message', array(
                'title' => __('addons.rus_unisender.sms_message'),
                'js' => true
            ));
        }
    }

} elseif ($mode == 'manage') {

    if (fn_allowed_for('MULTIVENDOR') || Registry::get('runtime.company_id') || Registry::get('runtime.simple_ultimate')) {
        Tygh::$app['view']->assign('show_unisender_tool', true);
    }
}
