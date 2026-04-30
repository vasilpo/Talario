<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access' . ' denied');

if (defined('AJAX_REQUEST')) {
    return;
}

$lt_yandex_metrika_goals = fn_lt_yandex_metrika_goals_pop_session_goals();
if ($lt_yandex_metrika_goals) {
    Tygh::$app['view']->assign('lt_yandex_metrika_goals', $lt_yandex_metrika_goals);
}
