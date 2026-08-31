<?php

namespace Tygh\Addons\TalarioVendorCabinet\Service;

use DateTimeImmutable;
use InvalidArgumentException;
use RuntimeException;
use Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService;

class BookingBridgeService
{
    private $schedule_resources;

    private $day_names = [
        1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday',
        5 => 'friday', 6 => 'saturday', 7 => 'sunday',
    ];

    public function __construct(ScheduleResourceService $schedule_resources = null)
    {
        $this->schedule_resources = $schedule_resources ?: new ScheduleResourceService();
    }

    public function syncProductSchedule($product_id, $company_id, array $data)
    {
        $product_id = (int) $product_id;
        $company_id = (int) $company_id;
        $this->validateProductSchedule($product_id, $company_id, $data);
        $product = db_get_row('SELECT product_id, company_id FROM ?:products WHERE product_id = ?i', $product_id);
        $location_id = (int) ($data['location_id'] ?? 0);

        // The validity period is an implementation detail. Partners must not be
        // able to accidentally create an expired (or excessively long) calendar.
        $today = new DateTimeImmutable('today');
        $from_date = $today->format('Y-m-d');
        $to_date = $today->modify('+1 year')->format('Y-m-d');

        $duration = (int) ($data['duration_minutes'] ?? 0);
        $slots = $this->normalizeSlots($data, $duration);

        // A legacy class may still share one resource between all variations.
        // Editing a concrete variation separates only that product so its
        // calendar and capacity can differ without changing its siblings.
        $resource_id = $this->ensureIndependentResource($product_id);
        $this->syncRules($resource_id, $location_id, $from_date, $to_date, $duration, $slots);
        $this->schedule_resources->syncOccurrencesFromRules($resource_id, $from_date, $to_date);
        $this->syncEcarter($product_id, $from_date, $to_date, $duration, $slots);

        return ['resource_id' => $resource_id, 'product_id' => $product_id, 'slots' => $slots];
    }

    /**
     * Validates a wizard schedule before any product schedule is changed.
     */
    public function validateScheduleData($company_id, array $data)
    {
        $location_id = (int) ($data['location_id'] ?? 0);
        if (!$location_id || !$this->schedule_resources->locationBelongsToCompany($location_id, (int) $company_id)) {
            throw new InvalidArgumentException('Выберите филиал.');
        }
        $duration = (int) ($data['duration_minutes'] ?? 0);
        if ($duration <= 0 || $duration > 1440) {
            throw new InvalidArgumentException('Укажите корректную продолжительность занятия.');
        }
        if (!$this->normalizeSlots($data, $duration)) {
            throw new InvalidArgumentException('Добавьте хотя бы один день и время занятия.');
        }
    }

    /**
     * Checks every condition that can be known before changing a resource.
     * The wizard calls this for all groups first, avoiding a partly updated
     * schedule when one of the later groups has active bookings.
     */
    public function validateProductSchedule($product_id, $company_id, array $data)
    {
        $product_id = (int) $product_id;
        $company_id = (int) $company_id;
        $product = db_get_row('SELECT product_id, company_id FROM ?:products WHERE product_id = ?i', $product_id);
        if (!$product) {
            throw new InvalidArgumentException('Занятие не найдено.');
        }
        if ((int) $product['company_id'] !== $company_id) {
            throw new RuntimeException('Cross-company operation is forbidden');
        }
        $this->validateScheduleData($company_id, $data);

        $resources = $this->schedule_resources->getResourcesForProduct($product_id);
        foreach ($resources as $resource_id) {
            $resource_products = array_map(
                'intval',
                $this->schedule_resources->getProductsForResource((int) $resource_id)
            );
            if ($resource_products === [$product_id]) {
                return;
            }
        }
        if (!$resources) {
            return;
        }
        $has_active_bookings = (bool) db_get_field(
            'SELECT 1 FROM ?:talario_resource_bookings AS booking '
            . 'INNER JOIN ?:talario_resource_occurrences AS occurrence '
            . 'ON occurrence.occurrence_id = booking.occurrence_id '
            . 'WHERE booking.product_id = ?i AND booking.status = ?s '
            . 'AND occurrence.starts_at >= ?s LIMIT 1',
            $product_id,
            'A',
            date('Y-m-d H:i:s', TIME)
        );
        $has_active_holds = (bool) db_get_field(
            'SELECT 1 FROM ?:talario_resource_holds WHERE product_id = ?i '
            . 'AND status = ?s AND expires_at > ?i LIMIT 1',
            $product_id,
            'A',
            TIME
        );
        if ($has_active_bookings || $has_active_holds) {
            throw new InvalidArgumentException(
                'У этой группы уже есть активные записи. Чтобы изменить её расписание отдельно, обратитесь в Talario.'
            );
        }
    }

