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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    fn_talario_analytics_json_response(405, ['error' => 'method_not_allowed']);
}

if ($mode !== 'orders') {
    fn_talario_analytics_json_response(404, ['error' => 'not_found']);
}

$rate_count = fn_talario_analytics_rate_limit();

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
