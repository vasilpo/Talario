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

$_cart = & Tygh::$app['session']['cart'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'select_customer') {
        $_cart['pickpoint_office'] = array();
    }
}

if ($mode == 'customer_info') {
    $_cart['pickpoint_office'] = array();
}

if ($mode == 'add') {
    if (!empty($_cart['product_groups'])) {
        if (!empty($_cart['pickpoint_office'])) {
            Tygh::$app['view']->assign('pickpoint_postamat', $_cart['pickpoint_office']);
        }
    }
}
