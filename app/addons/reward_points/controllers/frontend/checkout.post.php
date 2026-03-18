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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'point_payment') {

        $points_to_use = empty($_REQUEST['points_to_use']) ? 0 : intval($_REQUEST['points_to_use']);
        if (!empty($points_to_use) && abs($points_to_use) == $points_to_use) {
            Tygh::$app['session']['cart']['points_info']['in_use']['points'] = $points_to_use;
        }

        $redirect_mode = isset($_REQUEST['redirect_mode']) ? $_REQUEST['redirect_mode'] : 'checkout';

        return array(CONTROLLER_STATUS_REDIRECT, 'checkout.' . $redirect_mode . '.show_payment_options');
    }

    if ($mode == 'delete_points_in_use') {
        unset(Tygh::$app['session']['cart']['points_info']['in_use']);

        $redirect_mode = isset($_REQUEST['redirect_mode']) ? $_REQUEST['redirect_mode'] : 'checkout';

        return array(CONTROLLER_STATUS_REDIRECT, 'checkout.' . $redirect_mode . '.show_payment_options');
    }

    return;
}
