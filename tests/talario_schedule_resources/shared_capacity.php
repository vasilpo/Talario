<?php

define('BOOTSTRAP', true);
define('TIME', 1_757_635_200);

$fixture = [
    'products' => [101 => 10, 102 => 10, 201 => 20],
    'resources' => [7 => 10],
    'mappings' => [101 => 7, 102 => 7, 201 => 7],
    'occurrences' => [
        70 => ['occurrence_id' => 70, 'resource_id' => 7, 'starts_at' => '2026-09-12 09:00:00', 'ends_at' => '2026-09-12 10:00:00', 'capacity' => 5, 'status' => 'A'],
    ],
    'holds' => [
        ['occurrence_id' => 70, 'cart_id' => 'other-cart', 'cart_item_id' => 'other-item', 'quantity' => 1, 'status' => 'A', 'expires_at' => TIME + 600],
    ],
    'bookings' => [
        ['occurrence_id' => 70, 'quantity' => 1, 'status' => 'A'],
    ],
    'queries' => [],
];

function valid_resource_for_product($product_id)
{
    global $fixture;
    $resource_id = $fixture['mappings'][$product_id] ?? 0;
    if (!$resource_id) { return 0; }
    return ($fixture['products'][$product_id] ?? 0) === ($fixture['resources'][$resource_id] ?? -1)
        ? $resource_id
        : 0;
}

function db_get_row($query, ...$arguments)
{
    global $fixture;
    $fixture['queries'][] = $query;
    if (strpos($query, 'SELECT rp.resource_id, p.company_id') !== false
        && strpos($query, 'r.company_id = p.company_id') !== false) {
        $product_id = (int) $arguments[0];
        $resource_id = valid_resource_for_product($product_id);
        return $resource_id ? ['resource_id' => $resource_id, 'company_id' => $fixture['products'][$product_id]] : [];
    }
    throw new RuntimeException('Unexpected db_get_row query: ' . $query);
}

function db_get_fields($query, ...$arguments)
{
    global $fixture;
    $fixture['queries'][] = $query;
    if (strpos($query, 'SELECT rp.product_id') !== false
        && strpos($query, 'p.company_id = ?i') !== false) {
        [$company_id, $resource_id] = array_map('intval', $arguments);
        $result = [];
        foreach ($fixture['mappings'] as $product_id => $mapped_resource_id) {
            if ($mapped_resource_id === $resource_id && $fixture['products'][$product_id] === $company_id) {
                $result[] = (string) $product_id;
            }
        }
        return $result;
    }
    throw new RuntimeException('Unexpected db_get_fields query: ' . $query);
}

function db_get_hash_array($query, $key, ...$arguments)
{
    global $fixture;
    $fixture['queries'][] = $query;
    [$resource_id, $date, $status] = $arguments;
    $result = [];
    foreach ($fixture['occurrences'] as $occurrence) {
        if ((int) $occurrence['resource_id'] === (int) $resource_id
            && substr($occurrence['starts_at'], 0, 10) === $date
            && $occurrence['status'] === $status) {
            $result[$occurrence[$key]] = $occurrence;
        }
    }
    return $result;
}

function db_get_field($query, ...$arguments)
{
    global $fixture;
    $fixture['queries'][] = $query;
    if (strpos($query, 'SELECT rp.resource_id') !== false
        && strpos($query, 'r.company_id = p.company_id') !== false) {
        return valid_resource_for_product((int) $arguments[0]);
    }
    if (strpos($query, 'SELECT o.occurrence_id') !== false) {
        $resource_id = valid_resource_for_product((int) $arguments[0]);
        foreach ($fixture['occurrences'] as $occurrence) {
            if ((int) $occurrence['resource_id'] === $resource_id && $occurrence['starts_at'] === $arguments[1]) {
                return $occurrence['occurrence_id'];
            }
        }
        return 0;
    }
    if (strpos($query, 'SELECT capacity') !== false && strpos($query, 'FOR UPDATE') !== false) {
        return $fixture['occurrences'][(int) $arguments[0]]['capacity'];
    }
    if (strpos($query, 'FROM ?:talario_resource_holds') !== false && strpos($query, 'SUM(quantity)') !== false) {
        $total = 0;
        foreach ($fixture['holds'] as $hold) {
            if ((int) $hold['occurrence_id'] !== (int) $arguments[0] || $hold['status'] !== 'A' || $hold['expires_at'] <= TIME) { continue; }
            if (isset($arguments[3], $arguments[4]) && $hold['cart_id'] === (string) $arguments[3] && $hold['cart_item_id'] === (string) $arguments[4]) { continue; }
            $total += (int) $hold['quantity'];
        }
        return $total;
    }
    if (strpos($query, 'FROM ?:talario_resource_bookings') !== false && strpos($query, 'SUM(quantity)') !== false) {
        $total = 0;
        foreach ($fixture['bookings'] as $booking) {
            if ((int) $booking['occurrence_id'] === (int) $arguments[0] && $booking['status'] === 'A') {
                $total += (int) $booking['quantity'];
            }
        }
        return $total;
    }
    throw new RuntimeException('Unexpected db_get_field query: ' . $query);
}