    public function syncProductVariants($product_id, $company_id)
    {
        $resources = $this->schedule_resources->getResourcesForProduct((int) $product_id);
        if (!$resources) {
            return;
        }
        $resource_id = (int) reset($resources);
        $product_ids = $this->getVariationGroupProductIds($product_id, $company_id);

        $ecarter = db_get_row('SELECT * FROM ?:ec_table_booking_system WHERE product_id = ?i', $product_id);
        foreach ($product_ids as $current_product_id) {
            $current_resources = $this->schedule_resources->getResourcesForProduct($current_product_id);
            // New variations inherit the class schedule. Variations that were
            // already configured independently must never be reattached.
            if (!$current_resources) {
                $this->schedule_resources->addProductResource($current_product_id, $resource_id);
                if ($ecarter) {
                    $ecarter['product_id'] = (int) $current_product_id;
                    db_query('REPLACE INTO ?:ec_table_booking_system ?e', $ecarter);
                    db_query(
                        'UPDATE ?:products SET tracking = ?s, is_edp = ?s WHERE product_id = ?i',
                        'D',
                        'Y',
                        $current_product_id
                    );
                }
            }
        }
    }

    public function detachProduct($product_id, $company_id)
    {
        $resources = $this->schedule_resources->getResourcesForProduct((int) $product_id);
        foreach ($resources as $resource_id) {
            $this->schedule_resources->removeProductResource((int) $product_id, (int) $resource_id);
        }
        db_query('DELETE FROM ?:ec_table_booking_system WHERE product_id = ?i', $product_id);
    }

    private function getVariationGroupProductIds($product_id, $company_id)
    {
        $group_id = (int) db_get_field(
            'SELECT group_id FROM ?:product_variation_group_products WHERE product_id = ?i',
            $product_id
        );
        if (!$group_id) {
            return [(int) $product_id];
        }
        return array_map('intval', db_get_fields(
            'SELECT p.product_id FROM ?:products p INNER JOIN ?:product_variation_group_products gp '
            . 'ON gp.product_id = p.product_id WHERE gp.group_id = ?i AND p.company_id = ?i',
            $group_id,
            $company_id
        ));
    }

    public function getFormData($product_id, $company_id)
    {
        $product_id = (int) $product_id;
        $company_id = (int) $company_id;
        $product_company_id = (int) db_get_field('SELECT company_id FROM ?:products WHERE product_id = ?i', $product_id);
        if (!$product_company_id || $product_company_id !== $company_id) {
            throw new RuntimeException('Cross-company operation is forbidden');
        }

        $today = new DateTimeImmutable('today');
        $result = [
            'from_date' => $today->format('Y-m-d'),
            'to_date' => $today->modify('+1 year')->format('Y-m-d'),
            'duration_minutes' => 45,
            'location_id' => 0,
            'days' => [],
            'slots' => [],
        ];

        $resources = $this->schedule_resources->getResourcesForProduct($product_id);
        if ($resources) {
            $rules = $this->schedule_resources->getRules((int) $resources[0]);
            foreach ($rules as $rule) {
                if (($rule['status'] ?? 'D') !== 'A') {
                    continue;
                }
                $weekday = (int) $rule['weekday'];
                if (!isset($this->day_names[$weekday])) {
                    continue;
                }
                $result['location_id'] = (int) $rule['location_id'];
                $result['from_date'] = (string) $rule['valid_from'];
                $result['to_date'] = (string) $rule['valid_to'];
                $result['duration_minutes'] = (int) $rule['duration_minutes'];
                $slot = [
                    'weekday' => $weekday,
                    'enabled' => 1,
                    'start_time' => substr((string) $rule['starts_time'], 0, 5),
                    'capacity' => (int) $rule['capacity'],
                ];
                $result['slots'][] = $slot;
                // Kept for the old standalone schedule form during rollout.
                if (!isset($result['days'][$weekday])) {
                    $result['days'][$weekday] = $slot;
                }
            }
        }
        return $result;
    }

