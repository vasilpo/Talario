<?php

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Registry;

function fn_talario_analytics_json_response(int $status, array $payload): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function fn_talario_analytics_bearer_token(): string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if (!preg_match('/^Bearer\s+(.+)$/i', trim($header), $matches)) {
        return '';
    }

    return trim($matches[1]);
}

/**
 * DB-backed fixed-window throttling:
 * - max 600 requests/min globally;
 * - max 60 requests/min per source IP hash.
 *
 * @return int Current per-IP request count in the minute bucket.
 */
function fn_talario_analytics_rate_limit(): int
{
    $now = time();
    $bucket = (int) floor($now / 60);
    $global_hash = str_repeat('0', 64);

    db_query(
        'INSERT INTO ?:talario_analytics_rate_limits'
        . ' (scope_hash, minute_bucket, request_count, updated_at)'
        . ' VALUES (?s, ?i, 1, ?i)'
        . ' ON DUPLICATE KEY UPDATE request_count = request_count + 1, updated_at = ?i',
        $global_hash,
        $bucket,
        $now,
        $now
    );

    $global_count = (int) db_get_field(
        'SELECT request_count FROM ?:talario_analytics_rate_limits'
        . ' WHERE scope_hash = ?s AND minute_bucket = ?i',
        $global_hash,
        $bucket
    );

    if ($global_count === 1) {
        db_query(
            'DELETE FROM ?:talario_analytics_rate_limits WHERE updated_at < ?i',
            $now - 7200
        );
    }

    if ($global_count > 600) {
        fn_log_event('general', 'runtime', [
            'message' => 'Talario Analytics API global rate limit exceeded',
        ]);
        fn_talario_analytics_json_response(429, ['error' => 'rate_limit_exceeded']);
    }

    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $ip_hash = hash('sha256', $ip);

    db_query(
        'INSERT INTO ?:talario_analytics_rate_limits'
        . ' (scope_hash, minute_bucket, request_count, updated_at)'
        . ' VALUES (?s, ?i, 1, ?i)'
        . ' ON DUPLICATE KEY UPDATE request_count = request_count + 1, updated_at = ?i',
        $ip_hash,
        $bucket,
        $now,
        $now
    );

    $ip_count = (int) db_get_field(
        'SELECT request_count FROM ?:talario_analytics_rate_limits'
        . ' WHERE scope_hash = ?s AND minute_bucket = ?i',
        $ip_hash,
        $bucket
    );

    if ($ip_count > 60) {
        if ($ip_count === 61) {
            fn_log_event('general', 'runtime', [
                'message' => 'Talario Analytics API per-IP rate limit exceeded',
            ]);
        }
        fn_talario_analytics_json_response(429, ['error' => 'rate_limit_exceeded']);
    }

    return $ip_count;
}

function fn_talario_analytics_authorize_orders(int $rate_count): void
{
    $stored_token_hash = trim((string) Registry::get('addons.talario_analytics.api_token'));
    if (!preg_match('/^sha256:[a-f0-9]{64}$/', $stored_token_hash)) {
        fn_talario_analytics_json_response(503, ['error' => 'analytics_api_not_configured']);
    }

    $provided_token = fn_talario_analytics_bearer_token();
    $provided_hash = 'sha256:' . hash('sha256', $provided_token);

    if (strlen($provided_token) < 32 || !hash_equals($stored_token_hash, $provided_hash)) {
        if ($rate_count === 1) {
            fn_log_event('general', 'runtime', [
                'message' => 'Talario Analytics API unauthorized request',
            ]);
        }
        fn_talario_analytics_json_response(401, ['error' => 'unauthorized']);
    }
}

/**
 * Authorizes the internal Partner Sync service principal.
 *
 * This credential is intentionally separate from analytics and customer/vendor
 * credentials. It has global read scope for partner catalog reconciliation and
 * is additionally restricted to explicitly configured source IP addresses.
 */
