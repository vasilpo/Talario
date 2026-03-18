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

/**
 * @var string $mode
 * @var string $action
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode === 'clean') {
        if ($action === 'old') {
            fn_cleanup_old_logs(Registry::get('runtime.company_id'));
        } else {
            fn_cleanup_all_logs(Registry::get('runtime.company_id'));
        }

        fn_set_notification('N', __('notice'), __('successful'));
    }

    return [CONTROLLER_STATUS_REDIRECT, 'logs.manage'];
}

if ($mode == 'manage') {

    list($logs, $search) = fn_get_logs($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'));

    Tygh::$app['view']->assign([
        'logs'      => $logs,
        'search'    => $search,
        'log_types' => fn_get_log_types(),
    ]);
}
