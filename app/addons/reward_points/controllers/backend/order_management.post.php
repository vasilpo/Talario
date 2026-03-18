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
$customer_auth = & Tygh::$app['session']['customer_auth'];
$suffix = !empty($cart['order_id']) ? '.update' : '.add';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //
    // Delete point in use from the cart
    //
    if ($mode == 'delete_points_in_use') {
        if (isset($cart['points_info']['in_use'])) {
            $cart['deleted_points_info'] = $cart['points_info'];
            unset($cart['points_info']['in_use']);
        }

        return array(CONTROLLER_STATUS_REDIRECT, 'order_management' . $suffix);
    }

    return;
}

//
// Display totals
//
if ($mode == 'update' || $mode == 'add') {

    $prev_points = !empty($cart['previous_points_info']['in_use']['points']) ? $cart['previous_points_info']['in_use']['points'] : 0;
    $user_points = (int) fn_get_user_additional_data(POINTS, $customer_auth['user_id']) + (int) $prev_points;

    Tygh::$app['view']->assign('user_points', $user_points);
}
