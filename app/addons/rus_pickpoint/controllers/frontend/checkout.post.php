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

$cart = & Tygh::$app['session']['cart'];

if (!empty($cart['pickpoint_office'])) {
    Tygh::$app['view']->assign('pickpoint_office', $cart['pickpoint_office']);
}

if (!empty($cart['user_data'])) {
    $fromcity = '';
    $city = '';

    if (!empty($cart['user_data']['s_state_descr'])) {
        $fromcity = $cart['user_data']['s_state_descr'];

    } elseif (!empty($cart['user_data']['b_state_descr'])) {
        $fromcity = $cart['user_data']['b_state_descr'];
    }

    if (!empty($cart['user_data']['s_city'])) {
        $city = $cart['user_data']['s_city'];
    } elseif (!empty($cart['user_data']['b_city'])) {
        $city = $cart['user_data']['b_city'];
    }
    Tygh::$app['view']->assign('fromcity', $fromcity);
    Tygh::$app['view']->assign('pickpoint_city', $city);
}
