<?php

use Tygh\Enum\VendorPayoutApprovalStatuses;
use Tygh\Enum\VendorPayoutTypes;
use Tygh\Tygh;
use Tygh\VendorPayouts;

defined('BOOTSTRAP') or die('Access denied');

$company_id = (int) fn_get_runtime_company_id();
if (!$company_id) {
    return [CONTROLLER_STATUS_DENIED];
}

if ($mode === 'manage') {
    $settled_statuses = fn_get_settled_order_statuses();
    $orders = db_get_array(
        'SELECT order_id, timestamp, total, status, firstname, lastname'
        . ' FROM ?:orders'
        . ' WHERE company_id = ?i AND status IN (?a)'
        . ' ORDER BY timestamp DESC LIMIT 100',
        $company_id,
        $settled_statuses
    );

    $order_ids = array_column($orders, 'order_id');
    $order_net = [];
    if ($order_ids) {
        $order_rows = db_get_array(
            'SELECT order_id, COALESCE(SUM(order_amount), 0) AS partner_amount'
            . ' FROM ?:vendor_payouts'
            . ' WHERE company_id = ?i AND order_id IN (?n)'
            . ' AND payout_type IN (?a)'
            . ' GROUP BY order_id',
            $company_id,
            $order_ids,
            [VendorPayoutTypes::ORDER_PLACED, VendorPayoutTypes::ORDER_CHANGED, VendorPayoutTypes::ORDER_REFUNDED]
        );
        foreach ($order_rows as $row) {
            $order_net[(int) $row['order_id']] = (float) $row['partner_amount'];
        }
    }

    foreach ($orders as &$order) {
        $gross = (float) $order['total'];
        $partner_amount = isset($order_net[(int) $order['order_id']])
            ? (float) $order_net[(int) $order['order_id']]
            : $gross;

        $order['partner_amount'] = $partner_amount;
        $order['commission_amount'] = max(0, $gross - $partner_amount);
    }
    unset($order);

    $payouts = db_get_array(
        'SELECT payout_id, payout_amount, payout_date, start_date, end_date, approval_status, details'
        . ' FROM ?:vendor_payouts'
        . ' WHERE company_id = ?i AND payout_type = ?s'
        . ' ORDER BY payout_date DESC LIMIT 100',
        $company_id,
        VendorPayoutTypes::PAYOUT
    );

    $paid_total = 0.0;
    foreach ($payouts as &$payout) {
        if ($payout['approval_status'] === VendorPayoutApprovalStatuses::COMPLETED) {
            $paid_total += (float) $payout['payout_amount'];
        }

        if (!empty($payout['details'])) {
            $details = @unserialize($payout['details']);
            $payout['details_text'] = is_string($details) ? $details : '';
        } else {
            $payout['details_text'] = '';
        }

        switch ($payout['approval_status']) {
            case VendorPayoutApprovalStatuses::COMPLETED:
                $payout['status_text'] = 'Выплачено';
                break;
            case VendorPayoutApprovalStatuses::DECLINED:
                $payout['status_text'] = 'Отменено';
                break;
            default:
                $payout['status_text'] = 'Ожидает выплаты';
                break;
        }
    }
    unset($payout);

    $vendor_payouts = VendorPayouts::instance(['vendor' => $company_id]);
    [$current_balance] = $vendor_payouts->getBalance();

    Tygh::$app['view']->assign([
        'talario_finance_orders' => $orders,
        'talario_finance_payouts' => $payouts,
        'talario_finance_to_pay' => (float) $current_balance,
        'talario_finance_paid_total' => $paid_total,
    ]);
}
