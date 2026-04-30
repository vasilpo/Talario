<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access' . ' denied');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($mode === 'success_add' || $mode === 'update')) {
    fn_lt_yandex_metrika_goals_queue_recent_profile_registration_from_page($auth, $mode);

    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mode === 'update') {
    fn_lt_yandex_metrika_goals_queue_profile_registration_from_controller($auth);
}
