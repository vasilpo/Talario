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


/**
 * Returns a bounded, PII-free catalog snapshot for Partner Sync.
 *
 * The snapshot deliberately reads products, variations, prices and the
 * existing Talario schedule-resource tables in one request. It never reads
 * orders, users or payment data and never writes application data.
 */
function fn_talario_analytics_catalog_response(): void
{
    $timezone = new DateTimeZone('Europe/Moscow');
    $from_raw = isset($_GET['from']) ? (string) $_GET['from'] : (new DateTimeImmutable('now', $timezone))->format('Y-m-d');
    $to_raw = isset($_GET['to']) ? (string) $_GET['to'] : (new DateTimeImmutable($from_raw, $timezone))->modify('+30 days')->format('Y-m-d');
    $from = fn_talario_analytics_parse_date($from_raw);
    $to = fn_talario_analytics_parse_date($to_raw);

    if (!$from || !$to || $to < $from) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_date_range']);
    }

    $days = (int) $from->diff($to)->format('%a') + 1;
    if ($days > 62) {
        fn_talario_analytics_json_response(400, ['error' => 'date_range_too_large', 'max_days' => 62]);
    }

    $lang_code = (string) Registry::get('settings.Appearance.default_language');
    if ($lang_code === '') {
        $lang_code = 'ru';
    }

    $partners = [];
    foreach (db_get_array(
        'SELECT company_id, company, status FROM ?:companies WHERE status = ?s ORDER BY company_id ASC',
        'A'
    ) as $row) {
        $partners[] = [
            'partner_id' => (int) $row['company_id'],
            'name' => (string) $row['company'],
            'status' => (string) $row['status'],
        ];
    }

    $products = [];
    foreach (db_get_array(
        'SELECT p.product_id, p.company_id, p.product_type, p.parent_product_id,'
        . ' p.price, p.status, p.updated_timestamp, pd.product'
        . ' FROM ?:products p'
        . ' INNER JOIN ?:product_descriptions pd ON pd.product_id = p.product_id AND pd.lang_code = ?s'
        . ' WHERE p.status = ?s'
        . ' ORDER BY p.product_id ASC',
        $lang_code,
        'A'
    ) as $row) {
        $product_id = (int) $row['product_id'];
        $products[$product_id] = [
            'product_id' => $product_id,
            'partner_id' => (int) $row['company_id'],
            'name' => (string) $row['product'],
            'status' => (string) $row['status'],
            'product_type' => (string) $row['product_type'],
            'parent_product_id' => (int) $row['parent_product_id'],
            'price' => (float) $row['price'],
            'public_url' => (string) fn_url('products.view&product_id=' . $product_id, 'C'),
            'updated_at' => (int) $row['updated_timestamp'],
            'variations' => [],
            'prices' => [],
            'resource_ids' => [],
        ];
    }

    $prices = db_get_array(
        'SELECT product_id, lower_limit, usergroup_id, price'
        . ' FROM ?:product_prices WHERE product_id > 0 ORDER BY product_id ASC, lower_limit ASC'
    );
    foreach ($prices as $row) {
        $product_id = (int) $row['product_id'];
        if (!isset($products[$product_id])) {
            continue;
        }
        $products[$product_id]['prices'][] = [
            'lower_limit' => (int) $row['lower_limit'],
            'usergroup_id' => (int) $row['usergroup_id'],
            'price' => (float) $row['price'],
        ];
    }

    $variations = db_get_array(
        'SELECT vgp.group_id, vgp.product_id, vgp.parent_product_id,'
        . ' p.price, p.status, pd.product'
        . ' FROM ?:product_variation_group_products vgp'
        . ' INNER JOIN ?:products p ON p.product_id = vgp.product_id'
        . ' INNER JOIN ?:product_descriptions pd ON pd.product_id = p.product_id AND pd.lang_code = ?s'
        . ' WHERE p.status = ?s ORDER BY vgp.group_id ASC, vgp.product_id ASC',
        $lang_code,
        'A'
    );
    foreach ($variations as $row) {
        $product_id = (int) $row['product_id'];
        $variation = [
            'variation_id' => $product_id,
            'group_id' => (int) $row['group_id'],
            'product_id' => $product_id,
            'parent_product_id' => (int) $row['parent_product_id'],
            'name' => (string) $row['product'],
            'price' => (float) $row['price'],
            'status' => (string) $row['status'],
        ];
        if (isset($products[$product_id])) {
            $products[$product_id]['variations'][] = $variation;
        }
    }

    foreach (db_get_array(
        'SELECT rp.product_id, rp.resource_id'
        . ' FROM ?:talario_resource_products rp'
        . ' INNER JOIN ?:talario_resources r ON r.resource_id = rp.resource_id'
        . ' WHERE r.status = ?s ORDER BY rp.product_id ASC, rp.resource_id ASC',
        'A'
    ) as $row) {
        $product_id = (int) $row['product_id'];
        if (isset($products[$product_id])) {
            $products[$product_id]['resource_ids'][] = (int) $row['resource_id'];
        }
    }

    $from_sql = $from->format('Y-m-d') . ' 00:00:00';
    $to_sql = $to->format('Y-m-d') . ' 23:59:59';
    $schedule = [];
    foreach (db_get_array(
        'SELECT o.occurrence_id, o.resource_id, o.location_id, o.starts_at, o.ends_at,'
        . ' o.capacity, o.status, r.name AS resource_name, l.name AS location_name,'
        . ' l.address AS location_address'
        . ' FROM ?:talario_resource_occurrences o'
        . ' INNER JOIN ?:talario_resources r ON r.resource_id = o.resource_id AND r.status = ?s'
        . ' INNER JOIN ?:talario_locations l ON l.location_id = o.location_id AND l.status = ?s'
        . ' WHERE o.status = ?s AND o.starts_at >= ?s AND o.starts_at <= ?s'
        . ' ORDER BY o.starts_at ASC, o.occurrence_id ASC',
        'A',
        'A',
        'A',
        $from_sql,
        $to_sql
    ) as $row) {
        $occurrence_id = (int) $row['occurrence_id'];
        $booked = (int) db_get_field(
            'SELECT COALESCE(SUM(quantity), 0) FROM ?:talario_resource_bookings'
            . ' WHERE occurrence_id = ?i AND status = ?s',
            $occurrence_id,
            'A'
        );
        $held = (int) db_get_field(
            'SELECT COALESCE(SUM(quantity), 0) FROM ?:talario_resource_holds'
            . ' WHERE occurrence_id = ?i AND status = ?s AND expires_at > ?i',
            $occurrence_id,
            'A',
            TIME
        );
        $product_ids = array_map('intval', db_get_fields(
            'SELECT product_id FROM ?:talario_resource_products WHERE resource_id = ?i ORDER BY product_id ASC',
            (int) $row['resource_id']
        ));
        $schedule[] = [
            'occurrence_id' => $occurrence_id,
            'resource_id' => (int) $row['resource_id'],
            'resource_name' => (string) $row['resource_name'],
            'product_ids' => $product_ids,
            'location_id' => (int) $row['location_id'],
            'location_name' => (string) $row['location_name'],
            'location_address' => (string) $row['location_address'],
            'starts_at' => (string) $row['starts_at'],
            'ends_at' => (string) $row['ends_at'],
            'capacity' => (int) $row['capacity'],
            'booked' => $booked,
            'held' => $held,
            'available' => max(0, (int) $row['capacity'] - $booked - $held),
        ];
    }

    fn_talario_analytics_json_response(200, [
        'schema_version' => 'partner-sync.catalog.v1',
        'generated_at' => (new DateTimeImmutable('now', $timezone))->format(DateTimeInterface::ATOM),
        'timezone' => 'Europe/Moscow',
        'range' => ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')],
        'partners' => $partners,
        'products' => array_values($products),
        'schedule' => $schedule,
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    fn_talario_analytics_json_response(405, ['error' => 'method_not_allowed']);
}

if (!in_array($mode, ['orders', 'catalog'], true)) {
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

if ($mode === 'catalog') {
    fn_talario_analytics_catalog_response();
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
