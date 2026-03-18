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
    if ($mode == 'send_form') {
        $suffix = '';

        if (fn_send_form($_REQUEST['page_id'], empty($_REQUEST['form_values']) ? array() : $_REQUEST['form_values'])) {
            $suffix = '&sent=Y';
        }

        return array(CONTROLLER_STATUS_OK, 'pages.view?page_id=' . $_REQUEST['page_id'] . $suffix);
    }

    return;
}

if ($mode == 'view' && !empty($_REQUEST['page_id'])) {
    $restored_form_values = fn_restore_post_data('form_values');
    if (!empty($restored_form_values)) {
        Tygh::$app['view']->assign('form_values', $restored_form_values);
    }

} elseif ($mode == 'sent' && !empty($_REQUEST['page_id'])) {
    $page = fn_get_page_data($_REQUEST['page_id'], CART_LANGUAGE);
    Tygh::$app['view']->assign('page', $page);
}