function fn_talario_analytics_authorize_partner_sync(int $rate_count): void
{
    // This is an internal global-read service principal used only by
    // talario-assistant. It is intentionally distinct from analytics,
    // customer, admin and vendor credentials.
    $stored_token_hash = trim((string) Registry::get('addons.talario_analytics.partner_sync_token'));
    if (!preg_match('/^sha256:[a-f0-9]{64}$/', $stored_token_hash)) {
        fn_talario_analytics_json_response(503, ['error' => 'partner_sync_api_not_configured']);
    }

    $provided_token = fn_talario_analytics_bearer_token();
    $provided_hash = 'sha256:' . hash('sha256', $provided_token);

    if (strlen($provided_token) < 48 || !hash_equals($stored_token_hash, $provided_hash)) {
        if ($rate_count === 1) {
            fn_log_event('general', 'runtime', [
                'message' => 'Talario Partner Sync API unauthorized request',
            ]);
        }
        fn_talario_analytics_json_response(401, ['error' => 'unauthorized']);
    }
}

function fn_talario_analytics_parse_date(string $value): ?DateTimeImmutable
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return null;
    }

    $timezone = new DateTimeZone('Europe/Moscow');
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value, $timezone);

    if (!$date || $date->format('Y-m-d') !== $value) {
        return null;
    }

    return $date;
}


function fn_talario_analytics_feature_values(array $product_ids, string $lang_code): array
{
    if (!$product_ids) {
        return [];
    }

    $result = [];
    $rows = db_get_array(
        'SELECT values_data.product_id, values_data.feature_id,'
        . ' feature_descriptions.description AS feature_name,'
        . ' COALESCE(variant_descriptions.variant, NULLIF(values_data.value, \'\'),'
        . ' NULLIF(CAST(values_data.value_int AS CHAR), \'\')) AS feature_value'
        . ' FROM ?:product_features_values AS values_data'
        . ' LEFT JOIN ?:product_features_descriptions AS feature_descriptions'
        . ' ON feature_descriptions.feature_id = values_data.feature_id'
        . ' AND feature_descriptions.lang_code = ?s'
        . ' LEFT JOIN ?:product_feature_variant_descriptions AS variant_descriptions'
        . ' ON variant_descriptions.variant_id = values_data.variant_id'
        . ' AND variant_descriptions.lang_code = ?s'
        . ' WHERE values_data.product_id IN (?n)'
        . ' AND values_data.lang_code = ?s'
        . ' ORDER BY values_data.product_id ASC, values_data.feature_id ASC',
        $lang_code,
        $lang_code,
        $product_ids,
        $lang_code
    );

    foreach ($rows as $row) {
        $product_id = (int) $row['product_id'];
        $result[$product_id][] = [
            'feature_id' => (int) $row['feature_id'],
            'name' => (string) ($row['feature_name'] ?? ''),
            'value' => (string) ($row['feature_value'] ?? ''),
        ];
    }

    return $result;
}

function fn_talario_analytics_serialized_scalar(string $payload, string $key): string
{
    if ($payload === '' || strlen($payload) > 262144) {
        return '';
    }

    $key_pattern = preg_quote($key, '/');
    $patterns = [
        '/s:\\d+:"' . $key_pattern . '";s:\\d+:"([^"]*)";/',
        '/s:\\d+:"' . $key_pattern . '";i:(-?\\d+);/',
        '/s:\\d+:"' . $key_pattern . '";b:([01]);/',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $payload, $matches)) {
            return (string) ($matches[1] ?? '');
        }
    }

    return '';
}

