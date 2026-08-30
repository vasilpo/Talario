<?php

defined('BOOTSTRAP') or die('Access denied');

/** Removes only the commercial mapping; physical schedule history is retained. */
function fn_talario_schedule_resources_delete_product_post($product_id, $product_deleted)
{
    if ($product_deleted) {
        db_query('DELETE FROM ?:talario_resource_products WHERE product_id = ?i', $product_id);
    }
}

function fn_talario_schedule_resources_get_shared_product_ids($product_id)
{
    $resource_id = (int) db_get_field('SELECT resource_id FROM ?:talario_resource_products WHERE product_id = ?i LIMIT 1', $product_id);
    if (!$resource_id) { return [(int) $product_id]; }
    return array_map('intval', db_get_fields('SELECT product_id FROM ?:talario_resource_products WHERE resource_id = ?i', $resource_id));
}

function fn_talario_schedule_resources_pre_add_to_cart(&$product_data, &$cart, &$auth, &$update)
{
    $service = new \Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService();
    foreach ($product_data as $key => &$item) {
        $booking = (array) ($item['booking_info'] ?? $item['extra']['booking_info'] ?? []);
        if (($booking['booking_type'] ?? '') !== 'T') { continue; }
        if (!$service->productUsesScheduleResource((int) $item['product_id'])) { continue; }
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

function fn_talario_schedule_resources_post_add_to_cart($product_data, $cart, $auth, $update, $ids)
{
    $active_keys = array_map('strval', array_keys((array) ($cart['products'] ?? [])));
    $holds = db_get_array('SELECT cart_item_id FROM ?:talario_resource_holds WHERE cart_id = ?s AND status = ?s', session_id(), 'A');
    $service = new \Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService();
    foreach ($holds as $hold) {
        if (!in_array((string) $hold['cart_item_id'], $active_keys, true)) {
            $service->releaseCartHold(session_id(), (string) $hold['cart_item_id']);
        }
    }
}

function fn_talario_schedule_resources_pre_place_order(&$cart, &$allow, $product_groups)
{
    $service = new \Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService();
    foreach ((array) ($cart['products'] ?? []) as $key => $item) {
        $booking = (array) ($item['extra']['booking_info'] ?? []);
        if (($booking['booking_type'] ?? '') !== 'T') { continue; }
        if (!$service->productUsesScheduleResource((int) $item['product_id'])) { continue; }
        $slot = explode(' - ', (string) ($booking['booking_slot'] ?? ''));
        $date_value = $booking['original_booking_date'] ?? $booking['booking_date'] ?? '';
        $date = is_numeric($date_value) ? date('Y-m-d', (int) $date_value) : date('Y-m-d', strtotime($date_value));
        try {
            $service->reserveProductSlot(
                (int) $item['product_id'], $date, substr($slot[0] ?? '', 0, 5),
                (int) ($booking['booking_slot_amount'] ?? $item['amount'] ?? 1),
                session_id(), (string) $key, (int) ($cart['user_data']['user_id'] ?? 0)
            );
        } catch (\InvalidArgumentException $e) { $allow = false; fn_set_notification('E', __('error'), $e->getMessage()); }
    }
}

function fn_talario_schedule_resources_delete_cart_product($cart, $cart_id, $full_erase)
{
    (new \Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService())->releaseCartHold(session_id(), (string) $cart_id);
}

function fn_talario_schedule_resources_clear_cart($cart, $complete, $clear_all)
{
    (new \Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService())->releaseCartHold(session_id());
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

function fn_talario_schedule_resources_change_order_status(&$status_to, $status_from, $order_info)
{
    $to = fn_get_status_params($status_to, STATUSES_ORDER);
    $from = fn_get_status_params($status_from, STATUSES_ORDER);
    $service = new \Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService();
    if (($to['inventory'] ?? '') === 'I' && ($from['inventory'] ?? '') !== 'I') {
        $service->releaseOrderBookings((int) $order_info['order_id']);
        db_query('UPDATE ?:ec_table_booking_system_booking_info SET status = ?s WHERE order_id = ?i', 'D', $order_info['order_id']);
    } elseif (($to['inventory'] ?? '') === 'D' && ($from['inventory'] ?? '') === 'I') {
        try {
            $service->restoreOrderBookings((int) $order_info['order_id']);
            db_query('UPDATE ?:ec_table_booking_system_booking_info SET status = ?s WHERE order_id = ?i', 'A', $order_info['order_id']);
        } catch (\InvalidArgumentException $e) {
            fn_set_notification('E', __('error'), $e->getMessage());
            $status_to = $status_from;
        }
    }
}
