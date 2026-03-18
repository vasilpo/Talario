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

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'cron_get_verified_status') {
    if (!empty($_REQUEST['magic_key']) && urldecode($_REQUEST['magic_key']) == Registry::get('addons.paypal_adaptive.cron_key')) {
        list($companies, $search) = fn_get_companies(array(), Tygh::$app['session']['auth']);
        foreach ($companies as $company_key => $company) {
            fn_paypal_adaptive_get_verified_status($company);
        }
        die(__('paypal_adaptive_statuses_updated_successfully'));
    } else {
        die(__('paypal_adaptive_error_wrong_cron_key'));
    }

}