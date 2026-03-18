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

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Enum\YesNo;
use Tygh\Enum\Addons\Searchanise\SignupStatuses;

if ($mode == 'export') {
    fn_se_signup(fn_se_get_company_id(), NULL, true);
    fn_se_queue_import(fn_se_get_company_id(), NULL, true);

    return [CONTROLLER_STATUS_OK, 'addons.update?addon=searchanise&selected_section=settings'];

} elseif ($mode == 'options') {
    if (isset($_REQUEST['snize_use_navigation'])) {
        $is_navigation = ($_REQUEST['snize_use_navigation'] == 'true') ? YesNo::YES : YesNo::NO;
        fn_se_set_simple_setting('use_navigation', $is_navigation);
    }

    exit;

} elseif ($mode == 'signup') {
    if (defined('AJAX_REQUEST')) {
        Tygh::$app['ajax']->assign('non_ajax_notifications', true);
        $data = [
            'result' => false,
            'errors' => [],
        ];

        if (!empty($_REQUEST['get_status'])) {
            $data['result'] = fn_se_get_signup_status();

            if ($data['result'] == SignupStatuses::DONE) {
                return [CONTROLLER_STATUS_OK, 'addons.update?addon=searchanise&selected_section=settings'];
            } else {
                $data['errors'] = fn_get_notifications();
            }

        } else {
            if (fn_se_signup() == true) {
                fn_se_queue_import();
                $data['result'] = true;
            }
        }

        Tygh::$app['ajax']->assign('data', $data);

        exit();
    }

    return [CONTROLLER_STATUS_OK, 'addons.update?addon=searchanise'];
}