    private function ensureIndependentResource($product_id)
    {
        $resources = $this->schedule_resources->getResourcesForProduct($product_id);
        $independent_resource_id = 0;
        foreach ($resources as $resource_id) {
            $resource_products = array_map(
                'intval',
                $this->schedule_resources->getProductsForResource((int) $resource_id)
            );
            if ($resource_products === [(int) $product_id]) {
                $independent_resource_id = (int) $resource_id;
                break;
            }
        }
        if ($independent_resource_id) {
            foreach ($resources as $resource_id) {
                if ((int) $resource_id !== $independent_resource_id) {
                    $this->schedule_resources->removeProductResource($product_id, (int) $resource_id);
                }
            }
            return $independent_resource_id;
        }

        if ($resources) {
            $has_active_bookings = (bool) db_get_field(
                'SELECT 1 FROM ?:talario_resource_bookings AS booking '
                . 'INNER JOIN ?:talario_resource_occurrences AS occurrence '
                . 'ON occurrence.occurrence_id = booking.occurrence_id '
                . 'WHERE booking.product_id = ?i AND booking.status = ?s '
                . 'AND occurrence.starts_at >= ?s LIMIT 1',
                $product_id,
                'A',
                date('Y-m-d H:i:s', TIME)
            );
            $has_active_holds = (bool) db_get_field(
                'SELECT 1 FROM ?:talario_resource_holds WHERE product_id = ?i '
                . 'AND status = ?s AND expires_at > ?i LIMIT 1',
                $product_id,
                'A',
                TIME
            );
            if ($has_active_bookings || $has_active_holds) {
                throw new InvalidArgumentException(
                    'У этого варианта уже есть активные записи. Чтобы изменить его расписание отдельно, обратитесь в Talario.'
                );
            }
        }

        $product_name = (string) db_get_field(
            'SELECT product FROM ?:product_descriptions WHERE product_id = ?i AND lang_code = ?s',
            $product_id,
            DESCR_SL
        );
        if ($product_name === '') {
            $product_name = 'Занятие #' . $product_id;
        }
        $resource_id = (int) $this->schedule_resources->createResource(['name' => $product_name, 'status' => 'A']);
        $this->schedule_resources->addProductResource($product_id, $resource_id);
        foreach ($resources as $shared_resource_id) {
            $this->schedule_resources->removeProductResource($product_id, (int) $shared_resource_id);
        }
        return $resource_id;
    }

    private function syncRules($resource_id, $location_id, $from_date, $to_date, $duration, array $slots)
    {
        $existing = $this->schedule_resources->getRules($resource_id);
        $existing_by_key = [];
        foreach ($existing as $rule) {
            $key = (int) $rule['weekday'] . '|' . substr((string) $rule['starts_time'], 0, 5);
            $existing_by_key[$key] = $rule;
        }

        $wanted = [];
        foreach ($slots as $slot) {
            $weekday = (int) $slot['weekday'];
            $key = $weekday . '|' . $slot['start_time'];
            $wanted[$key] = true;
            $rule_data = [
                'resource_id' => $resource_id,
                'location_id' => $location_id,
                'weekday' => $weekday,
                'starts_time' => $slot['start_time'] . ':00',
                'duration_minutes' => $duration,
                'capacity' => $slot['capacity'],
                'valid_from' => $from_date,
                'valid_to' => $to_date,
                'status' => 'A',
            ];
            if (isset($existing_by_key[$key])) {
                unset($rule_data['resource_id']);
                $this->schedule_resources->updateRule((int) $existing_by_key[$key]['rule_id'], $rule_data);
            } else {
                $this->schedule_resources->createRule($rule_data);
            }
        }
        foreach ($existing_by_key as $key => $rule) {
            if (!isset($wanted[$key]) && ($rule['status'] ?? '') === 'A') {
                $this->schedule_resources->disableRule((int) $rule['rule_id']);
            }
        }
    }

    private function syncEcarter($product_id, $from_date, $to_date, $duration, array $slots)
    {
        $days_data = [];
        $slots_by_day = [];
        foreach ($slots as $slot) {
            $slots_by_day[(int) $slot['weekday']][] = $slot;
        }
        foreach ($this->day_names as $weekday => $day_name) {
            $day_slots = $slots_by_day[$weekday] ?? [];
            $enabled = (bool) $day_slots;
            $start_time = $enabled ? $day_slots[0]['start_time'] : '';
            $end_time = $enabled ? $day_slots[0]['end_time'] : '';
            $days_data[$day_name . '_status'] = $enabled ? 1 : 0;
            $days_data[$day_name . '_timing_start_time'] = $start_time;
            $days_data[$day_name . '_timing_end_time'] = $end_time;
            if ($enabled) {
                $days_data[$day_name]['time_by_amount'] = array_map(static function (array $slot) {
                    return [
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                        'amount' => (int) $slot['capacity'],
                    ];
                }, $day_slots);
            }
        }

        $current = db_get_row(
            'SELECT quantity_selector, show_price_date, blocked_date, minimum_booking_time FROM ?:ec_table_booking_system WHERE product_id = ?i',
            $product_id
        );
        $row = [
            'product_id' => (int) $product_id,
            'booking_type' => 'T',
            'quantity_selector' => (string) ($current['quantity_selector'] ?? ''),
            'show_price_date' => (string) ($current['show_price_date'] ?? ''),
            'blocked_date' => (string) ($current['blocked_date'] ?? ''),
            'from_date' => strtotime($from_date . ' 00:00:00'),
            'to_date' => strtotime($to_date . ' 23:59:59'),
            'slot_time' => (int) $duration,
            'free_time' => 0,
            'days_data' => serialize($days_data),
            'minimum_booking_time' => (string) ($current['minimum_booking_time'] ?? ''),
        ];
        db_query('REPLACE INTO ?:ec_table_booking_system ?e', $row);
        db_query('UPDATE ?:products SET tracking = ?s, is_edp = ?s WHERE product_id = ?i', 'D', 'Y', $product_id);
    }

