<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

function fn_bookings_get_product_names(array $product_ids, string $lang_code): array
{
    static $cache = [];

    $product_ids = array_values(array_unique(array_filter(array_map('intval', $product_ids))));
    if (!$product_ids) {
        return [];
    }

    $result = [];
    $missing_product_ids = [];

    foreach ($product_ids as $product_id) {
        if (isset($cache[$lang_code][$product_id])) {
            $result[$product_id] = $cache[$lang_code][$product_id];
        } else {
            $missing_product_ids[] = $product_id;
        }
    }

    if ($missing_product_ids) {
        $descriptions = db_get_hash_single_array(
            'SELECT product_id, product FROM ?:product_descriptions WHERE product_id IN (?n) AND lang_code = ?s',
            ['product_id', 'product'],
            $missing_product_ids,
            $lang_code
        );

        foreach ($missing_product_ids as $product_id) {
            $product_name = !empty($descriptions[$product_id])
                ? (string) $descriptions[$product_id]
                : (string) fn_get_product_name($product_id, $lang_code);

            if ($product_name === '') {
                continue;
            }

            $cache[$lang_code][$product_id] = $product_name;
            $result[$product_id] = $product_name;
        }
    }

    return $result;
}

function fn_bookings_unserialize_order_extra(string $serialized_extra): array
{
    if ($serialized_extra === '') {
        return [];
    }

    $extra = @unserialize($serialized_extra);

    return is_array($extra) ? $extra : [];
}

function fn_bookings_get_product_booking_info(array $product): array
{
    return !empty($product['extra']['booking_info']) && is_array($product['extra']['booking_info'])
        ? $product['extra']['booking_info']
        : [];
}

function fn_bookings_get_booking_slot_start(string $booking_slot): string
{
    if ($booking_slot === '') {
        return '';
    }

    $slot_parts = preg_split('/\s*-\s*/', $booking_slot);

    return !empty($slot_parts[0]) ? trim((string) $slot_parts[0]) : trim($booking_slot);
}

function fn_bookings_get_booking_slot_end(string $booking_slot): string
{
    if ($booking_slot === '') {
        return '';
    }

    $slot_parts = preg_split('/\s*-\s*/', $booking_slot);

    return !empty($slot_parts[1]) ? trim((string) $slot_parts[1]) : '';
}

function fn_bookings_build_booking_payload(
    int $order_id,
    array $product_data,
    array $feature_values,
    float $products_total,
    float $points_cost,
    float $paid_total
): array {
    $product_id = !empty($product_data['product_id']) ? (int) $product_data['product_id'] : 0;
    $booking_info = !empty($product_data['booking_info']) && is_array($product_data['booking_info'])
        ? $product_data['booking_info']
        : [];
    $booking_slot = !empty($booking_info['booking_slot']) ? (string) $booking_info['booking_slot'] : '';

    return [
        'booking_product_id'   => $product_id,
        'booking_product_name' => !empty($product_data['product_name'])
            ? (string) $product_data['product_name']
            : __('order') . ' #' . $order_id,
        'booking_address'      => !empty($booking_info['address']) ? (string) $booking_info['address'] : '',
        'booking_info_data'    => $booking_info,
        'booking_age'          => !empty($feature_values[$product_id][BOOKINGS_BOOKING_AGE_FEATURE_ID])
            ? $feature_values[$product_id][BOOKINGS_BOOKING_AGE_FEATURE_ID]
            : '',
        'booking_type_name'    => !empty($feature_values[$product_id][BOOKINGS_BOOKING_TYPE_FEATURE_ID])
            ? $feature_values[$product_id][BOOKINGS_BOOKING_TYPE_FEATURE_ID]
            : '',
        'booking_slot_start'   => $booking_slot !== ''
            ? fn_bookings_get_booking_slot_start($booking_slot)
            : '',
        'booking_slot_end'     => $booking_slot !== ''
            ? fn_bookings_get_booking_slot_end($booking_slot)
            : '',
        'booking_products_total' => $products_total,
        'booking_points_cost'    => $points_cost,
        'booking_paid_total'     => $paid_total,
    ];
}

function fn_bookings_get_calendar_event_data(array $order_info): ?array
{
    $booking_info = !empty($order_info['booking_info_data']) && is_array($order_info['booking_info_data'])
        ? $order_info['booking_info_data']
        : [];

    if (
        empty($booking_info['booking_type'])
        || $booking_info['booking_type'] !== 'T'
        || empty($booking_info['booking_date'])
    ) {
        return null;
    }

    $start_time = !empty($order_info['booking_slot_start'])
        ? (string) $order_info['booking_slot_start']
        : '';
    if ($start_time === '') {
        return null;
    }

    $timezone = new DateTimeZone(date_default_timezone_get());
    $start_date = (new DateTimeImmutable('@' . (int) $booking_info['booking_date']))->setTimezone($timezone);
    $start_datetime = DateTimeImmutable::createFromFormat(
        'Y-m-d H:i',
        $start_date->format('Y-m-d') . ' ' . $start_time,
        $timezone
    );

    if (!$start_datetime) {
        return null;
    }

    $end_time = !empty($order_info['booking_slot_end']) ? (string) $order_info['booking_slot_end'] : '';
    $end_datetime = $end_time !== ''
        ? DateTimeImmutable::createFromFormat(
            'Y-m-d H:i',
            $start_date->format('Y-m-d') . ' ' . $end_time,
            $timezone
        )
        : $start_datetime->modify('+1 hour');

    if (!$end_datetime || $end_datetime <= $start_datetime) {
        $end_datetime = $start_datetime->modify('+1 hour');
    }

    $description_parts = [];
    if (!empty($order_info['booking_address'])) {
        $description_parts[] = __('bookings.booking_address') . ': ' . $order_info['booking_address'];
    }
    if (!empty($order_info['notes'])) {
        $description_parts[] = __('customer_notes') . ': ' . trim((string) $order_info['notes']);
    }

    return [
        'title'       => !empty($order_info['booking_product_name'])
            ? (string) $order_info['booking_product_name']
            : __('order') . ' #' . (int) $order_info['order_id'],
        'location'    => !empty($order_info['booking_address'])
            ? (string) $order_info['booking_address']
            : '',
        'description' => implode("\n", $description_parts),
        'start'       => $start_datetime,
        'end'         => $end_datetime,
    ];
}

