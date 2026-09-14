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

$expected_token = trim((string) Registry::get('addons.talario_analytics.api_token'));
if ($expected_token === '' || strlen($expected_token) < 32) {
    fn_talario_analytics_json_response(503, ['error' => 'analytics_api_not_configured']);
}

$provided_token = fn_talario_analytics_bearer_token();
if ($provided_token === '' || !hash_equals($expected_token, $provided_token)) {
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

$timezone = new DateTimeZone('Europe/Moscow');
$time_from = $date1->setTimezone($timezone)->setTime(0, 0, 0)->getTimestamp();
$time_to = $date2->setTimezone($timezone)->setTime(23, 59, 59)->getTimestamp();

$rows = db_get_array(
    'SELECT order_id, parent_order_id, is_parent_order, status, total, timestamp, company_id'
    . ' FROM ?:orders'
    . ' WHERE timestamp >= ?i AND timestamp <= ?i'
    . ' ORDER BY timestamp ASC, order_id ASC',
    $time_from,
    $time_to
);

$orders = [];
foreach ($rows as $row) {
    $total = (float) $row['total'];
    $orders[] = [
        'order_id' => (int) $row['order_id'],
        'parent_order_id' => (int) $row['parent_order_id'],
        'is_parent_order' => (string) $row['is_parent_order'],
        'status' => (string) $row['status'],
        'total' => $total,
        'timestamp' => (int) $row['timestamp'],
        'company_id' => (int) $row['company_id'],
        'is_free' => $total <= 0.0,
    ];
}

fn_talario_analytics_json_response(200, [
    'date1' => $date1_raw,
    'date2' => $date2_raw,
    'timezone' => 'Europe/Moscow',
    'count' => count($orders),
    'orders' => $orders,
]);
