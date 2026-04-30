<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access' . ' denied');

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || $mode !== 'view') {
    return;
}

fn_lt_yandex_metrika_goals_queue_partner_application_from_page();