function fn_talario_analytics_legacy_schedules(array $product_ids): array
{
    if (!$product_ids) {
        return [];
    }

    $rows = db_get_array(
        'SELECT product_id, booking_type, from_date, to_date, slot_time, days_data,'
        . ' quantity_selector, minimum_booking_time'
        . ' FROM ?:ec_table_booking_system'
        . ' WHERE product_id IN (?n)',
        $product_ids
    );

    $price_rows = db_get_array(
        'SELECT product_id, from_date, to_date, price'
        . ' FROM ?:ec_table_booking_system_price'
        . ' WHERE product_id IN (?n)'
        . ' ORDER BY product_id ASC, from_date ASC, to_date ASC',
        $product_ids
    );
    $price_wise = [];
    foreach ($price_rows as $price_row) {
        $price_wise[(int) $price_row['product_id']][] = [
            'from_date' => (string) $price_row['from_date'],
            'to_date' => (string) $price_row['to_date'],
            'price' => (float) $price_row['price'],
        ];
    }

    $day_map = [
        'monday' => 1,
        'tuesday' => 2,
        'wednesday' => 3,
        'thursday' => 4,
        'friday' => 5,
        'saturday' => 6,
        'sunday' => 7,
    ];

    $result = [];
    foreach ($rows as $row) {
        $product_id = (int) $row['product_id'];
        $serialized = (string) ($row['days_data'] ?? '');
        $slots = [];

        if ($serialized !== '' && strlen($serialized) <= 262144) {
            foreach ($day_map as $day_name => $weekday) {
                $enabled = fn_talario_analytics_serialized_scalar(
                    $serialized,
                    $day_name . '_status'
                );
                if ($enabled !== '1') {
                    continue;
                }

                $time_rows = [];
                if (function_exists('fn_ec_table_booking_system_get_saved_data')) {
                    $saved_rows = fn_ec_table_booking_system_get_saved_data([
                        'product_id' => $product_id,
                        'day' => $day_name,
                    ]);
                    if (is_array($saved_rows) && count($saved_rows) <= 50) {
                        $time_rows = $saved_rows;
                    }
                }

                if (!$time_rows) {
                    $start = fn_talario_analytics_serialized_scalar(
                        $serialized,
                        $day_name . '_timing_start_time'
                    );
                    $end = fn_talario_analytics_serialized_scalar(
                        $serialized,
                        $day_name . '_timing_end_time'
                    );
                    if ($start !== '') {
                        $time_rows[] = [
                            'start_time' => $start,
                            'end_time' => $end,
                            'amount' => null,
                        ];
                    }
                }

                foreach ($time_rows as $time_row) {
                    if (!is_array($time_row)) {
                        continue;
                    }
                    $start = substr(trim((string) ($time_row['start_time'] ?? '')), 0, 5);
                    $end = substr(trim((string) ($time_row['end_time'] ?? '')), 0, 5);
                    if (!preg_match('/^(?:[01]\\d|2[0-3]):[0-5]\\d$/', $start)) {
                        continue;
                    }
                    if ($end !== '' && !preg_match('/^(?:[01]\\d|2[0-3]):[0-5]\\d$/', $end)) {
                        $end = '';
                    }
                    $capacity = null;
                    if (isset($time_row['amount']) && is_numeric($time_row['amount'])) {
                        $capacity = max(0, min(10000, (int) $time_row['amount']));
                    }
                    $slots[] = [
                        'weekday' => $weekday,
                        'start_time' => $start,
                        'end_time' => $end,
                        'capacity' => $capacity,
                    ];
                }
            }
        }

        usort($slots, static function (array $left, array $right): int {
            return [$left['weekday'], $left['start_time']] <=> [$right['weekday'], $right['start_time']];
        });

        $result[$product_id] = [
            'source' => 'ec_table_booking_system',
            'booking_type' => (string) ($row['booking_type'] ?? ''),
            'from_date' => !empty($row['from_date']) ? date('Y-m-d', (int) $row['from_date']) : null,
            'to_date' => !empty($row['to_date']) ? date('Y-m-d', (int) $row['to_date']) : null,
            'duration_minutes' => (int) ($row['slot_time'] ?? 0),
            'quantity_selector' => (string) ($row['quantity_selector'] ?? ''),
            'minimum_booking_time' => (string) ($row['minimum_booking_time'] ?? ''),
            'slots' => $slots,
            'price_wise' => $price_wise[$product_id] ?? [],
        ];
    }

    return $result;
}

function fn_talario_analytics_partner_companies(): void
{
    $rows = db_get_array(
        'SELECT company_id, company, status'
        . ' FROM ?:companies'
        . ' ORDER BY company ASC, company_id ASC'
        . ' LIMIT 500'
    );

    $companies = array_map(static function (array $row): array {
        return [
            'company_id' => (int) $row['company_id'],
            'company' => (string) $row['company'],
            'status' => (string) $row['status'],
        ];
    }, $rows);

    fn_talario_analytics_json_response(200, [
        'fetched_at' => time(),
        'total' => count($companies),
        'companies' => $companies,
    ]);
}

