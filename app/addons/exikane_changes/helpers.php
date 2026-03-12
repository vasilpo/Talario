<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
defined('BOOTSTRAP') or die('Access denied');
// phpcs:enable PSR1.Files.SideEffects.FoundWithSymbols

/**
 * Gets partner website URL by product identifier.
 *
 * @param int $product_id Product identifier
 *
 * @return string
 */
function fn_exikane_changes_get_partner_site($product_id)
{
    $site = (string) db_get_field(
        'SELECT site FROM ?:exikane_partner_product_sites WHERE product_id = ?i',
        $product_id
    );

    return trim($site);
}

/**
 * Normalizes website URL by prepending protocol when needed.
 *
 * @param string $url Website URL
 *
 * @return string
 */
function fn_exikane_changes_normalize_site_url($url)
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }

    if (!preg_match('~^https?://~i', $url)) {
        $url = 'http://' . $url;
    }

    return $url;
}

/**
 * Appends fixed UTM parameters to partner website URL.
 *
 * @param string $url Website URL
 *
 * @return string
 */
function fn_exikane_changes_attach_partner_utm($url)
{
    return fn_link_attach($url, 'utm_source=talario&utm_medium=partner&utm_campaign=partner_site');
}

/**
 * Logs partner website click.
 *
 * @param int   $product_id Product identifier
 * @param array $auth       Authorization data
 *
 * @return int|string
 */
function fn_exikane_changes_log_partner_click($product_id, array $auth)
{
    $user_id = isset($auth['user_id']) ? (int) $auth['user_id'] : 0;
    $email = '';

    if ($user_id > 0) {
        $email = !empty($auth['email']) ? (string) $auth['email'] : '';
        if ($email === '') {
            $user_data = fn_get_user_info($user_id);
            if (!empty($user_data['email'])) {
                $email = (string) $user_data['email'];
            }
        }
    }

    return db_query('INSERT INTO ?:exikane_partner_site_clicks ?e', [
        'user_id'    => $user_id,
        'email'      => $email,
        'product_id' => (int) $product_id,
        /** @phpstan-ignore-next-line Runtime CS-Cart timestamp constant. */
        'timestamp'  => TIME,
    ]);
}

/**
 * Loads product names in batch with a fallback to the core name resolver.
 *
 * @param array  $product_ids Product identifiers
 * @param string $lang_code   Language code
 *
 * @return array<int, string>
 */
function fn_exikane_changes_get_product_names(array $product_ids, $lang_code)
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
            'SELECT product_id, product'
            . ' FROM ?:product_descriptions'
            . ' WHERE product_id IN (?n) AND lang_code = ?s',
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

/**
 * Safely unserializes stored order item extra data.
 *
 * @param string $serialized_extra Serialized extra data from order details
 *
 * @return array
 */
function fn_exikane_changes_unserialize_order_extra($serialized_extra)
{
    if ($serialized_extra === '') {
        return [];
    }

    $extra = @unserialize($serialized_extra);

    return is_array($extra) ? $extra : [];
}

/**
 * Extracts normalized booking info from an order product.
 *
 * @param array $product Order product data
 *
 * @return array
 */
function fn_exikane_changes_get_product_booking_info(array $product)
{
    return !empty($product['extra']['booking_info']) && is_array($product['extra']['booking_info'])
        ? $product['extra']['booking_info']
        : [];
}

/**
 * Extracts the start time from a booking slot string.
 *
 * @param string $booking_slot Booking slot value
 *
 * @return string
 */
function fn_exikane_changes_get_booking_slot_start($booking_slot)
{
    if ($booking_slot === '') {
        return '';
    }

    $slot_parts = preg_split('/\s*-\s*/', $booking_slot);

    return !empty($slot_parts[0]) ? trim((string) $slot_parts[0]) : trim($booking_slot);
}

/**
 * Extracts the end time from a booking slot string.
 *
 * @param string $booking_slot Booking slot value
 *
 * @return string
 */
