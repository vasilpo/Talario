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

function fn_talario_schedule_resources_override_single_day_slots(
    $product_id,
    $selected_date,
    array &$available_slots,
    array &$unavailable_slots
) {
    $resource_id = (int) db_get_field('SELECT resource_id FROM ?:talario_resource_products WHERE product_id = ?i LIMIT 1', $product_id);
    if (!$resource_id) { return; }
    $occurrences = db_get_hash_array(
        'SELECT occurrence_id, starts_at, ends_at, capacity FROM ?:talario_resource_occurrences '
        . 'WHERE resource_id = ?i AND DATE(starts_at) = ?s AND status = ?s',
        'occurrence_id', $resource_id, date('Y-m-d', strtotime($selected_date)), 'A'
    );
    $by_start = [];
    foreach ($occurrences as $occurrence) {
        $holds = (int) db_get_field(
            'SELECT COALESCE(SUM(quantity), 0) FROM ?:talario_resource_holds '
            . 'WHERE occurrence_id = ?i AND status = ?s AND expires_at > ?i',
            $occurrence['occurrence_id'], 'A', TIME
        );
        $bookings = (int) db_get_field(
            'SELECT COALESCE(SUM(quantity), 0) FROM ?:talario_resource_bookings WHERE occurrence_id = ?i AND status = ?s',
            $occurrence['occurrence_id'], 'A'
        );
        $by_start[substr($occurrence['starts_at'], 11, 5)] = max(0, (int) $occurrence['capacity'] - $holds - $bookings);
    }
    $project = static function (array $slot) use ($by_start) {
        $start = substr((string) ($slot[0] ?? $slot['start_time'] ?? ''), 0, 5);
        // A mapped Talario product is available only when the exact dated
        // occurrence exists; the weekly Ecarter row is merely a UI template.
        $slot['amount'] = $by_start[$start] ?? 0;
        return $slot;
    };
    $combined = array_map($project, array_merge($available_slots, $unavailable_slots));
    $available_slots = [];
    $unavailable_slots = [];
    foreach ($combined as $slot) {
        if ((int) ($slot['amount'] ?? 1) > 0) { $available_slots[] = $slot; } else { $unavailable_slots[] = $slot; }
    }
}

function fn_talario_schedule_resources_pre_add_to_cart(&$product_data, &$cart, &$auth, &$update)
{
    // The final CS-Cart cart item identifier does not exist yet. A persistent
    // hold is therefore created only by post_add_to_cart, after Ecarter and
    // the core have accepted the item.
}

function fn_talario_schedule_resources_post_add_to_cart($product_data, &$cart, $auth, $update, $ids)
{
    $service = new \Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService();
    foreach ((array) $ids as $cart_item_id => $product_id) {
        if (empty($cart['products'][$cart_item_id]) || !$service->productUsesScheduleResource((int) $product_id)) {
            continue;
        }
        $item = $cart['products'][$cart_item_id];
        $booking = (array) ($item['extra']['booking_info'] ?? []);
        if (($booking['booking_type'] ?? '') !== 'T') { continue; }
        $date_value = $booking['original_booking_date'] ?? $booking['booking_date'] ?? '';
        $date = is_numeric($date_value) ? date('Y-m-d', (int) $date_value) : date('Y-m-d', strtotime($date_value));
        $slot = explode(' - ', (string) ($booking['booking_slot'] ?? ''));
        try {
            $service->reserveProductSlot(
                (int) $product_id,
                $date,
                substr($slot[0] ?? '', 0, 5),
                (int) ($booking['booking_slot_amount'] ?? $item['amount'] ?? 1),
                session_id(),
                (string) $cart_item_id,
                (int) ($auth['user_id'] ?? 0)
            );
        } catch (\InvalidArgumentException $e) {
            unset($cart['products'][$cart_item_id]);
            $service->releaseCartHold(session_id(), (string) $cart_item_id);
            fn_set_notification('E', __('error'), $e->getMessage());
        }
    }

    $active_keys = array_map('strval', array_keys((array) ($cart['products'] ?? [])));
    $holds = db_get_array('SELECT cart_item_id FROM ?:talario_resource_holds WHERE cart_id = ?s AND status = ?s', session_id(), 'A');
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

function fn_talario_schedule_resources_order_placement_routines($order_id, $force_notification, $order_info, $_error, &$redirect_url)
{
    if ($_error) { return; }
    if (empty($order_info['products'])) {
        $order_info = fn_get_order_info($order_id);
    }
    if (empty($order_info['products'])) { return; }
    $service = new \Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService();
    try {
        $service->convertCartHoldsToBookings(session_id(), $order_id, $order_info['products']);
    } catch (\Throwable $e) {
        $service->releaseCartHold(session_id());
        $inactive_statuses = fn_get_status_by_type_and_param(STATUSES_ORDER, ['inventory' => 'I']);
        $failed_status = in_array(\Tygh\Enum\OrderStatuses::CANCELED, $inactive_statuses, true)
            ? \Tygh\Enum\OrderStatuses::CANCELED
            : reset($inactive_statuses);
        if ($failed_status) {
            fn_change_order_status((int) $order_id, $failed_status, (string) ($order_info['status'] ?? ''), false);
        }
        fn_set_notification('E', __('error'), 'Не удалось подтвердить места для занятия. Заказ отменён, попробуйте оформить его снова.');
        $redirect_url = 'checkout.checkout';
    }
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