function fn_talario_analytics_partner_snapshot(): void
{
    $company_id = isset($_REQUEST['company_id']) ? (int) $_REQUEST['company_id'] : 0;
    $product_id = isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0;

    if ($company_id <= 0 && $product_id <= 0) {
        fn_talario_analytics_json_response(400, ['error' => 'company_id_or_product_id_required']);
    }

    $lang_code = defined('CART_LANGUAGE') ? CART_LANGUAGE : 'ru';
    $condition = '';
    $query_args = [$lang_code];

    if ($product_id > 0) {
        $condition = ' AND p.product_id = ?i';
        $query_args[] = $product_id;
    } else {
        $condition = ' AND p.company_id = ?i';
        $query_args[] = $company_id;
    }

    $rows = db_get_array(
        'SELECT p.product_id, p.company_id, p.product_code, p.status,'
        . ' descriptions.product, descriptions.short_description, descriptions.full_description,'
        . ' prices.price'
        . ' FROM ?:products AS p'
        . ' INNER JOIN ?:product_descriptions AS descriptions'
        . ' ON descriptions.product_id = p.product_id AND descriptions.lang_code = ?s'
        . ' LEFT JOIN ?:product_prices AS prices'
        . ' ON prices.product_id = p.product_id'
        . ' AND prices.usergroup_id = 0 AND prices.lower_limit = 1'
        . ' WHERE 1=1'
        . $condition
        . ' ORDER BY p.product_id ASC'
        . ' LIMIT 100',
        ...$query_args
    );

    if (!$rows) {
        fn_talario_analytics_json_response(404, ['error' => 'products_not_found']);
    }

    $product_ids = array_values(array_unique(array_map(static function (array $row): int {
        return (int) $row['product_id'];
    }, $rows)));

    $feature_values = fn_talario_analytics_feature_values($product_ids, $lang_code);
    $schedules = fn_talario_analytics_legacy_schedules($product_ids);

    $variation_groups = [];
    foreach (db_get_array(
        'SELECT group_id, product_id FROM ?:product_variation_group_products'
        . ' WHERE product_id IN (?n)',
        $product_ids
    ) as $group_row) {
        $variation_groups[(int) $group_row['product_id']] = (int) $group_row['group_id'];
    }

    $company_ids = array_values(array_unique(array_map(static function (array $row): int {
        return (int) $row['company_id'];
    }, $rows)));
    $companies = [];
    if ($company_ids) {
        foreach (db_get_array(
            'SELECT company_id, company, status FROM ?:companies WHERE company_id IN (?n)',
            $company_ids
        ) as $company_row) {
            $companies[(int) $company_row['company_id']] = [
                'company_id' => (int) $company_row['company_id'],
                'company' => (string) $company_row['company'],
                'status' => (string) $company_row['status'],
            ];
        }
    }

    $products = [];
    foreach ($rows as $row) {
        $current_product_id = (int) $row['product_id'];
        $current_company_id = (int) $row['company_id'];

        $products[] = [
            'product_id' => $current_product_id,
            'company_id' => $current_company_id,
            'company' => $companies[$current_company_id]['company'] ?? '',
            'product_code' => (string) ($row['product_code'] ?? ''),
            'name' => (string) ($row['product'] ?? ''),
            'status' => (string) ($row['status'] ?? ''),
            'price' => isset($row['price']) ? (float) $row['price'] : null,
            'short_description' => trim(html_entity_decode(
                strip_tags((string) ($row['short_description'] ?? '')),
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            )),
            'full_description' => trim(html_entity_decode(
                strip_tags((string) ($row['full_description'] ?? '')),
                ENT_QUOTES | ENT_HTML5,
                'UTF-8'
            )),
            'variation_group_id' => $variation_groups[$current_product_id] ?? null,
            'features' => $feature_values[$current_product_id] ?? [],
            'schedule' => $schedules[$current_product_id] ?? null,
            'public_url' => fn_url(
                'products.view?product_id=' . $current_product_id,
                'C',
                'https'
            ),
        ];
    }

    fn_talario_analytics_json_response(200, [
        'fetched_at' => time(),
        'company_id' => $company_id ?: null,
        'product_id' => $product_id ?: null,
        'total' => count($products),
        'products' => $products,
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    fn_talario_analytics_json_response(405, ['error' => 'method_not_allowed']);
}

if (!in_array($mode, ['orders', 'partner_companies', 'partner_snapshot'], true)) {
    fn_talario_analytics_json_response(404, ['error' => 'not_found']);
}

$rate_count = fn_talario_analytics_rate_limit();

if (in_array($mode, ['partner_companies', 'partner_snapshot'], true)) {
    fn_talario_analytics_authorize_partner_sync($rate_count);

    if ($mode === 'partner_companies') {
        fn_talario_analytics_partner_companies();
        exit;
    }

    fn_talario_analytics_partner_snapshot();
    exit;
}

fn_talario_analytics_authorize_orders($rate_count);

$date1_raw = isset($_REQUEST['date1']) ? (string) $_REQUEST['date1'] : '';
$date2_raw = isset($_REQUEST['date2']) ? (string) $_REQUEST['date2'] : '';
$date1 = fn_talario_analytics_parse_date($date1_raw);
$date2 = fn_talario_analytics_parse_date($date2_raw);

if (!$date1 || !$date2 || $date2 < $date1) {
    fn_talario_analytics_json_response(400, ['error' => 'invalid_date_range']);
}

$days = (int) $date1->diff($date2)->format('%a') + 1;
if ($days > 31) {
    fn_talario_analytics_json_response(400, ['error' => 'date_range_too_large', 'max_days' => 31]);
}

$page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;
$limit = isset($_REQUEST['limit']) ? (int) $_REQUEST['limit'] : 100;

if ($page < 1 || $limit < 1 || $limit > 100) {
    fn_talario_analytics_json_response(400, ['error' => 'invalid_pagination', 'max_limit' => 100]);
}

$timezone = new DateTimeZone('Europe/Moscow');
$time_from = $date1->setTimezone($timezone)->setTime(0, 0, 0)->getTimestamp();
$time_to = $date2->setTimezone($timezone)->setTime(23, 59, 59)->getTimestamp();
$offset = ($page - 1) * $limit;

$total = (int) db_get_field(
    'SELECT COUNT(*) FROM ?:orders WHERE timestamp >= ?i AND timestamp <= ?i',
    $time_from,
    $time_to
);

$rows = db_get_array(
    'SELECT order_id, parent_order_id, is_parent_order, status, total, timestamp, company_id'
    . ' FROM ?:orders'
    . ' WHERE timestamp >= ?i AND timestamp <= ?i'
    . ' ORDER BY timestamp ASC, order_id ASC'
    . ' LIMIT ?i, ?i',
    $time_from,
    $time_to,
    $offset,
    $limit
);

$order_ids = array_map(static function ($row) {
    return (int) $row['order_id'];
}, $rows);

$resource_booking_counts = [];
$legacy_booking_counts = [];

if ($order_ids) {
    foreach (db_get_array(
        'SELECT order_id, COUNT(*) AS booking_count FROM ?:talario_resource_bookings'
        . ' WHERE order_id IN (?n) GROUP BY order_id',
        $order_ids
    ) as $booking_row) {
        $resource_booking_counts[(int) $booking_row['order_id']] = (int) $booking_row['booking_count'];
    }

    foreach (db_get_array(
        'SELECT order_id, COUNT(*) AS booking_count FROM ?:ec_table_booking_system_booking_info'
        . ' WHERE order_id IN (?n) GROUP BY order_id',
        $order_ids
    ) as $booking_row) {
        $legacy_booking_counts[(int) $booking_row['order_id']] = (int) $booking_row['booking_count'];
    }
}

$orders = [];
foreach ($rows as $row) {
    $total_value = (float) $row['total'];
    $parent_order_id = $row['parent_order_id'] === null ? null : (int) $row['parent_order_id'];

    $order_id = (int) $row['order_id'];
    $resource_count = (int) ($resource_booking_counts[$order_id] ?? 0);
    $legacy_count = (int) ($legacy_booking_counts[$order_id] ?? 0);
    $is_free = $total_value <= 0.0;

    $orders[] = [
        'order_id' => $order_id,
        'parent_order_id' => $parent_order_id,
        'is_parent_order' => (string) $row['is_parent_order'] === 'Y',
        'status' => (string) $row['status'],
        'total' => $total_value,
        'timestamp' => (int) $row['timestamp'],
        'company_id' => (int) $row['company_id'],
        'is_free' => $is_free,
        'expected_metrika_event' => $is_free ? 'talario_free_booking' : 'ecommerce_purchase',
        'resource_booking_count' => $resource_count,
        'legacy_booking_count' => $legacy_count,
        'booking_mismatch' => $resource_count !== $legacy_count,
    ];
}

fn_log_event('general', 'runtime', [
    'message' => 'Talario Analytics API authorized request completed',
]);

fn_talario_analytics_json_response(200, [
    'date1' => $date1_raw,
    'date2' => $date2_raw,
    'timezone' => 'Europe/Moscow',
    'page' => $page,
    'limit' => $limit,
    'total' => $total,
    'has_more' => ($offset + count($orders)) < $total,
    'orders' => $orders,
]);