function fn_exikane_changes_get_booking_slot_end($booking_slot)
{
    if ($booking_slot === '') {
        return '';
    }

    $slot_parts = preg_split('/\s*-\s*/', $booking_slot);

    return !empty($slot_parts[1]) ? trim((string) $slot_parts[1]) : '';
}

/**
 * Builds normalized booking payload for storefront order views.
 *
 * @param int    $order_id       Order identifier
 * @param array  $product_data   Product data
 * @param array  $feature_values Product feature values indexed by product identifier
 * @param float  $products_total Total products amount
 * @param float  $points_cost    Deducted points cost
 * @param float  $paid_total     Final paid total
 *
 * @return array<string, mixed>
 */
function fn_exikane_changes_build_booking_payload(
    $order_id,
    array $product_data,
    array $feature_values,
    $products_total,
    $points_cost,
    $paid_total
) {
    $product_id = !empty($product_data['product_id']) ? (int) $product_data['product_id'] : 0;
    $booking_info = !empty($product_data['booking_info']) && is_array($product_data['booking_info'])
        ? $product_data['booking_info']
        : [];
    $booking_slot = !empty($booking_info['booking_slot']) ? (string) $booking_info['booking_slot'] : '';

    return [
        'exikane_booking_product_id'   => $product_id,
        'exikane_booking_product_name' => !empty($product_data['product_name'])
            ? (string) $product_data['product_name']
            : __('order') . ' #' . (int) $order_id,
        'exikane_booking_address'      => !empty($booking_info['address']) ? (string) $booking_info['address'] : '',
        'exikane_booking_info'         => $booking_info,
        /** @phpstan-ignore-next-line Runtime CS-Cart booking age feature constant. */
        'exikane_booking_age'          => !empty($feature_values[$product_id][EXIKANE_CHANGES_BOOKING_AGE_FEATURE_ID])
            /** @phpstan-ignore-next-line Runtime CS-Cart booking age feature constant. */
            ? $feature_values[$product_id][EXIKANE_CHANGES_BOOKING_AGE_FEATURE_ID]
            : '',
        /** @phpstan-ignore-next-line Runtime CS-Cart booking type feature constant. */
        'exikane_booking_type'         => !empty($feature_values[$product_id][EXIKANE_CHANGES_BOOKING_TYPE_FEATURE_ID])
            /** @phpstan-ignore-next-line Runtime CS-Cart booking type feature constant. */
            ? $feature_values[$product_id][EXIKANE_CHANGES_BOOKING_TYPE_FEATURE_ID]
            : '',
        'exikane_booking_slot_start'   => $booking_slot !== ''
            ? fn_exikane_changes_get_booking_slot_start($booking_slot)
            : '',
        'exikane_booking_slot_end'     => $booking_slot !== ''
            ? fn_exikane_changes_get_booking_slot_end($booking_slot)
            : '',
        'exikane_products_total'       => (float) $products_total,
        'exikane_points_cost'          => (float) $points_cost,
        'exikane_paid_total'           => (float) $paid_total,
    ];
}

/**
 * Prepares calendar event data for a booking order.
 *
 * @param array $order_info Order information
 *
 * @return array|null
 */
function fn_exikane_changes_get_calendar_event_data(array $order_info)
{
    $booking_info = !empty($order_info['exikane_booking_info']) && is_array($order_info['exikane_booking_info'])
        ? $order_info['exikane_booking_info']
        : [];

    if (
        empty($booking_info['booking_type'])
        || $booking_info['booking_type'] !== 'T'
        || empty($booking_info['booking_date'])
    ) {
        return null;
    }

    $start_time = !empty($order_info['exikane_booking_slot_start'])
        ? (string) $order_info['exikane_booking_slot_start']
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

    $end_time = !empty($order_info['exikane_booking_slot_end'])
        ? (string) $order_info['exikane_booking_slot_end']
        : '';
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
    if (!empty($order_info['exikane_booking_address'])) {
        $description_parts[] = __('exikane_changes.booking_address') . ': ' . $order_info['exikane_booking_address'];
    }
    if (!empty($order_info['notes'])) {
        $description_parts[] = __('customer_notes') . ': ' . trim((string) $order_info['notes']);
    }

    return [
        'title'       => !empty($order_info['exikane_booking_product_name'])
            ? (string) $order_info['exikane_booking_product_name']
            : __('order') . ' #' . (int) $order_info['order_id'],
        'location'    => !empty($order_info['exikane_booking_address'])
            ? (string) $order_info['exikane_booking_address']
            : '',
        'description' => implode("\n", $description_parts),
        'start'       => $start_datetime,
        'end'         => $end_datetime,
    ];
}

