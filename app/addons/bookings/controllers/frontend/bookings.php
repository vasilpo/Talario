<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

if ($mode === 'calendar_event') {
    $order_id = isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : 0;
    if ($order_id <= 0) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $order_info = fn_get_order_info($order_id);
    if (empty($order_info)) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $event_data = fn_bookings_get_calendar_event_data($order_info);
    if ($event_data === null) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $ics_content = fn_bookings_build_ics_content($event_data);
    $filename = 'booking-' . $order_id . '.ics';

    header('Content-Type: text/calendar; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($ics_content));

    echo $ics_content;
    exit;
}
