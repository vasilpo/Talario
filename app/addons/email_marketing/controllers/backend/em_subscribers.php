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

use Tygh\EmailSync;
use Tygh\Registry;
use Tygh\Settings;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {

    $suffix = '.manage';

    if ($mode == 'update') {
        $subscriber_id = fn_em_update_subscriber($_REQUEST['subscriber_data'], $_REQUEST['subscriber_id']);
    }

    if ($mode == 'm_delete') {
        fn_em_delete_subscribers($_REQUEST['subscriber_ids']);
    }

    if ($mode == 'export_range') {
        if (!empty($_REQUEST['subscriber_ids'])) {
            if (empty(Tygh::$app['session']['export_ranges'])) {
                Tygh::$app['session']['export_ranges'] = array();
            }

            if (empty(Tygh::$app['session']['export_ranges']['subscribers'])) {
                Tygh::$app['session']['export_ranges']['subscribers'] = array('pattern_id' => 'em_subscribers');
            }

            Tygh::$app['session']['export_ranges']['subscribers']['data'] = array('subscriber_id' => $_REQUEST['subscriber_ids']);

            unset($_REQUEST['redirect_url']);

            return array(CONTROLLER_STATUS_REDIRECT, 'exim.export?section=subscribers&pattern_id=em_subscribers');
        }
    }

    if ($mode == 'delete') {
        if (!empty($_REQUEST['subscriber_id'])) {
            fn_em_delete_subscribers((array) $_REQUEST['subscriber_id']);
        }
        $suffix = '.manage';
    }

    if ($mode == 'sync') {
        EmailSync::instance()->sync();
        $suffix = '.manage';
    }

    if ($mode == 'import') {
        EmailSync::instance()->import();
        $suffix = '.manage';
    }

    return array(CONTROLLER_STATUS_OK, 'em_subscribers' . $suffix);
}

if ($mode == 'manage') {

    list($subscribers, $search) = fn_em_get_subscribers($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'));

    // Get settings
    $em_settings = array();
    foreach (fn_em_get_managable_settings() as $option) {
         $option_data = Settings::instance()->getSettingDataByName($option);
         $em_settings[$option_data['object_id']] = $option_data;
    }

    Tygh::$app['view']->assign('em_settings', $em_settings);
    Tygh::$app['view']->assign('em_support', EmailSync::instance()->supports());

    Tygh::$app['view']->assign('subscribers', $subscribers);
    Tygh::$app['view']->assign('search', $search);
}

function fn_em_get_managable_settings()
{
    return array('em_show_on_checkout', 'em_checkout_enabled', 'em_double_opt_in', 'em_welcome_letter');
}
