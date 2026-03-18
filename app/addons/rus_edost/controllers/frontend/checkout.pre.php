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

use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

$cart = &Tygh::$app['session']['cart'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode === 'update_steps' || $mode === 'checkout' || $mode === 'update_shipping') {

        if (!empty($_REQUEST['pickpointmap'])) {
            $cart['pickpointmap'] = $_REQUEST['pickpointmap'];
        }

        if (!empty($_REQUEST['select_office'])) {
            $cart['select_office'] = $_REQUEST['select_office'];
        }
    }

    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'checkout') {
    if (!empty($_REQUEST['select_office'])) {
        $cart['select_office'] = $_REQUEST['select_office'];
    }
    if (!empty($cart['select_office'])) {
        Tygh::$app['view']->assign('select_office', $cart['select_office']);
    }
    if (!empty($_REQUEST['pickpointmap'])) {
        $cart['pickpointmap'] = $_REQUEST['pickpointmap'];
    }

}