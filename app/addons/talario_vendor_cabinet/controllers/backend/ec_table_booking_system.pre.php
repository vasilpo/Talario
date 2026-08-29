<?php

defined('BOOTSTRAP') or die('Access denied');

if (fn_get_runtime_company_id() && !in_array($mode, ['booked_orders', 'booking_info'], true)) {
    return [CONTROLLER_STATUS_DENIED];
}
