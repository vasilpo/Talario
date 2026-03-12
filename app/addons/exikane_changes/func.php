<?php

use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\UserTypes;
use Tygh\Enum\YesNo;
use Tygh\Registry;

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
defined('BOOTSTRAP') or die('Access denied');

require_once __DIR__ . '/helpers.php';
// phpcs:enable PSR1.Files.SideEffects.FoundWithSymbols

/**
 * The `get_product_data_post` hook handler.
 *
 * @param array  $product_data Product data
 * @param array  $auth         Authorization data
 * @param bool   $preview      Preview mode flag
 * @param string $lang_code    Two-letter language code
 *
 * @return void
 */
function fn_exikane_changes_get_product_data_post(&$product_data, $auth, $preview, $lang_code)
{
    if (empty($product_data['product_id'])) {
        return;
    }

    $site = fn_exikane_changes_get_partner_site((int) $product_data['product_id']);
    if ($site !== '') {
        $product_data['exikane_partner_site'] = $site;
    }
}

/**
 * The `update_profile` hook handler.
 *
 * Adds welcome reward points after a customer profile is created.
 *
 * @param string $action            Profile update action
 * @param array  $user_data         Updated user data
 * @param array  $current_user_data Current user data
 *
 * @return void
 */
function fn_exikane_changes_update_profile($action, $user_data, $current_user_data)
{
    if ($action !== 'add' || Registry::get('addons.reward_points.status') !== ObjectStatuses::ACTIVE) {
        return;
    }

    $addons = Registry::get('addons.exikane_changes');
    if (empty($addons['welcome_bonus_enabled']) || $addons['welcome_bonus_enabled'] !== YesNo::YES) {
        return;
    }

    $amount = isset($addons['welcome_bonus_amount']) ? (int) $addons['welcome_bonus_amount'] : 0;
    if ($amount <= 0) {
        return;
    }

    $user_id = !empty($user_data['user_id'])
        ? (int) $user_data['user_id']
        : (!empty($current_user_data['user_id']) ? (int) $current_user_data['user_id'] : 0);
    if ($user_id <= 0) {
        return;
    }

    $user_type = !empty($user_data['user_type'])
        ? $user_data['user_type']
        : (!empty($current_user_data['user_type']) ? $current_user_data['user_type'] : '');
    if ($user_type && $user_type !== UserTypes::CUSTOMER) {
        return;
    }

    fn_change_user_points($amount, $user_id, __('exikane_changes.welcome_bonus_reason'), CHANGE_DUE_ADDITION);
}

/**
 * The `set_point_payment` hook handler.
 *
 * Limits the maximum reward points that can be applied to the order total.
 *
 * @param array $cart                           Array of cart data.
 * @param array $cart_products                  List of cart products.
 * @param array $auth                           Array of user authentication data.
 * @param array $user_info                      Array of user data.
 * @param float $cost_covered_by_applied_points Total sum of products covered by previously applied points.
 * @param float $point_exchange_rate            The number of points equal to 1 conventional unit.
 * @param float $user_points                    Total sum of points available for user.
 *
 * @return void
 */
function fn_exikane_changes_set_point_payment(
    &$cart,
    &$cart_products,
    &$auth,
    &$user_info,
    &$cost_covered_by_applied_points,
    &$point_exchange_rate,
    &$user_points
) {
    $addons = Registry::get('addons.exikane_changes');
    $max_percent = isset($addons['max_points_percent']) ? (float) $addons['max_points_percent'] : 0.0;
    if ($max_percent <= 0 || empty($cart['points_info']['in_use']['points']) || $point_exchange_rate <= 0) {
        return;
    }

    $base_total = isset($cart['subtotal']) ? (float) $cart['subtotal'] : 0.0;
    $base_total += !empty($cart['shipping_cost']) ? (float) $cart['shipping_cost'] : 0.0;
    $base_total += !empty($cart['tax_subtotal']) ? (float) $cart['tax_subtotal'] : 0.0;

    if (!empty($cart['subtotal_discount'])) {
        $discount_without_points = (float) $cart['subtotal_discount'] - (float) $cost_covered_by_applied_points;
        if ($discount_without_points > 0) {
            $base_total -= $discount_without_points;
        }
    }

    if ($base_total <= 0) {
        return;
    }

    $max_points = (int) floor(($base_total * ($max_percent / 100)) * $point_exchange_rate);
    $points_in_use = (int) $cart['points_info']['in_use']['points'];

    if ($points_in_use > $max_points) {
        fn_set_notification(
            'E',
            __('error'),
            __('exikane_changes.reward_points_limit_exceeded', ['[points]' => $max_points])
        );
        unset($cart['points_info']['in_use']);
    }
}

/**
 * The `update_product_post` hook handler.
 *
 * @param array  $product_data Product data
 * @param int    $product_id   Product identifier
 * @param string $lang_code    Two-letter language code
 * @param bool   $create       True when product is created
 *
 * @return void
 */
function fn_exikane_changes_update_product_post($product_data, $product_id, $lang_code, $create)
{
    if (!isset($product_data['exikane_partner_site'])) {
        return;
    }

    $site = trim((string) $product_data['exikane_partner_site']);
    if ($site === '') {
        db_query('DELETE FROM ?:exikane_partner_product_sites WHERE product_id = ?i', $product_id);
        return;
    }

    db_query('REPLACE INTO ?:exikane_partner_product_sites ?e', [
        'product_id' => (int) $product_id,
        'site'       => $site,
    ]);
}

/**
 * The `delete_product_post` hook handler.
 *
 * @param int  $product_id      Product identifier
 * @param bool $product_deleted Product deletion result
 *
 * @return void
 */
