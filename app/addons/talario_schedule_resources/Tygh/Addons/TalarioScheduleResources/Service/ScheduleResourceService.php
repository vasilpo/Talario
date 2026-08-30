<?php

namespace Tygh\Addons\TalarioScheduleResources\Service;

use DateTimeImmutable;
use InvalidArgumentException;
use RuntimeException;
use Tygh\Addons\TalarioScheduleResources\Repository\LocationRepository;
use Tygh\Addons\TalarioScheduleResources\Repository\OccurrenceRepository;
use Tygh\Addons\TalarioScheduleResources\Repository\ResourceProductRepository;
use Tygh\Addons\TalarioScheduleResources\Repository\ResourceRepository;
use Tygh\Addons\TalarioScheduleResources\Repository\ScheduleRuleRepository;
use Tygh\Registry;

/**
 * Application boundary for the physical schedule model.
 *
 * Controllers must not bypass this class: every public operation resolves the
 * acting company from runtime context and checks ownership before persistence.
 */
class ScheduleResourceService
{
    private $locations;
    private $resources;
    private $rules;
    private $occurrences;
    private $products;

    public function __construct(
        LocationRepository $locations = null,
        ResourceRepository $resources = null,
        ScheduleRuleRepository $rules = null,
        OccurrenceRepository $occurrences = null,
        ResourceProductRepository $products = null
    ) {
        $this->locations = $locations ?: new LocationRepository();
        $this->resources = $resources ?: new ResourceRepository();
        $this->rules = $rules ?: new ScheduleRuleRepository();
        $this->occurrences = $occurrences ?: new OccurrenceRepository();
        $this->products = $products ?: new ResourceProductRepository();
    }

