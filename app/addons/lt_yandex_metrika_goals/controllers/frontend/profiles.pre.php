<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access' . ' denied');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $mode !== 'update') {
    return;
}

fn_lt_yandex_metrika_goals_mark_profile_add_request($auth);
