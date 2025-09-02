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
    return fn_get_simple_statuses();
}
