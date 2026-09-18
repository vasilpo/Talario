<?php

defined('BOOTSTRAP') or die('Access denied');

fn_register_hooks(
    'place_order'
);

/**
 * Keep Yandex Ecommerce aligned with the final persisted order instead of the
 * mutable checkout cart. Free booking orders are tracked by a separate goal
 * and must not inflate paid purchase metrics.
 */
function fn_talario_analytics_place_order($order_id, $action, $order_status, $cart, $auth)
{
    if (\Tygh\Registry::get('addons.rus_yandex_metrika.ecommerce') !== 'Y') {
        return;
    }

    $order_info = fn_get_order_info((int) $order_id);
    if (empty($order_info) || empty($order_info['products'])) {
        return;
    }

    $is_booking_order = false;
    foreach ((array) $order_info['products'] as $product) {
        if (($product['extra']['booking_info']['booking_type'] ?? '') === 'T') {
            $is_booking_order = true;
            break;
        }
    }

    $total = (float) ($order_info['total'] ?? 0);

    if ($is_booking_order && $total <= 0.0) {
        unset(Tygh::$app['session']['yandex_metrika']['purchased']);
        Tygh::$app['session']['talario_analytics']['free_booking_order_id'] = (int) $order_id;
        return;
    }

    $purchased = [
        'action' => [
            'id' => (int) $order_id,
            'revenue' => $total,
        ],
        'products' => [],
    ];

    if (!empty($order_info['coupon_codes'])) {
        $purchased['action']['coupon'] = (string) $order_info['coupon_codes'];
    }

    foreach ((array) $order_info['products'] as $item_id => $product) {
        $product_id = (int) ($product['product_id'] ?? 0);
        if (!$product_id) {
            continue;
        }

        $entry = [
            'id' => $product_id,
            'quantity' => (int) ($product['amount'] ?? 1),
            'name' => (string) ($product['product'] ?? ''),
            'price' => (float) ($product['price'] ?? 0),
        ];

        $product_options = (array) ($product['extra']['product_options'] ?? []);
        if ($product_options) {
            $entry['variant'] = implode(',', array_map('intval', array_values($product_options)));
        }

        $purchased['products'][$item_id] = $entry;
    }

    Tygh::$app['session']['yandex_metrika']['purchased'] = $purchased;
}