function fn_bookings_build_ics_content(array $event_data): string
{
    $uid = sprintf(
        'booking-%s@%s',
        md5($event_data['title'] . $event_data['start']->format('c')),
        $_SERVER['HTTP_HOST'] ?? 'localhost'
    );
    $stamp = new DateTimeImmutable('now', new DateTimeZone('UTC'));

    $lines = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//Larionov.tech//Bookings//RU',
        'CALSCALE:GREGORIAN',
        'METHOD:PUBLISH',
        'BEGIN:VEVENT',
        'UID:' . fn_bookings_escape_ics_text($uid),
        'DTSTAMP:' . $stamp->format('Ymd\THis\Z'),
        'DTSTART:' . $event_data['start']->format('Ymd\THis'),
        'DTEND:' . $event_data['end']->format('Ymd\THis'),
        'SUMMARY:' . fn_bookings_escape_ics_text($event_data['title']),
    ];

    if ($event_data['location'] !== '') {
        $lines[] = 'LOCATION:' . fn_bookings_escape_ics_text($event_data['location']);
    }

    if ($event_data['description'] !== '') {
        $lines[] = 'DESCRIPTION:' . fn_bookings_escape_ics_text($event_data['description']);
    }

    $lines[] = 'END:VEVENT';
    $lines[] = 'END:VCALENDAR';

    return implode("\r\n", $lines) . "\r\n";
}

function fn_bookings_escape_ics_text(string $value): string
{
    $value = str_replace('\\', '\\\\', $value);
    $value = str_replace(';', '\;', $value);
    $value = str_replace(',', '\,', $value);
    $value = preg_replace("/\r\n|\r|\n/", '\n', $value);

    return (string) $value;
}

function fn_bookings_get_booking_feature_values(array $product_ids, string $lang_code): array
{
    static $cache = [];

    $product_ids = array_values(array_unique(array_filter(array_map('intval', $product_ids))));
    if (!$product_ids) {
        return [];
    }

    $features = [];
    $missing_product_ids = [];

    foreach ($product_ids as $product_id) {
        if (isset($cache[$lang_code][$product_id])) {
            $features[$product_id] = $cache[$lang_code][$product_id];
        } else {
            $missing_product_ids[] = $product_id;
        }
    }

    if ($missing_product_ids) {
        $rows = db_get_array(
            'SELECT values_data.product_id, values_data.feature_id,'
            . ' COALESCE(variant_descriptions.variant, NULLIF(values_data.value, \'\'),'
            . ' NULLIF(CAST(values_data.value_int AS CHAR), \'\')) AS feature_value'
            . ' FROM ?:product_features_values AS values_data'
            . ' LEFT JOIN ?:product_feature_variant_descriptions AS variant_descriptions'
            . ' ON variant_descriptions.variant_id = values_data.variant_id AND variant_descriptions.lang_code = ?s'
            . ' WHERE values_data.product_id IN (?n)'
            . ' AND values_data.feature_id IN (?n)'
            . ' AND values_data.lang_code = ?s',
            $lang_code,
            $missing_product_ids,
            [BOOKINGS_BOOKING_AGE_FEATURE_ID, BOOKINGS_BOOKING_TYPE_FEATURE_ID],
            $lang_code
        );

        foreach ($missing_product_ids as $product_id) {
            $cache[$lang_code][$product_id] = [];
        }

        foreach ($rows as $row) {
            if ($row['feature_value'] === null || $row['feature_value'] === '') {
                continue;
            }

            $cache[(string) $lang_code][(int) $row['product_id']][(int) $row['feature_id']]
                = (string) $row['feature_value'];
        }

        foreach ($missing_product_ids as $product_id) {
            $features[$product_id] = $cache[$lang_code][$product_id];
        }
    }

    return $features;
}

function fn_bookings_get_orders_points_costs(array $order_ids): array
{
    static $cache = [];

    $order_ids = array_values(array_unique(array_filter(array_map('intval', $order_ids))));
    if (!$order_ids) {
        return [];
    }

    $points_costs = [];
    $missing_order_ids = [];

    foreach ($order_ids as $order_id) {
        if (isset($cache[$order_id])) {
            $points_costs[$order_id] = $cache[$order_id];
        } else {
            $missing_order_ids[] = $order_id;
        }
    }

    if ($missing_order_ids) {
        $rows = db_get_array(
            'SELECT order_id, data FROM ?:order_data WHERE order_id IN (?n) AND type = ?s',
            $missing_order_ids,
            defined('POINTS_IN_USE') ? POINTS_IN_USE : 'I'
        );

        foreach ($missing_order_ids as $order_id) {
            $cache[$order_id] = 0.0;
        }

        foreach ($rows as $row) {
            $points_info = @unserialize($row['data']);
            if (!is_array($points_info) || empty($points_info['cost'])) {
                continue;
            }

            $cache[(int) $row['order_id']] = (float) $points_info['cost'];
        }

        foreach ($missing_order_ids as $order_id) {
            $points_costs[$order_id] = $cache[$order_id];
        }
    }

    return $points_costs;
}