function db_query($query, ...$arguments)
{
    global $fixture;
    $fixture['queries'][] = $query;
    if (in_array($query, ['START TRANSACTION', 'COMMIT', 'ROLLBACK'], true)) { return true; }
    if (strpos($query, 'UPDATE ?:talario_resource_holds SET status') === 0) { return true; }
    if (strpos($query, 'DELETE FROM ?:talario_resource_holds') === 0) {
        $fixture['holds'] = array_values(array_filter($fixture['holds'], static function ($hold) use ($arguments) {
            return $hold['cart_id'] !== (string) $arguments[0] || $hold['cart_item_id'] !== (string) $arguments[1];
        }));
        return true;
    }
    if (strpos($query, 'INSERT INTO ?:talario_resource_holds') === 0) {
        $fixture['holds'][] = $arguments[0];
        return true;
    }
    throw new RuntimeException('Unexpected db_query: ' . $query);
}

function assert_same($expected, $actual, $message)
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
    }
}

function expect_capacity_rejection(callable $operation)
{
    try {
        $operation();
    } catch (InvalidArgumentException $exception) {
        assert_same('На это время не осталось нужного количества мест.', $exception->getMessage(), 'Unexpected capacity error');
        return;
    }
    throw new RuntimeException('Expected shared-capacity rejection was not thrown');
}

$root = dirname(__DIR__, 2);
$base = $root . '/app/addons/talario_schedule_resources/Tygh/Addons/TalarioScheduleResources';
require_once $base . '/Repository/LocationRepository.php';
require_once $base . '/Repository/ResourceRepository.php';
require_once $base . '/Repository/ScheduleRuleRepository.php';
require_once $base . '/Repository/OccurrenceRepository.php';
require_once $base . '/Repository/ResourceProductRepository.php';
require_once $base . '/Service/ScheduleResourceService.php';
require_once $root . '/app/addons/talario_schedule_resources/func.php';

assert_same([101, 102], fn_talario_schedule_resources_get_shared_product_ids(101), 'Only same-company products may share a resource');
assert_same([201], fn_talario_schedule_resources_get_shared_product_ids(201), 'Invalid cross-company mapping must not be expanded');

foreach ([101, 102] as $product_id) {
    $available = [['09:00', '10:00', 'amount' => 99], ['11:00', '12:00', 'amount' => 99]];
    $unavailable = [];
    fn_talario_schedule_resources_override_single_day_slots($product_id, '2026-09-12', $available, $unavailable);
    assert_same(3, $available[0]['amount'], 'Products on one resource must see the same remaining capacity');
    assert_same(0, $unavailable[0]['amount'], 'A missing dated occurrence must be unavailable');
}

$available = [['09:00', '10:00', 'amount' => 99]];
$unavailable = [];
fn_talario_schedule_resources_override_single_day_slots(201, '2026-09-12', $available, $unavailable);
assert_same(99, $available[0]['amount'], 'Cross-company mapping must not affect slot projection');

$service = new Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService();
$service->reserveProductSlot(101, '2026-09-12', '09:00', 3, 'cart-a', 'item-a');
expect_capacity_rejection(static function () use ($service) {
    $service->reserveProductSlot(102, '2026-09-12', '09:00', 1, 'cart-b', 'item-b');
});
assert_same(true, count(array_filter($fixture['queries'], static function ($query) {
    return strpos($query, 'SELECT capacity') !== false && strpos($query, 'FOR UPDATE') !== false;
})) >= 2, 'Capacity must be locked before reservation');

echo "PASS: shared capacity\n";
