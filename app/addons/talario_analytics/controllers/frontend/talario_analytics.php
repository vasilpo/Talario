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
 * Simple per-IP fixed-window rate limit. Only a SHA-256 hash of the IP is stored.
 *
 * @return int Current request count in the minute bucket.
 */
function fn_talario_analytics_rate_limit(): int
{
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $ip_hash = hash('sha256', $ip);
    $bucket = gmdate('YmdHi');
    $dir = Registry::get('config.dir.cache_misc') . 'talario_analytics_rate/';

    if (!is_dir($dir)) {
        fn_mkdir($dir);
    }

    foreach ((array) glob($dir . '*.cnt') as $old_file) {
        if (is_file($old_file) && filemtime($old_file) < time() - 7200) {
            @unlink($old_file);
        }
    }

    $file = $dir . $bucket . '_' . $ip_hash . '.cnt';
    $handle = @fopen($file, 'c+');
    if (!$handle) {
        // Fail closed if throttling state cannot be maintained.
        fn_talario_analytics_json_response(503, ['error' => 'rate_limit_unavailable']);
    }

    flock($handle, LOCK_EX);
    rewind($handle);
    $current = (int) trim((string) stream_get_contents($handle));

    if ($current >= 60) {
        flock($handle, LOCK_UN);
        fclose($handle);
        fn_log_event('general', 'runtime', [
            'message' => 'Talario Analytics API rate limit exceeded',
        ]);
        fn_talario_analytics_json_response(429, ['error' => 'rate_limit_exceeded']);
    }

    $current++;
    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, (string) $current);
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return $current;
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

fn_talario_analytics_rate_limit();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    fn_talario_analytics_json_response(405, ['error' => 'method_not_allowed']);
}

if ($mode !== 'orders') {
    fn_talario_analytics_json_response(404, ['error' => 'not_found']);
}

$stored_token_hash = trim((string) Registry::get('addons.talario_analytics.api_token'));
if (!preg_match('/^sha256:[a-f0-9]{64}$/', $stored_token_hash)) {
    fn_talario_analytics_json_response(503, ['error' => 'analytics_api_not_configured']);
}

$provided_token = fn_talario_analytics_bearer_token();
$provided_hash = 'sha256:' . hash('sha256', $provided_token);

if (strlen($provided_token) < 32 || !hash_equals($stored_token_hash, $provided_hash)) {
    fn_log_event('general', 'runtime', [
        'message' => 'Talario Analytics API unauthorized request',
    ]);
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

$orders = [];
foreach ($rows as $row) {
    $total_value = (float) $row['total'];
    $parent_order_id = $row['parent_order_id'] === null ? null : (int) $row['parent_order_id'];

    $orders[] = [
        'order_id' => (int) $row['order_id'],
        'parent_order_id' => $parent_order_id,
        'is_parent_order' => (string) $row['is_parent_order'] === 'Y',
        'status' => (string) $row['status'],
        'total' => $total_value,
        'timestamp' => (int) $row['timestamp'],
        'company_id' => (int) $row['company_id'],
        'is_free' => $total_value <= 0.0,
    ];
}

fn_log_event('general', 'runtime', [
    'message' => sprintf(
        'Talario Analytics API success: %s..%s page=%d limit=%d returned=%d total=%d',
        $date1_raw,
        $date2_raw,
        $page,
        $limit,
        count($orders),
        $total
    ),
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