    private function normalizeDays(array $raw_days, $duration)
    {
        $days = [];
        foreach ($this->day_names as $weekday => $day_name) {
            $raw = isset($raw_days[$weekday]) && is_array($raw_days[$weekday]) ? $raw_days[$weekday] : [];
            if (empty($raw['enabled'])) {
                continue;
            }
            $start_time = $this->normalizeTime($raw['start_time'] ?? '');
            $capacity = (int) ($raw['capacity'] ?? 0);
            if ($capacity <= 0) {
                throw new InvalidArgumentException('Количество мест должно быть больше нуля.');
            }
            $start = DateTimeImmutable::createFromFormat('!H:i', $start_time);
            $end = $start ? $start->modify('+' . (int) $duration . ' minutes') : false;
            if (!$start || !$end || $end->format('Y-m-d') !== $start->format('Y-m-d')) {
                throw new InvalidArgumentException('Укажите корректное время занятия.');
            }
            $days[$weekday] = [
                'start_time' => $start_time,
                'end_time' => $end->format('H:i'),
                'capacity' => $capacity,
            ];
        }
        return $days;
    }

    private function normalizeSlots(array $data, $duration)
    {
        $raw_slots = isset($data['slots']) && is_array($data['slots']) ? $data['slots'] : [];
        if (!$raw_slots && !empty($data['days']) && is_array($data['days'])) {
            foreach ($this->normalizeDays($data['days'], $duration) as $weekday => $day) {
                $raw_slots[] = $day + ['weekday' => $weekday];
            }
        }

        $slots = [];
        $seen = [];
        foreach ($raw_slots as $raw) {
            if (!is_array($raw)) {
                continue;
            }
            $weekday = (int) ($raw['weekday'] ?? 0);
            if (!isset($this->day_names[$weekday])) {
                throw new InvalidArgumentException('Выберите день занятия.');
            }
            $start_time = $this->normalizeTime($raw['start_time'] ?? '');
            $capacity = (int) ($raw['capacity'] ?? 0);
            if ($capacity <= 0) {
                throw new InvalidArgumentException('Количество мест должно быть больше нуля.');
            }
            $start = DateTimeImmutable::createFromFormat('!H:i', $start_time);
            $end = $start ? $start->modify('+' . (int) $duration . ' minutes') : false;
            if (!$start || !$end || $end->format('Y-m-d') !== $start->format('Y-m-d')) {
                throw new InvalidArgumentException('Укажите корректное время занятия.');
            }
            $key = $weekday . '|' . $start_time;
            if (isset($seen[$key])) {
                throw new InvalidArgumentException('Одинаковые день и время добавлены дважды.');
            }
            $seen[$key] = true;
            $slots[] = [
                'weekday' => $weekday,
                'start_time' => $start_time,
                'end_time' => $end->format('H:i'),
                'capacity' => $capacity,
            ];
        }
        usort($slots, static function (array $left, array $right) {
            return [$left['weekday'], $left['start_time']] <=> [$right['weekday'], $right['start_time']];
        });
        return $slots;
    }

    private function normalizeDate($value)
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', trim((string) $value));
        $errors = DateTimeImmutable::getLastErrors();
        if (!$date || ($errors && ($errors['warning_count'] || $errors['error_count']))) {
            throw new InvalidArgumentException('Укажите корректные даты расписания.');
        }
        return $date->format('Y-m-d');
    }

    private function normalizeTime($value)
    {
        $value = trim((string) $value);
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value)) {
            throw new InvalidArgumentException('Укажите время в формате ЧЧ:ММ.');
        }
        return $value;
    }
}