    public function createLocation(array $data, $admin_company_id = null)
    {
        $company_id = $this->actingCompanyId($admin_company_id);
        $now = TIME;
        return $this->locations->create($this->only($data, ['name', 'address', 'address_details', 'latitude', 'longitude', 'status']) + [
            'company_id' => $company_id, 'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    public function getLocation($id, $admin_company_id = null)
    {
        $location = $this->requireLocation($id);
        $this->assertCompany($location['company_id'], $this->actingCompanyId($admin_company_id));
        return $location;
    }

    public function updateLocation($id, array $data, $admin_company_id = null)
    {
        $this->getLocation($id, $admin_company_id);
        $data = $this->only($data, ['name', 'address', 'address_details', 'latitude', 'longitude', 'status']);
        $data['updated_at'] = TIME;
        $this->locations->update($id, $data);
    }

    public function archiveLocation($id, $admin_company_id = null) { $this->updateLocation($id, ['status' => 'D'], $admin_company_id); }
    public function getLocations($admin_company_id = null) { return $this->locations->findByCompany($this->actingCompanyId($admin_company_id)); }
    public function locationBelongsToCompany($id, $company_id) { $location = $this->locations->find($id); return $location && (int) $location['company_id'] === (int) $company_id; }

    public function createResource(array $data, $admin_company_id = null)
    {
        $company_id = $this->actingCompanyId($admin_company_id);
        $now = TIME;
        return $this->resources->create($this->only($data, ['name', 'status']) + ['company_id' => $company_id, 'created_at' => $now, 'updated_at' => $now]);
    }

    public function getResource($id, $admin_company_id = null)
    {
        $resource = $this->requireResource($id);
        $this->assertCompany($resource['company_id'], $this->actingCompanyId($admin_company_id));
        return $resource;
    }

    public function updateResource($id, array $data, $admin_company_id = null)
    {
        $this->getResource($id, $admin_company_id);
        $data = $this->only($data, ['name', 'status']);
        $data['updated_at'] = TIME;
        $this->resources->update($id, $data);
    }

    public function archiveResource($id, $admin_company_id = null) { $this->updateResource($id, ['status' => 'D'], $admin_company_id); }
    public function getResources($admin_company_id = null) { return $this->resources->findByCompany($this->actingCompanyId($admin_company_id)); }

    public function createRule(array $data, $admin_company_id = null)
    {
        $resource = $this->getResource($data['resource_id'], $admin_company_id);
        $this->assertLocationCompany($data['location_id'], $resource['company_id']);
        $this->validateRule($data);
        return $this->rules->create($this->only($data, ['resource_id', 'location_id', 'weekday', 'starts_time', 'duration_minutes', 'capacity', 'valid_from', 'valid_to', 'status']));
    }

    public function updateRule($id, array $data, $admin_company_id = null)
    {
        $rule = $this->requireRule($id);
        $resource = $this->getResource($rule['resource_id'], $admin_company_id);
        if (isset($data['resource_id']) && (int) $data['resource_id'] !== (int) $rule['resource_id']) {
            throw new InvalidArgumentException('A schedule rule cannot be moved to another resource');
        }
        $merged = array_merge($rule, $data);
        $this->assertLocationCompany($merged['location_id'], $resource['company_id']);
        $this->validateRule($merged);
        $this->rules->update($id, $this->only($data, ['location_id', 'weekday', 'starts_time', 'duration_minutes', 'capacity', 'valid_from', 'valid_to', 'status']));
    }

    public function disableRule($id, $admin_company_id = null) { $this->updateRule($id, ['status' => 'D'], $admin_company_id); }
    public function getRules($resource_id, $admin_company_id = null) { $this->getResource($resource_id, $admin_company_id); return $this->rules->findByResource($resource_id); }

    public function addProductResource($product_id, $resource_id, $admin_company_id = null)
    {
        $resource = $this->getResource($resource_id, $admin_company_id);
        $product = $this->products->findProduct($product_id);
        if (!$product) { throw new InvalidArgumentException('Product does not exist'); }
        $this->assertCompany($product['company_id'], $resource['company_id']);
        if (!$this->products->exists($product_id, $resource_id)) { $this->products->add($product_id, $resource_id); }
    }

    public function removeProductResource($product_id, $resource_id, $admin_company_id = null)
    {
        $resource = $this->getResource($resource_id, $admin_company_id);
        $product = $this->products->findProduct($product_id);
        if (!$product) { throw new InvalidArgumentException('Product does not exist'); }
        $this->assertCompany($product['company_id'], $resource['company_id']);
        $this->products->remove($product_id, $resource_id);
    }
    public function getResourcesForProduct($product_id, $admin_company_id = null)
    {
        $product = $this->products->findProduct($product_id);
        if (!$product) { throw new InvalidArgumentException('Product does not exist'); }
        $this->assertCompany($product['company_id'], $this->actingCompanyId($admin_company_id));
        return $this->products->findResourcesByProduct($product_id, $product['company_id']);
    }
    public function getProductsForResource($resource_id, $admin_company_id = null)
    {
        $resource = $this->getResource($resource_id, $admin_company_id);
        return $this->products->findProductsByResource($resource_id, $resource['company_id']);
    }

    public function createOccurrence(array $data, $admin_company_id = null)
    {
        $resource = $this->getResource($data['resource_id'], $admin_company_id);
        $rule = empty($data['rule_id']) ? null : $this->requireRule($data['rule_id']);
        if ($rule && (int) $rule['resource_id'] !== (int) $resource['resource_id']) { throw new InvalidArgumentException('Rule does not belong to resource'); }
        if ($rule) {
            $data['location_id'] = $rule['location_id'];
        } elseif (empty($data['location_id'])) {
            throw new InvalidArgumentException('Location is required for an occurrence without a rule');
        }
        $this->assertLocationCompany($data['location_id'], $resource['company_id']);
        if ((int) $data['capacity'] <= 0) { throw new InvalidArgumentException('Capacity must be positive'); }
        if (new DateTimeImmutable($data['ends_at']) <= new DateTimeImmutable($data['starts_at'])) { throw new InvalidArgumentException('Occurrence end must be after start'); }
        $now = TIME;
        return $this->occurrences->create($this->only($data, ['resource_id', 'rule_id', 'location_id', 'starts_at', 'ends_at', 'capacity', 'status']) + ['created_at' => $now, 'updated_at' => $now]);
    }

    public function getOccurrence($id, $admin_company_id = null) { $item = $this->occurrences->find($id); if (!$item) { throw new InvalidArgumentException('Occurrence does not exist'); } $this->getResource($item['resource_id'], $admin_company_id); return $item; }
    public function getOccurrences($resource_id, $from, $to, $admin_company_id = null) { $this->getResource($resource_id, $admin_company_id); if (new DateTimeImmutable($to) <= new DateTimeImmutable($from)) { throw new InvalidArgumentException('Invalid date range'); } return $this->occurrences->findByResourceAndRange($resource_id, $from, $to); }

    public function syncOccurrencesFromRules($resource_id, $from, $to, $admin_company_id = null)
    {
        $this->getResource($resource_id, $admin_company_id);
        $rules = $this->rules->findByResource($resource_id);
        $range_start = new DateTimeImmutable($from . ' 00:00:00');
        $range_end = new DateTimeImmutable($to . ' 23:59:59');
        $starts = [];
        foreach ($rules as $rule) {
            if (($rule['status'] ?? 'D') !== 'A') {
                continue;
            }
            for ($date = $range_start; $date <= $range_end; $date = $date->modify('+1 day')) {
                if ((int) $date->format('N') !== (int) $rule['weekday']) {
                    continue;
                }
                $date_string = $date->format('Y-m-d');
                if ($date_string < $rule['valid_from'] || $date_string > $rule['valid_to']) {
                    continue;
                }
                $start = new DateTimeImmutable($date_string . ' ' . substr($rule['starts_time'], 0, 5) . ':00');
                $end = $start->modify('+' . (int) $rule['duration_minutes'] . ' minutes');
                $starts[] = $start->format('Y-m-d H:i:s');
                $this->occurrences->upsert([
                    'resource_id' => (int) $resource_id,
                    'rule_id' => (int) $rule['rule_id'],
                    'location_id' => (int) $rule['location_id'],
                    'starts_at' => $start->format('Y-m-d H:i:s'),
                    'ends_at' => $end->format('Y-m-d H:i:s'),
                    'capacity' => (int) $rule['capacity'],
                    'status' => 'A',
                    'created_at' => TIME,
                    'updated_at' => TIME,
                ]);
            }
        }
        $this->occurrences->disableExcept(
            $resource_id,
            $range_start->format('Y-m-d H:i:s'),
            $range_end->modify('+1 second')->format('Y-m-d H:i:s'),
            $starts
        );
    }

    public function reserveProductSlot($product_id, $date, $start_time, $quantity, $cart_id, $cart_item_id, $user_id = 0)
    {
        $quantity = (int) $quantity;
        if ($quantity <= 0) { throw new InvalidArgumentException('Quantity must be positive'); }
        $occurrence_id = (int) db_get_field(
            'SELECT o.occurrence_id FROM ?:talario_resource_occurrences o '
            . 'INNER JOIN ?:talario_resource_products rp ON rp.resource_id = o.resource_id '
            . 'WHERE rp.product_id = ?i AND o.starts_at = ?s AND o.status = ?s',
            $product_id, $date . ' ' . $start_time . ':00', 'A'
        );
        if (!$occurrence_id) { throw new InvalidArgumentException('Выбранное время больше недоступно.'); }
        db_query('START TRANSACTION');
        try {
            $capacity = (int) db_get_field(
                'SELECT capacity FROM ?:talario_resource_occurrences WHERE occurrence_id = ?i FOR UPDATE',
                $occurrence_id
            );
            db_query('UPDATE ?:talario_resource_holds SET status = ?s WHERE status = ?s AND expires_at <= ?i', 'D', 'A', TIME);
            $reserved = (int) db_get_field(
                'SELECT COALESCE(SUM(quantity), 0) FROM ?:talario_resource_holds '
                . 'WHERE occurrence_id = ?i AND status = ?s AND expires_at > ?i '
                . 'AND NOT (cart_id = ?s AND cart_item_id = ?s)',
                $occurrence_id, 'A', TIME, (string) $cart_id, (string) $cart_item_id
            );
            $reserved += (int) db_get_field(
                'SELECT COALESCE(SUM(quantity), 0) FROM ?:talario_resource_bookings WHERE occurrence_id = ?i AND status = ?s',
                $occurrence_id, 'A'
            );
            if ($reserved + $quantity > $capacity) {
                throw new InvalidArgumentException('На это время не осталось нужного количества мест.');
            }
            db_query(
                'DELETE FROM ?:talario_resource_holds WHERE cart_id = ?s AND cart_item_id = ?s',
                (string) $cart_id,
                (string) $cart_item_id
            );
            db_query('INSERT INTO ?:talario_resource_holds ?e', [
                'occurrence_id' => $occurrence_id, 'product_id' => (int) $product_id,
                'cart_id' => (string) $cart_id, 'cart_item_id' => (string) $cart_item_id,
                'user_id' => (int) $user_id, 'quantity' => $quantity, 'status' => 'A',
                'expires_at' => TIME + 15 * 60, 'created_at' => TIME, 'updated_at' => TIME,
            ]);
            db_query('COMMIT');
        } catch (\Throwable $e) {
            db_query('ROLLBACK');
            throw $e;
        }
        $this->syncEcarterOccurrenceAvailability($occurrence_id);
        return $occurrence_id;
    }

    public function productUsesScheduleResource($product_id)
    {
        return (bool) db_get_field('SELECT 1 FROM ?:talario_resource_products WHERE product_id = ?i', $product_id);
    }

    public function releaseCartHold($cart_id, $cart_item_id = null)
    {
        $condition = ['cart_id' => (string) $cart_id, 'status' => 'A'];
        if ($cart_item_id !== null) { $condition['cart_item_id'] = (string) $cart_item_id; }
        $occurrence_ids = db_get_fields('SELECT DISTINCT occurrence_id FROM ?:talario_resource_holds WHERE ?w', $condition);
        db_query('UPDATE ?:talario_resource_holds SET status = ?s, updated_at = ?i WHERE ?w', 'D', TIME, $condition);
        foreach ($occurrence_ids as $occurrence_id) { $this->syncEcarterOccurrenceAvailability((int) $occurrence_id); }
    }

    public function convertCartHoldsToBookings($cart_id, $order_id, array $order_items)
    {
        db_query('START TRANSACTION');
        try {
            foreach ($order_items as $item_id => $item) {
                if (!$this->productUsesScheduleResource((int) $item['product_id'])) { continue; }
                $booking = (array) ($item['extra']['booking_info'] ?? []);
                $date_value = $booking['original_booking_date'] ?? $booking['booking_date'] ?? '';
                $date = is_numeric($date_value) ? date('Y-m-d', (int) $date_value) : date('Y-m-d', strtotime($date_value));
                $slot = explode(' - ', (string) ($booking['booking_slot'] ?? ''));
                $expected_occurrence_id = (int) db_get_field(
                    'SELECT o.occurrence_id FROM ?:talario_resource_occurrences o '
                    . 'INNER JOIN ?:talario_resource_products rp ON rp.resource_id = o.resource_id '
                    . 'WHERE rp.product_id = ?i AND o.starts_at = ?s',
                    $item['product_id'], $date . ' ' . substr($slot[0] ?? '', 0, 5) . ':00'
                );
                $hold = db_get_row(
                    'SELECT * FROM ?:talario_resource_holds WHERE cart_id = ?s AND product_id = ?i '
                    . 'AND occurrence_id = ?i AND status = ?s AND expires_at > ?i ORDER BY hold_id DESC LIMIT 1 FOR UPDATE',
                    (string) $cart_id, (int) $item['product_id'], $expected_occurrence_id, 'A', TIME
                );
                if (!$hold) { throw new InvalidArgumentException('Резерв места истёк до создания бронирования.'); }
                db_replace_into('talario_resource_bookings', [
                    'occurrence_id' => (int) $hold['occurrence_id'], 'product_id' => (int) $item['product_id'],
                    'order_id' => (int) $order_id, 'order_item_id' => (int) $item_id,
                    'quantity' => (int) $hold['quantity'], 'status' => 'A',
                    'created_at' => TIME, 'updated_at' => TIME,
                ]);
                db_query('UPDATE ?:talario_resource_holds SET status = ?s, updated_at = ?i WHERE hold_id = ?i', 'C', TIME, $hold['hold_id']);
            }
            db_query('COMMIT');
        } catch (\Throwable $e) { db_query('ROLLBACK'); throw $e; }
        foreach (array_unique(array_column($order_items, 'product_id')) as $product_id) {
            $occurrence_ids = db_get_fields(
                'SELECT DISTINCT occurrence_id FROM ?:talario_resource_bookings WHERE order_id = ?i AND product_id = ?i',
                $order_id, $product_id
            );
            foreach ($occurrence_ids as $occurrence_id) { $this->syncEcarterOccurrenceAvailability((int) $occurrence_id); }
        }
    }

    public function releaseOrderBookings($order_id)
    {
        $occurrence_ids = db_get_fields('SELECT DISTINCT occurrence_id FROM ?:talario_resource_bookings WHERE order_id = ?i AND status = ?s', $order_id, 'A');
        db_query('UPDATE ?:talario_resource_bookings SET status = ?s, updated_at = ?i WHERE order_id = ?i', 'D', TIME, $order_id);
        foreach ($occurrence_ids as $occurrence_id) { $this->syncEcarterOccurrenceAvailability((int) $occurrence_id); }
    }

    public function restoreOrderBookings($order_id)
    {
        $items = db_get_array('SELECT * FROM ?:talario_resource_bookings WHERE order_id = ?i AND status <> ?s', $order_id, 'A');
        db_query('START TRANSACTION');
        try {
            foreach ($items as $item) {
                $capacity = (int) db_get_field(
                    'SELECT capacity FROM ?:talario_resource_occurrences WHERE occurrence_id = ?i FOR UPDATE',
                    $item['occurrence_id']
                );
                $reserved = (int) db_get_field(
                    'SELECT COALESCE(SUM(quantity), 0) FROM ?:talario_resource_bookings WHERE occurrence_id = ?i AND status = ?s',
                    $item['occurrence_id'], 'A'
                );
                $reserved += (int) db_get_field(
                    'SELECT COALESCE(SUM(quantity), 0) FROM ?:talario_resource_holds WHERE occurrence_id = ?i AND status = ?s AND expires_at > ?i',
                    $item['occurrence_id'], 'A', TIME
                );
                if ($reserved + (int) $item['quantity'] > $capacity) {
                    throw new InvalidArgumentException('Нельзя восстановить заказ: на занятии больше нет свободных мест.');
                }
                db_query('UPDATE ?:talario_resource_bookings SET status = ?s, updated_at = ?i WHERE booking_id = ?i', 'A', TIME, $item['booking_id']);
            }
            db_query('COMMIT');
        } catch (\Throwable $e) { db_query('ROLLBACK'); throw $e; }
        foreach ($items as $item) { $this->syncEcarterOccurrenceAvailability((int) $item['occurrence_id']); }
    }

    public function syncEcarterOccurrenceAvailability($occurrence_id)
    {
        $occurrence = db_get_row('SELECT * FROM ?:talario_resource_occurrences WHERE occurrence_id = ?i', $occurrence_id);
        if (!$occurrence) { return; }
        $holds = (int) db_get_field(
            'SELECT COALESCE(SUM(quantity), 0) FROM ?:talario_resource_holds WHERE occurrence_id = ?i AND status = ?s AND expires_at > ?i',
            $occurrence_id, 'A', TIME
        );
        $bookings = (int) db_get_field(
            'SELECT COALESCE(SUM(quantity), 0) FROM ?:talario_resource_bookings WHERE occurrence_id = ?i AND status = ?s',
            $occurrence_id, 'A'
        );
        $available = max(0, (int) $occurrence['capacity'] - $holds - $bookings);
        $day = strtolower(date('l', strtotime($occurrence['starts_at'])));
        $start = substr($occurrence['starts_at'], 11, 5);
        $end = substr($occurrence['ends_at'], 11, 5);
        $product_ids = db_get_fields('SELECT product_id FROM ?:talario_resource_products WHERE resource_id = ?i', $occurrence['resource_id']);
        foreach ($product_ids as $product_id) {
            $row = db_get_row('SELECT days_data FROM ?:ec_table_booking_system WHERE product_id = ?i', $product_id);
            $days = $row ? (array) unserialize($row['days_data']) : [];
            foreach ((array) ($days[$day]['time_by_amount'] ?? []) as $key => $slot) {
                if (($slot['start_time'] ?? '') === $start && ($slot['end_time'] ?? '') === $end) {
                    $days[$day]['time_by_amount'][$key]['amount'] = $available;
                }
            }
            if ($row) { db_query('UPDATE ?:ec_table_booking_system SET days_data = ?s WHERE product_id = ?i', serialize($days), $product_id); }
        }
    }

    private function actingCompanyId($admin_company_id)
    {
        $runtime_company_id = (int) Registry::get('runtime.company_id');
        if ($runtime_company_id > 0) { return $runtime_company_id; }
        if (defined('AREA') && AREA === 'A' && (int) $admin_company_id > 0) { return (int) $admin_company_id; }
        throw new RuntimeException('Company context is required');
    }

    private function assertCompany($actual, $expected) { if ((int) $actual !== (int) $expected) { throw new RuntimeException('Cross-company operation is forbidden'); } }
    private function assertLocationCompany($id, $company_id) { if (!$this->locationBelongsToCompany($id, $company_id)) { throw new RuntimeException('Location belongs to another company or does not exist'); } }
    private function requireLocation($id) { $item = $this->locations->find($id); if (!$item) { throw new InvalidArgumentException('Location does not exist'); } return $item; }
    private function requireResource($id) { $item = $this->resources->find($id); if (!$item) { throw new InvalidArgumentException('Resource does not exist'); } return $item; }
    private function requireRule($id) { $item = $this->rules->find($id); if (!$item) { throw new InvalidArgumentException('Schedule rule does not exist'); } return $item; }
    private function only(array $data, array $allowed) { return array_intersect_key($data, array_flip($allowed)); }
    private function validateRule(array $data)
    {
        if ((int) $data['weekday'] < 1 || (int) $data['weekday'] > 7) { throw new InvalidArgumentException('Weekday must be between 1 and 7'); }
        if ((int) $data['duration_minutes'] <= 0 || (int) $data['capacity'] <= 0) { throw new InvalidArgumentException('Duration and capacity must be positive'); }
        if (!empty($data['valid_to']) && $data['valid_to'] < $data['valid_from']) { throw new InvalidArgumentException('valid_to cannot precede valid_from'); }
    }
}
