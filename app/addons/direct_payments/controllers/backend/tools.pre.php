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

use Tygh\Registry;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update_status') {
        if ($_REQUEST['table'] == 'payments' && Registry::get('runtime.company_id')) {
            fn_tools_update_status($_REQUEST);
            Tygh::$app['ajax']->assign('force_redirection', fn_url('payments.manage'));
            exit;
        }
    }
}

if ($mode == 'update_position') {
    if ($_REQUEST['table'] == 'payments' && $vendor_id = Registry::get('runtime.company_id')) {
        $ids = explode(',', $_REQUEST['ids']);
        $positions = explode(',', $_REQUEST['positions']);

        foreach ($ids as $i => $id) {
            if (!fn_direct_payments_check_payment_owner($vendor_id, $id)) {
                unset($ids[$i], $positions[$i]);
            }
        }

        $_REQUEST['ids'] = implode(',', $ids);
        $_REQUEST['positions'] = implode(',', $positions);
    }
}
