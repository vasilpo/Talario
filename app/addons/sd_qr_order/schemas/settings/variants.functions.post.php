<?php
/****************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

/**
 * Get order statuses.
 *
 * @return array
 */
function fn_settings_variants_addons_sd_qr_order_statuses()
{
    $order_statuses = fn_get_statuses(STATUSES_ORDER, [], false, true);
    $result = [];
    foreach ($order_statuses as $o_status) {
        $result[$o_status['status']] = $o_status['description'];
    }

    return $result;
}
