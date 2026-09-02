<?php

use Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService;
use Tygh\Enum\Addons\VendorDataPremoderation\ProductStatuses;
use Tygh\Enum\ObjectStatuses;
use Tygh\Tygh;
use Tygh\VendorPayouts;

defined('BOOTSTRAP') or die('Access denied');

$company_id = (int) fn_get_runtime_company_id();
if (!$company_id) {
    return [CONTROLLER_STATUS_DENIED];
}

if ($mode === 'manage') {
    $counts = array_fill_keys(['active', 'pending', 'drafts', 'rejected', 'bookings'], 0);
    $status_groups = [
        'active'   => [ObjectStatuses::ACTIVE],
        'pending'  => [ProductStatuses::REQUIRES_APPROVAL],
        // В кабинете «Черновики» — это только занятия со статусом H.
        // Выключенные и отклонённые занятия не должны выдавать себя за черновики.
        'drafts'   => [ObjectStatuses::HIDDEN],
        'rejected' => [ProductStatuses::DISAPPROVED],
    ];

    foreach ($status_groups as $count_key => $statuses) {
        [, $search] = fn_get_products([
            'company_id'               => $company_id,
            'status'                   => $statuses,
            'include_child_variations' => false,
        ], 1, DESCR_SL);
        $counts[$count_key] = (int) $search['total_items'];
    }

    $time_from = strtotime('-30 days');
    $time_to = TIME;
    $settled_statuses = fn_get_settled_order_statuses();

    $counts['bookings'] = (int) db_get_field(
        'SELECT COUNT(*) FROM ?:orders WHERE company_id = ?i AND timestamp >= ?i AND timestamp <= ?i AND status IN (?a)',
        $company_id,
        $time_from,
        $time_to,
        $settled_statuses
    );

    $sales_30_days = (float) db_get_field(
        'SELECT COALESCE(SUM(total), 0) FROM ?:orders WHERE company_id = ?i AND timestamp >= ?i AND timestamp <= ?i AND status IN (?a)',
        $company_id,
        $time_from,
        $time_to,
        $settled_statuses
    );

    $vendor_payouts = VendorPayouts::instance(['vendor' => $company_id]);
    [$current_balance] = $vendor_payouts->getBalance();

    $recent_orders = db_get_array(
        'SELECT order_id, timestamp, total, status, firstname, lastname FROM ?:orders WHERE company_id = ?i ORDER BY timestamp DESC LIMIT 5',
        $company_id
    );

    $attention_items = [];
    if ($counts['drafts'] > 0) {
        $attention_items[] = [
            'type'        => 'drafts',
            'count'       => $counts['drafts'],
            'title'       => 'Есть черновики занятий',
            'description' => 'Опубликуйте их, чтобы родители могли записываться.',
            'url'         => fn_url('talario_classes.manage?talario_status=disabled'),
            'action'      => 'Открыть черновики',
        ];
    }
    if ($counts['rejected'] > 0) {
        $attention_items[] = [
            'type'        => 'rejected',
            'count'       => $counts['rejected'],
            'title'       => 'Есть занятия, которые нужно доработать',
            'description' => 'Откройте их и учтите комментарии Talario.',
            'url'         => fn_url('talario_classes.manage?talario_status=disabled'),
            'action'      => 'Открыть занятия',
        ];
    }

    $company_data = fn_get_company_data($company_id, DESCR_SL, ['skip_cache' => true]);
    $center = fn_talario_vendor_cabinet_get_center($company_id);
    $locations = (new ScheduleResourceService())->getLocations();
    $has_center_info = !empty($center['name']);
    $has_branch = !empty($locations);

    Tygh::$app['view']->assign([
        'talario_counts'          => $counts,
        'talario_attention_items' => $attention_items,
        'talario_sales_30_days'   => $sales_30_days,
        'talario_current_balance' => (float) $current_balance,
        'talario_recent_orders'   => $recent_orders,
        'talario_has_center_info' => $has_center_info,
        'talario_has_branch'      => $has_branch,
        'talario_partner_name'    => (string) ($company_data['company'] ?? ''),
        'talario_center_name'     => (string) ($center['name'] ?? ''),
    ]);
}
