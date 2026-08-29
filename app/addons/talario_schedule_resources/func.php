<?php

defined('BOOTSTRAP') or die('Access denied');

/** Removes only the commercial mapping; physical schedule history is retained. */
function fn_talario_schedule_resources_delete_product_post($product_id, $product_deleted)
{
    if ($product_deleted) {
        db_query('DELETE FROM ?:talario_resource_products WHERE product_id = ?i', $product_id);
    }
}

function fn_talario_schedule_resources_pre_add_to_cart(&$product_data, &$cart, &$auth, &$update)
{
    $service = new \Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService();
    foreach ($product_data as $key => &$item) {
        $booking = (array) ($item['booking_info'] ?? $item['extra']['booking_info'] ?? []);
        if (($booking['booking_type'] ?? '') !== 'T') { continue; }
        $date = (string) ($booking['original_booking_date'] ?? $booking['booking_date'] ?? '');
        $date = is_numeric($date) ? date('Y-m-d', (int) $date) : date('Y-m-d', strtotime($date));
        $slot = explode(' - ', (string) ($booking['booking_slot'] ?? ''));
        $quantity = (int) ($booking['booking_slot_amount'] ?? $item['amount'] ?? 1);
        try {
            $service->reserveProductSlot(
                (int) $item['product_id'], $date, substr($slot[0] ?? '', 0, 5), $quantity,
                session_id(), (string) $key, (int) ($auth['user_id'] ?? 0)
            );
        } catch (\InvalidArgumentException $e) {
            fn_set_notification('E', __('error'), $e->getMessage());
            unset($product_data[$key]);
        }
    }
    unset($item);
}

function fn_talario_schedule_resources_pre_place_order(&$cart, &$allow, $product_groups)
{
    foreach ((array) ($cart['products'] ?? []) as $item) {
        $booking = (array) ($item['extra']['booking_info'] ?? []);
        if (($booking['booking_type'] ?? '') !== 'T') { continue; }
        $active = (bool) db_get_field(
            'SELECT 1 FROM ?:talario_resource_holds WHERE cart_id = ?s AND product_id = ?i '
            . 'AND status = ?s AND expires_at > ?i', session_id(), $item['product_id'], 'A', TIME
        );
        if (!$active) { $allow = false; fn_set_notification('E', __('error'), 'Срок резерва места истёк. Выберите время ещё раз.'); }
    }
}

function fn_talario_schedule_resources_order_placement_routines($order_id, $force_notification, $order_info, $_error)
{
    if ($_error) { return; }
    if (empty($order_info['products'])) {
        $order_info = fn_get_order_info($order_id);
    }
    if (empty($order_info['products'])) { return; }
    (new \Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService())
        ->convertCartHoldsToBookings(session_id(), $order_id, $order_info['products']);
}

function fn_talario_schedule_resources_change_order_status($status_to, $status_from, $order_info)
{
    if (in_array($status_to, ['I', 'D', 'F'], true)) {
        (new \Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService())
            ->releaseOrderBookings((int) $order_info['order_id']);
    }
}