function fn_exikane_changes_delete_product_post($product_id, $product_deleted)
{
    if (!$product_deleted) {
        return;
    }

    db_query('DELETE FROM ?:exikane_partner_product_sites WHERE product_id = ?i', $product_id);
    db_query('DELETE FROM ?:exikane_partner_site_clicks WHERE product_id = ?i', $product_id);
}

/**
 * The `get_order_info` hook handler.
 *
 * Enriches storefront order details with booking-related data for the first product.
 *
 * @param array $order           Order information
 * @param array $additional_data Additional order data
 *
 * @return void
 */
function fn_exikane_changes_get_order_info(&$order, &$additional_data)
{
    /** @phpstan-ignore-next-line Runtime CS-Cart area constant. */
    if (AREA !== 'C' || empty($order) || empty($order['products'])) {
        return;
    }

    $first_product = reset($order['products']);
    if (empty($first_product['product_id'])) {
        return;
    }

    $product_id = (int) $first_product['product_id'];
    /** @phpstan-ignore-next-line Runtime CS-Cart language constant. */
    $feature_values = fn_exikane_changes_get_booking_feature_values([$product_id], CART_LANGUAGE);
    $points_cost = !empty($order['points_info']['in_use']['cost'])
        ? (float) $order['points_info']['in_use']['cost']
        : 0.0;
    $products_total = !empty($first_product['display_subtotal'])
        ? (float) $first_product['display_subtotal']
        : (float) $order['subtotal'];

    $order = array_merge($order, fn_exikane_changes_build_booking_payload(
        $order['order_id'],
        [
            'product_id'   => $product_id,
            'product_name' => !empty($first_product['product']) ? (string) $first_product['product'] : '',
            'booking_info' => fn_exikane_changes_get_product_booking_info($first_product),
        ],
        $feature_values,
        $products_total,
        $points_cost,
        (float) $order['total']
    ));

    $order['exikane_calendar_event_available'] = fn_exikane_changes_get_calendar_event_data($order) !== null;
}

/**
 * The `pre_get_orders` hook handler.
 *
 * Normalizes storefront booking filter inputs into core order search parameters.
 *
 * @param array  $params      Search parameters
 * @param array  $fields      Selected fields
 * @param array  $sortings    Available sortings
 * @param bool   $get_totals  Totals calculation flag
 * @param string $lang_code   Current language code
 *
 * @return void
 */
function fn_exikane_changes_pre_get_orders(&$params, &$fields, &$sortings, &$get_totals, &$lang_code)
{
    /** @phpstan-ignore-next-line Runtime CS-Cart area constant. */
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
 * The `get_orders` hook handler.
 *
 * Extends storefront order search with filtering by the first product name in the order.
 *
 * @param array  $params    Search parameters
 * @param array  $fields    Selected fields
 * @param array  $sortings  Available sortings
 * @param string $condition SQL conditions
 * @param string $join      SQL joins
 * @param string $group     SQL grouping
 *
 * @return void
 */
function fn_exikane_changes_get_orders(&$params, &$fields, &$sortings, &$condition, &$join, &$group)
{
    /** @phpstan-ignore-next-line Runtime CS-Cart area constant. */
    if (AREA !== 'C' || empty($params['query'])) {
        return;
    }

    $query = trim((string) $params['query']);
    if ($query === '') {
        return;
    }

    $join .= ' LEFT JOIN ?:order_details AS exikane_order_details'
        . ' ON exikane_order_details.order_id = ?:orders.order_id';
    $join .= db_quote(
        ' LEFT JOIN ?:product_descriptions AS exikane_product_descriptions'
        . ' ON exikane_product_descriptions.product_id = exikane_order_details.product_id'
        . ' AND exikane_product_descriptions.lang_code = ?s',
        /** @phpstan-ignore-next-line Runtime CS-Cart language constant. */
        CART_LANGUAGE
    );

    $condition .= db_quote(
        ' AND exikane_product_descriptions.product LIKE ?l',
        '%' . $query . '%'
    );
    $group = ' GROUP BY ?:orders.order_id ';
}

/**
 * The `get_orders_post` hook handler.
 *
 * Enriches storefront order list items with booking-related data for the first product in each order.
 *
 * @param array $params Search parameters
 * @param array $orders Found orders
 *
 * @return void
 */
function fn_exikane_changes_get_orders_post($params, &$orders)
{
    /** @phpstan-ignore-next-line Runtime CS-Cart area constant. */
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
                'booking_info'   => fn_exikane_changes_unserialize_order_extra($order_item['extra']),
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
    /** @phpstan-ignore-next-line Runtime CS-Cart language constant. */
    $product_names = fn_exikane_changes_get_product_names($product_ids, CART_LANGUAGE);
    /** @phpstan-ignore-next-line Runtime CS-Cart language constant. */
    $feature_values = fn_exikane_changes_get_booking_feature_values($product_ids, CART_LANGUAGE);
    $points_costs = fn_exikane_changes_get_orders_points_costs($order_ids);

    foreach ($orders as &$order) {
        $order_id = (int) $order['order_id'];
        $product_id = !empty($orders_meta[$order_id]['product_id']) ? (int) $orders_meta[$order_id]['product_id'] : 0;
        $products_total = isset($orders_meta[$order_id]['products_total'])
            ? (float) $orders_meta[$order_id]['products_total']
            : (float) $order['total'];
        $points_cost = !empty($points_costs[$order_id]) ? (float) $points_costs[$order_id] : 0.0;

        $booking_payload = fn_exikane_changes_build_booking_payload(
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
