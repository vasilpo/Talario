<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
defined('BOOTSTRAP') or die('Access denied');

require_once __DIR__ . '/helpers.php';
// phpcs:enable PSR1.Files.SideEffects.FoundWithSymbols

/**
 * Hook handler for `get_order_info`.
 *
 * Extends storefront order data with booking payload.
 *
 * @param array $order           Order data
 * @param array $additional_data Additional order data
 *
 * @return void
 *
 * @see fn_get_order_info()
 */
function fn_bookings_get_order_info(&$order, &$additional_data): void
{
    if (AREA !== 'C' || empty($order) || empty($order['products'])) {
        return;
    }

    $first_product = reset($order['products']);
    if (empty($first_product['product_id'])) {
        return;
    }

    $product_id = (int) $first_product['product_id'];
    $feature_values = fn_bookings_get_booking_feature_values([$product_id], CART_LANGUAGE);
    $points_cost = !empty($order['points_info']['in_use']['cost'])
        ? (float) $order['points_info']['in_use']['cost']
        : 0.0;
    $products_total = !empty($first_product['display_subtotal'])
        ? (float) $first_product['display_subtotal']
        : (float) $order['subtotal'];

    $order = array_merge($order, fn_bookings_build_booking_payload(
        (int) $order['order_id'],
        [
            'product_id'   => $product_id,
            'product_name' => !empty($first_product['product']) ? (string) $first_product['product'] : '',
            'booking_info' => fn_bookings_get_product_booking_info($first_product),
        ],
        $feature_values,
        $products_total,
        $points_cost,
        (float) $order['total']
    ));

    $order['booking_calendar_event_available'] = fn_bookings_get_calendar_event_data($order) !== null;
}

/**
 * Hook handler for `pre_get_orders`.
 *
 * Normalizes storefront booking filters into core order search params.
 *
 * @param array  $params     Search parameters
 * @param array  $fields     Selected fields
 * @param array  $sortings   Available sortings
 * @param bool   $get_totals Totals flag
 * @param string $lang_code  Language code
 *
 * @return void
 *
 * @see fn_get_orders()
 */
function fn_bookings_pre_get_orders(&$params, &$fields, &$sortings, &$get_totals, &$lang_code): void
{
    if (AREA !== 'C') {
        return;
    }

    if (isset($params['status_filter'])) {
        $status_filter = trim((string) $params['status_filter']);
        if ($status_filter !== '') {
            $params['status'] = [$status_filter];
        } else {
            unset($params['status']);
        }
    }

    if (!empty($params['sort_token'])) {
        $sort_parts = explode('_', (string) $params['sort_token']);
        $sort_order = array_pop($sort_parts);
        $sort_by = implode('_', $sort_parts);

        if (isset($sortings[$sort_by]) && in_array($sort_order, ['asc', 'desc'], true)) {
            $params['sort_by'] = $sort_by;
            $params['sort_order'] = $sort_order;
        }
    }

    if (isset($params['query'])) {
        $params['query'] = trim((string) $params['query']);
    }
}

/**
 * Hook handler for `get_orders`.
 *
 * Adds product-name search support to storefront bookings list.
 *
 * @param array  $params    Search parameters
 * @param array  $fields    Selected fields
 * @param array  $sortings  Available sortings
 * @param string $condition SQL condition
 * @param string $join      SQL joins
 * @param string $group     SQL group
 *
 * @return void
 *
 * @see fn_get_orders()
 */
function fn_bookings_get_orders(&$params, &$fields, &$sortings, &$condition, &$join, &$group): void
{
    if (AREA !== 'C' || empty($params['query'])) {
        return;
    }

    $query = trim((string) $params['query']);
    if ($query === '') {
        return;
    }

    $join .= ' LEFT JOIN ?:order_details AS booking_order_details'
        . ' ON booking_order_details.order_id = ?:orders.order_id';
    $join .= db_quote(
        ' LEFT JOIN ?:product_descriptions AS booking_product_descriptions'
        . ' ON booking_product_descriptions.product_id = booking_order_details.product_id'
        . ' AND booking_product_descriptions.lang_code = ?s',
        CART_LANGUAGE
    );

    $condition .= db_quote(
        ' AND booking_product_descriptions.product LIKE ?l',
        '%' . $query . '%'
    );
    $group = ' GROUP BY ?:orders.order_id ';
}

/**
 * Hook handler for `get_orders_post`.
 *
 * Enriches storefront bookings list rows with derived booking data.
 *
 * @param array $params Search parameters
 * @param array $orders Found orders
 *
 * @return void
 *
 * @see fn_get_orders()
 */
function fn_bookings_get_orders_post($params, &$orders): void
{
    if (AREA !== 'C' || empty($orders)) {
        return;
    }

    $order_ids = array_values(array_filter(array_map('intval', array_column($orders, 'order_id'))));
    if (!$order_ids) {
        return;
    }

    $order_items = db_get_array(
        'SELECT order_id, item_id, product_id, extra, price, amount'
        . ' FROM ?:order_details'
        . ' WHERE order_id IN (?n)'
        . ' ORDER BY order_id ASC, item_id ASC',
        $order_ids
    );

    $orders_meta = [];
    $product_ids = [];

    foreach ($order_items as $order_item) {
        $order_id = (int) $order_item['order_id'];

        if (!isset($orders_meta[$order_id])) {
            $orders_meta[$order_id] = [
                'product_id'     => (int) $order_item['product_id'],
                'booking_info'   => fn_bookings_unserialize_order_extra((string) $order_item['extra']),
                'products_total' => 0.0,
            ];

            $orders_meta[$order_id]['booking_info'] = !empty($orders_meta[$order_id]['booking_info']['booking_info'])
                && is_array($orders_meta[$order_id]['booking_info']['booking_info'])
                ? $orders_meta[$order_id]['booking_info']['booking_info']
                : [];

            if (!empty($order_item['product_id'])) {
                $product_ids[] = (int) $order_item['product_id'];
            }
        }

        $orders_meta[$order_id]['products_total'] += (float) $order_item['price'] * (float) $order_item['amount'];
    }

    $product_ids = array_values(array_unique($product_ids));
    $product_names = fn_bookings_get_product_names($product_ids, CART_LANGUAGE);
    $feature_values = fn_bookings_get_booking_feature_values($product_ids, CART_LANGUAGE);
    $points_costs = fn_bookings_get_orders_points_costs($order_ids);

    foreach ($orders as &$order) {
        $order_id = (int) $order['order_id'];
        $product_id = !empty($orders_meta[$order_id]['product_id'])
            ? (int) $orders_meta[$order_id]['product_id']
            : 0;
        $products_total = isset($orders_meta[$order_id]['products_total'])
            ? (float) $orders_meta[$order_id]['products_total']
            : (float) $order['total'];
        $points_cost = !empty($points_costs[$order_id]) ? (float) $points_costs[$order_id] : 0.0;

        $booking_payload = fn_bookings_build_booking_payload(
            $order_id,
            [
                'product_id'   => $product_id,
                'product_name' => !empty($product_names[$product_id]) ? $product_names[$product_id] : '',
                'booking_info' => !empty($orders_meta[$order_id]['booking_info'])
                    ? $orders_meta[$order_id]['booking_info']
                    : [],
            ],
            $feature_values,
            $products_total,
            $points_cost,
            max(0, $products_total - $points_cost)
        );

        foreach ($booking_payload as $payload_key => $payload_value) {
            $order[$payload_key] = $payload_value;
        }
    }
    unset($order);
}
