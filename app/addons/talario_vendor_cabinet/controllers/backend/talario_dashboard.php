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
    $statuses = db_get_hash_single_array(
        'SELECT status, COUNT(*) AS amount FROM ?:products WHERE company_id = ?i GROUP BY status',
        ['status', 'amount'],
        $company_id
    );

    $counts['active'] = (int) ($statuses[ObjectStatuses::ACTIVE] ?? 0);
    $counts['pending'] = (int) ($statuses[ProductStatuses::REQUIRES_APPROVAL] ?? 0);
    foreach ([ObjectStatuses::DISABLED, ObjectStatuses::HIDDEN, ProductStatuses::DISAPPROVED] as $status) {
        $counts['disabled'] += (int) ($statuses[$status] ?? 0);
    }

    Tygh::$app['view']->assign('talario_counts', $counts);
}