/**
 * Builds ICS content for a booking event.
 *
 * @param array $event_data Calendar event data
 *
 * @return string
 */
function fn_exikane_changes_build_ics_content(array $event_data)
{
    $uid = sprintf(
        'exikane-booking-%s@%s',
        md5($event_data['title'] . $event_data['start']->format('c')),
        $_SERVER['HTTP_HOST'] ?? 'localhost'
    );
    $stamp = new DateTimeImmutable('now', new DateTimeZone('UTC'));

    $lines = [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//Exikane//Bookings//RU',
        'CALSCALE:GREGORIAN',
        'METHOD:PUBLISH',
        'BEGIN:VEVENT',
        'UID:' . fn_exikane_changes_escape_ics_text($uid),
        'DTSTAMP:' . $stamp->format('Ymd\THis\Z'),
        'DTSTART:' . $event_data['start']->format('Ymd\THis'),
        'DTEND:' . $event_data['end']->format('Ymd\THis'),
        'SUMMARY:' . fn_exikane_changes_escape_ics_text($event_data['title']),
    ];

    if ($event_data['location'] !== '') {
        $lines[] = 'LOCATION:' . fn_exikane_changes_escape_ics_text($event_data['location']);
    }

    if ($event_data['description'] !== '') {
        $lines[] = 'DESCRIPTION:' . fn_exikane_changes_escape_ics_text($event_data['description']);
    }

    $lines[] = 'END:VEVENT';
    $lines[] = 'END:VCALENDAR';

    return implode("\r\n", $lines) . "\r\n";
}

/**
 * Escapes ICS text values.
 *
 * @param string $value Raw value
 *
 * @return string
 */
function fn_exikane_changes_escape_ics_text($value)
{
    $value = str_replace('\\', '\\\\', (string) $value);
    $value = str_replace(';', '\;', $value);
    $value = str_replace(',', '\,', $value);
    $value = preg_replace("/\r\n|\r|\n/", '\n', $value);

    return (string) $value;
}

/**
 * Loads required booking feature values for products in one query.
 *
 * @param array  $product_ids Product identifiers
 * @param string $lang_code   Language code
 *
 * @return array<int, array<int, string>>
 */
function fn_exikane_changes_get_booking_feature_values(array $product_ids, $lang_code)
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
            [
                /** @phpstan-ignore-next-line Runtime CS-Cart booking feature constants. */
                EXIKANE_CHANGES_BOOKING_AGE_FEATURE_ID,
                /** @phpstan-ignore-next-line Runtime CS-Cart booking feature constants. */
                EXIKANE_CHANGES_BOOKING_TYPE_FEATURE_ID,
            ],
            $lang_code
        );

        foreach ($missing_product_ids as $product_id) {
            $cache[$lang_code][$product_id] = [];
        }

        foreach ($rows as $row) {
            if ($row['feature_value'] === null || $row['feature_value'] === '') {
                continue;
            }

            $product_id = (int) $row['product_id'];
            $feature_id = (int) $row['feature_id'];
            $feature_value = (string) $row['feature_value'];

            $cache[$lang_code][$product_id][$feature_id] = $feature_value;
        }

        foreach ($missing_product_ids as $product_id) {
            $features[$product_id] = $cache[$lang_code][$product_id];
        }
    }

    return $features;
}

/**
 * Loads reward points deduction cost for orders.
 *
 * @param array $order_ids Order identifiers
 *
 * @return array<int, float>
 */
function fn_exikane_changes_get_orders_points_costs(array $order_ids)
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
