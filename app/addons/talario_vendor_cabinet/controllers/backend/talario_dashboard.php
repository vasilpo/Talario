<?php

use Tygh\Enum\Addons\VendorDataPremoderation\ProductStatuses;
use Tygh\Enum\ObjectStatuses;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

$company_id = (int) fn_get_runtime_company_id();
if (!$company_id) {
    return [CONTROLLER_STATUS_DENIED];
}

if ($mode === 'manage') {
    $counts = array_fill_keys(['active', 'pending', 'disabled', 'bookings'], 0);
    $status_groups = [
        'active'   => [ObjectStatuses::ACTIVE],
        'pending'  => [ProductStatuses::REQUIRES_APPROVAL],
        'disabled' => [ObjectStatuses::DISABLED, ObjectStatuses::HIDDEN, ProductStatuses::DISAPPROVED],
    ];

    foreach ($status_groups as $count_key => $statuses) {
        [, $search] = fn_get_products([
            'company_id'               => $company_id,
            'status'                   => $statuses,
            'include_child_variations' => false,
        ], 1, DESCR_SL);
        $counts[$count_key] = (int) $search['total_items'];
    }

    Tygh::$app['view']->assign('talario_counts', $counts);
}
