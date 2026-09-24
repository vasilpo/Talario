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

function fn_talario_analytics_canonical_token_hash(string $value, bool $hash_raw_secret = false): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }

    if (preg_match('/^sha256:([a-f0-9]{64})$/i', $value, $matches)) {
        return 'sha256:' . strtolower($matches[1]);
    }

    if (preg_match('/^[a-f0-9]{64}$/i', $value)) {
        return 'sha256:' . strtolower($value);
    }

    return $hash_raw_secret ? 'sha256:' . hash('sha256', $value) : '';
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
function fn_talario_analytics_catalog_public_url(int $product_id): string
{
    $url = (string) fn_url('products.view&product_id=' . $product_id, 'C', 'https');
    $parts = parse_url($url);
    if (!$parts) {
        return $url;
    }

    // Public catalog URLs intentionally carry no request query parameters.
    $query = [];
    $rebuilt = '';
    if (!empty($parts['scheme']) && !empty($parts['host'])) {
        $rebuilt = $parts['scheme'] . '://' . $parts['host'];
        if (!empty($parts['port'])) {
            $rebuilt .= ':' . $parts['port'];
        }
    }
    $rebuilt .= (string) ($parts['path'] ?? '');
    if ($query) {
        $rebuilt .= '?' . http_build_query($query);
    }
    if (!empty($parts['fragment'])) {
        $rebuilt .= '#' . $parts['fragment'];
    }

    return $rebuilt;
}


function fn_talario_analytics_legacy_schedule(array $product_ids, DateTimeImmutable $from, DateTimeImmutable $to): array
{
    if (!$product_ids) {
        return [];
    }

    $timezone = $from->getTimezone();
    $rows = db_get_array(
        'SELECT e.product_id, e.booking_type, e.from_date, e.to_date, e.days_data,'
        . ' e.slot_time, e.free_time, pd.product'
        . ' FROM ?:ec_table_booking_system e'
        . ' INNER JOIN ?:products p ON p.product_id = e.product_id AND p.status = ?s'
        . ' INNER JOIN ?:product_descriptions pd ON pd.product_id = p.product_id'
        . ' AND pd.lang_code = ?s'
        . ' WHERE e.product_id IN (?n)'
        . ' AND e.booking_type IN (?a)'
        . ' AND NOT EXISTS (SELECT 1 FROM ?:talario_resource_products rp'
        . ' WHERE rp.product_id = e.product_id)'
        . ' ORDER BY e.product_id ASC',
        'A',
        (string) Registry::get('settings.Appearance.default_language') ?: 'ru',
        $product_ids,
        ['T', 'R']
    );

    $day_names = [
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
        7 => 'sunday',
    ];
    $schedule = [];

    foreach ($rows as $row) {
        $serialized_days_data = (string) ($row['days_data'] ?? '');
        if ($serialized_days_data === '' || strlen($serialized_days_data) > 65536) {
            continue;
        }
        $days_data = unserialize($serialized_days_data, ['allowed_classes' => false]);
        if (!is_array($days_data) || count($days_data) > 128) {
            continue;
        }
        $valid_days_data = true;
        foreach ($days_data as $key => $value) {
            if (!is_string($key) || is_array($value) || is_object($value) || is_resource($value)) {
                $valid_days_data = false;
                break;
            }
        }
        if (!$valid_days_data) {
            continue;
        }

        $product_id = (int) $row['product_id'];
        $raw_from = $row['from_date'] ?? '';
        $raw_to = $row['to_date'] ?? '';
        $range_from = is_numeric($raw_from)
            ? (new DateTimeImmutable())->setTimestamp((int) $raw_from)->setTimezone($timezone)->setTime(0, 0, 0)
            : (DateTimeImmutable::createFromFormat('!Y-m-d', substr((string) $raw_from, 0, 10), $timezone) ?: $from);
        $range_to = is_numeric($raw_to)
            ? (new DateTimeImmutable())->setTimestamp((int) $raw_to)->setTimezone($timezone)->setTime(0, 0, 0)
            : (DateTimeImmutable::createFromFormat('!Y-m-d', substr((string) $raw_to, 0, 10), $timezone) ?: $to);
        if ($range_to < $range_from || $range_to < $from || $range_from > $to) {
            continue;
        }

        $cursor = $range_from > $from ? $range_from : $from;
        $end = $range_to < $to ? $range_to : $to;
        for (; $cursor <= $end; $cursor = $cursor->modify('+1 day')) {
            $day_name = $day_names[(int) $cursor->format('N')];
            $status = (string) ($days_data[$day_name . '_status'] ?? '0');
            if (!in_array($status, ['1', 'Y', 'A'], true)) {
                continue;
            }

            $starts = trim((string) ($days_data[$day_name . '_timing_start_time'] ?? ''));
            $ends = trim((string) ($days_data[$day_name . '_timing_end_time'] ?? ''));
            if ($starts === '' || $ends === '') {
                continue;
            }

            $schedule[] = [
                'occurrence_id' => 'legacy:' . $product_id . ':' . $cursor->format('Y-m-d') . ':' . $starts,
                'resource_id' => null,
                'resource_name' => null,
                'product_ids' => [$product_id],
                'location_id' => null,
                'location_name' => null,
                'location_address' => null,
                'starts_at' => $cursor->format('Y-m-d') . ' ' . $starts . ':00',
                'ends_at' => $cursor->format('Y-m-d') . ' ' . $ends . ':00',
                'capacity' => null,
                'booked' => null,
                'held' => null,
                'available' => null,
                'source' => 'legacy_ecarter',
            ];
        }
    }

    return $schedule;
}

function fn_talario_analytics_catalog_response(): void
{
    $partner_id = max(0, (int) ($_GET['partner_id'] ?? 0));
    $after_product_id = max(0, (int) ($_GET['after_product_id'] ?? 0));
    $product_limit = (int) ($_GET['limit'] ?? 250);
    if ($product_limit < 1 || $product_limit > 500) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_limit', 'max_limit' => 500]);
    }

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

    $categories = [];
    foreach (db_get_array(
        'SELECT c.category_id, c.parent_id, cd.category'
        . ' FROM ?:categories c'
        . ' INNER JOIN ?:category_descriptions cd ON cd.category_id = c.category_id'
        . ' AND cd.lang_code = ?s'
        . ' WHERE c.status = ?s'
        . ' ORDER BY c.parent_id ASC, c.position ASC, c.category_id ASC',
        $lang_code,
        'A'
    ) as $row) {
        $categories[] = [
            'category_id' => (int) $row['category_id'],
            'parent_id' => (int) $row['parent_id'],
            'name' => (string) $row['category'],
        ];
    }

    $variation_features = [];
    foreach (db_get_array(
        'SELECT pf.feature_id, pf.purpose, pfd.description'
        . ' FROM ?:product_features pf'
        . ' INNER JOIN ?:product_features_descriptions pfd'
        . ' ON pfd.feature_id = pf.feature_id AND pfd.lang_code = ?s'
        . ' WHERE pf.purpose IN (?a)'
        . ' ORDER BY pf.feature_id ASC',
        $lang_code,
        ['group_catalog_item', 'group_variation_catalog_item']
    ) as $feature) {
        $variants = [];
        foreach (db_get_array(
            'SELECT pfvd.variant'
            . ' FROM ?:product_feature_variants pfv'
            . ' INNER JOIN ?:product_feature_variant_descriptions pfvd'
            . ' ON pfvd.variant_id = pfv.variant_id AND pfvd.lang_code = ?s'
            . ' WHERE pfv.feature_id = ?i'
            . ' ORDER BY pfv.position ASC, pfv.variant_id ASC',
            $lang_code,
            (int) $feature['feature_id']
        ) as $variant) {
            $label = trim((string) $variant['variant']);
            if ($label !== '') {
                $variants[] = $label;
            }
        }
        $variation_features[] = [
            'name' => (string) $feature['description'],
            'purpose' => (string) $feature['purpose'],
            'variants' => array_values(array_unique($variants)),
        ];
    }

    $partners = [];
    foreach (db_get_array(
        'SELECT company_id, company, status FROM ?:companies WHERE status = ?s ORDER BY company_id ASC',
        'A'
    ) as $row) {
        if ($partner_id > 0 && (int) $row['company_id'] !== $partner_id) {
            continue;
        }
        $partners[] = [
            'partner_id' => (int) $row['company_id'],
            'name' => (string) $row['company'],
            'status' => (string) $row['status'],
        ];
    }

    $products = [];
    $product_query = 'SELECT p.product_id, p.company_id, p.product_type, p.parent_product_id,'
        . ' COALESCE(pp.price, 0) AS price, p.status, p.updated_timestamp, pd.product'
        . ' FROM ?:products p'
        . ' INNER JOIN ?:product_descriptions pd ON pd.product_id = p.product_id AND pd.lang_code = ?s'
        . ' LEFT JOIN ?:product_prices pp ON pp.product_id = p.product_id'
        . ' AND pp.lower_limit = 1 AND pp.usergroup_id = 0'
        . ' WHERE p.status = ?s';
    $product_args = [$lang_code, 'A'];
    if ($partner_id > 0) {
        $product_query .= ' AND p.company_id = ?i';
        $product_args[] = $partner_id;
    }
    if ($after_product_id > 0) {
        $product_query .= ' AND p.product_id > ?i';
        $product_args[] = $after_product_id;
    }
    $product_query .= ' ORDER BY p.product_id ASC LIMIT ?i';
    $product_args[] = $product_limit + 1;

    foreach (db_get_array($product_query, ...$product_args) as $row) {
        $product_id = (int) $row['product_id'];
        $products[$product_id] = [
            'product_id' => $product_id,
            'partner_id' => (int) $row['company_id'],
            'name' => (string) $row['product'],
            'status' => (string) $row['status'],
            'product_type' => (string) $row['product_type'],
            'parent_product_id' => (int) $row['parent_product_id'],
            'price' => (float) $row['price'],
            'public_url' => fn_talario_analytics_catalog_public_url($product_id),
            'updated_at' => (int) $row['updated_timestamp'],
            'variations' => [],
            'prices' => [],
            'category_ids' => [],
            'resource_ids' => [],
        ];
    }

    $products_truncated = count($products) > $product_limit;
    if ($products_truncated) {
        $products = array_slice($products, 0, $product_limit, true);
    }

    $selected_product_ids = array_map('intval', array_keys($products));

    $product_categories = $products ? db_get_array(
        'SELECT product_id, category_id'
        . ' FROM ?:products_categories'
        . ' WHERE product_id IN (?n)'
        . ' ORDER BY product_id ASC, position ASC, category_id ASC',
        array_keys($products)
    ) : [];
    foreach ($product_categories as $row) {
        $product_id = (int) $row['product_id'];
        if (!isset($products[$product_id])) {
            continue;
        }
        $products[$product_id]['category_ids'][] = (int) $row['category_id'];
    }

    $prices = $products ? db_get_array(
        'SELECT product_id, lower_limit, usergroup_id, price'
        . ' FROM ?:product_prices WHERE product_id IN (?n)'
        . ' ORDER BY product_id ASC, lower_limit ASC',
        array_keys($products)
    ) : [];
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

    $variations = $products ? db_get_array(
        'SELECT vgp.group_id, vgp.product_id, vgp.parent_product_id,'
        . ' COALESCE(vpp.price, 0) AS price, p.status, pd.product'
        . ' FROM ?:product_variation_group_products vgp'
        . ' INNER JOIN ?:products p ON p.product_id = vgp.product_id'
        . ' INNER JOIN ?:product_descriptions pd ON pd.product_id = p.product_id AND pd.lang_code = ?s'
        . ' LEFT JOIN ?:product_prices vpp ON vpp.product_id = p.product_id'
        . ' AND vpp.lower_limit = 1 AND vpp.usergroup_id = 0'
        . ' WHERE p.status = ?s AND vgp.product_id IN (?n)'
        . ' ORDER BY vgp.group_id ASC, vgp.product_id ASC',
        $lang_code,
        'A',
        array_keys($products)
    ) : [];
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
        $parent_product_id = (int) $row['parent_product_id'];
        $catalog_product_id = $parent_product_id > 0 ? $parent_product_id : $product_id;
        if (isset($products[$catalog_product_id])) {
            $products[$catalog_product_id]['variations'][] = $variation;
        }
    }

    foreach ($selected_product_ids ? db_get_array(
        'SELECT rp.product_id, rp.resource_id'
        . ' FROM ?:talario_resource_products rp'
        . ' INNER JOIN ?:talario_resources r ON r.resource_id = rp.resource_id'
        . ' WHERE r.status = ?s AND rp.product_id IN (?n)'
        . ' ORDER BY rp.product_id ASC, rp.resource_id ASC',
        'A',
        $selected_product_ids
    ) : [] as $row) {
        $product_id = (int) $row['product_id'];
        if (isset($products[$product_id])) {
            $products[$product_id]['resource_ids'][] = (int) $row['resource_id'];
        }
    }

    $from_sql = $from->format('Y-m-d') . ' 00:00:00';
    $to_sql = $to->format('Y-m-d') . ' 23:59:59';
    $schedule = [];
    $resource_occurrences = $selected_product_ids ? db_get_array(
        'SELECT o.occurrence_id, o.resource_id, o.location_id, o.starts_at, o.ends_at,'
        . ' o.capacity, o.status, r.name AS resource_name, l.name AS location_name'
        . ' FROM ?:talario_resource_occurrences o'
        . ' INNER JOIN ?:talario_resources r ON r.resource_id = o.resource_id AND r.status = ?s'
        . ' INNER JOIN ?:talario_locations l ON l.location_id = o.location_id AND l.status = ?s'
        . ' WHERE o.status = ?s AND o.starts_at >= ?s AND o.starts_at <= ?s'
        . ' AND EXISTS (SELECT 1 FROM ?:talario_resource_products rp_scope'
        . ' WHERE rp_scope.resource_id = o.resource_id AND rp_scope.product_id IN (?n))'
        . ' ORDER BY o.starts_at ASC, o.occurrence_id ASC LIMIT 2001',
        'A',
        'A',
        'A',
        $from_sql,
        $to_sql,
        $selected_product_ids
    ) : [];
    foreach ($resource_occurrences as $row) {
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
            'SELECT product_id FROM ?:talario_resource_products'
            . ' WHERE resource_id = ?i AND product_id IN (?n) ORDER BY product_id ASC',
            (int) $row['resource_id'],
            $selected_product_ids
        ));
        $schedule[] = [
            'occurrence_id' => $occurrence_id,
            'resource_id' => (int) $row['resource_id'],
            'resource_name' => (string) $row['resource_name'],
            'product_ids' => $product_ids,
            'location_id' => (int) $row['location_id'],
            'location_name' => (string) $row['location_name'],
            'location_address' => null,
            'starts_at' => (string) $row['starts_at'],
            'ends_at' => (string) $row['ends_at'],
            'capacity' => (int) $row['capacity'],
            'booked' => $booked,
            'held' => $held,
            'available' => max(0, (int) $row['capacity'] - $booked - $held),
        ];
    }

    $resource_product_ids = [];
    foreach ($schedule as $entry) {
        foreach ($entry['product_ids'] as $product_id) {
            $resource_product_ids[$product_id] = true;
        }
    }
    $legacy_product_ids = array_values(array_filter(
        $selected_product_ids,
        static function ($product_id) use ($resource_product_ids) {
            return !isset($resource_product_ids[$product_id]);
        }
    ));
    $legacy_schedule = fn_talario_analytics_legacy_schedule($legacy_product_ids, $from, $to);
    $schedule = array_merge($schedule, $legacy_schedule);
    usort($schedule, static function (array $left, array $right): int {
        return strcmp((string) $left['starts_at'], (string) $right['starts_at']);
    });

    $schedule_truncated = count($schedule) > 2000;
    if ($schedule_truncated) {
        $schedule = array_slice($schedule, 0, 2000);
    }
    $next_schedule_marker = null;
    if ($schedule_truncated && $schedule) {
        $last_schedule = $schedule[count($schedule) - 1];
        $next_schedule_marker = [
            'starts_at' => (string) ($last_schedule['starts_at'] ?? ''),
            'occurrence_id' => (string) ($last_schedule['occurrence_id'] ?? ''),
        ];
    }

    fn_log_event('general', 'runtime', [
        'message' => 'Talario Partner Sync catalog request completed',
        'mode' => 'catalog',
        'partner_id' => $partner_id,
        'partner_count' => count($partners),
        'product_count' => count($products),
        'schedule_count' => count($schedule),
        'source_ip_hash' => hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown')),
    ]);

    fn_talario_analytics_json_response(200, [
        'schema_version' => 'partner-sync.catalog.v1',
        'generated_at' => (new DateTimeImmutable('now', $timezone))->format(DateTimeInterface::ATOM),
        'timezone' => 'Europe/Moscow',
        'range' => ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')],
        'categories' => $categories,
        'variation_features' => $variation_features,
        'partners' => $partners,
        'products' => array_values($products),
        'schedule' => $schedule,
        'truncated' => [
            'products' => $products_truncated,
            'schedule' => $schedule_truncated,
        ],
        'has_more' => $products_truncated || $schedule_truncated,
        'next_product_id' => $products_truncated && $products
            ? (int) array_key_last($products)
            : null,
        'next_schedule_marker' => $next_schedule_marker,
    ]);
}

function fn_talario_analytics_partner_sync_dispatcher_status_response(): void
{
    $is_development = function_exists('fn_is_development') && fn_is_development();
    $enabled = $is_development
        && defined('TALARIO_PARTNER_SYNC_DEV_COPY')
        && TALARIO_PARTNER_SYNC_DEV_COPY === true;
    if (!$enabled) {
        fn_talario_analytics_json_response(404, ['error' => 'not_found']);
    }

    $account_home = dirname(DIR_ROOT, 3);
    $source_path = DIR_ROOT . '/ops/beget/talario-dev-github-dispatcher.sh';
    $target_path = $account_home . '/.local/bin/talario-dev-github-dispatcher';
    $authorized_keys_path = $account_home . '/.ssh/authorized_keys';

    $source = is_file($source_path) && !is_link($source_path)
        ? file_get_contents($source_path)
        : false;
    $target = is_file($target_path) && !is_link($target_path)
        ? file_get_contents($target_path)
        : false;

    $binding_expected = false;
    $binding_v2_present = false;
    if (is_readable($authorized_keys_path) && !is_link($authorized_keys_path)) {
        $authorized_keys = file($authorized_keys_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (is_array($authorized_keys)) {
            $expected_command = 'command="' . $target_path . '"';
            foreach ($authorized_keys as $line) {
                if (!is_string($line) || strlen($line) > 16384) {
                    continue;
                }
                if (strpos($line, 'github-actions-talario-dev-v2') !== false) {
                    $binding_v2_present = true;
                    if (strpos($line, $expected_command) !== false) {
                        $binding_expected = true;
                    }
                }
            }
        }
    }

    $target_mode = is_file($target_path) ? (fileperms($target_path) & 0777) : null;
    $source_ok = is_string($source) && $source !== '';
    $target_ok = is_string($target) && $target !== '';

    fn_talario_analytics_json_response(200, [
        'schema_version' => 'partner-sync.dispatcher-status.v1',
        'source_ok' => $source_ok,
        'target_ok' => $target_ok,
        'target_matches_source' => $source_ok && $target_ok
            ? hash_equals(hash('sha256', $source), hash('sha256', $target))
            : false,
        'target_has_dry_run' => $target_ok
            && strpos($target, '"talario-partner-sync-dry-run"') !== false,
        'target_has_enable_penaty' => $target_ok
            && strpos($target, '"talario-partner-sync-enable-penaty-pilot"') !== false,
        'target_mode_0700' => $target_mode === 0700,
        'authorized_keys_readable' => is_readable($authorized_keys_path) && !is_link($authorized_keys_path),
        'authorized_v2_present' => $binding_v2_present,
        'authorized_v2_expected_command' => $binding_expected,
    ]);
}

function fn_talario_analytics_partner_sync_verify_penaty_signature(
    string $purpose,
    string $raw_body
): void {
    $request_id = trim((string) ($_SERVER['HTTP_X_TALARIO_REQUEST_ID'] ?? ''));
    $timestamp_raw = trim((string) ($_SERVER['HTTP_X_TALARIO_TIMESTAMP'] ?? ''));
    $signature_b64 = trim((string) ($_SERVER['HTTP_X_TALARIO_SIGNATURE'] ?? ''));

    $expected_request_id = 'part-sync-penaty-' . $purpose . '-20260924';
    if (!hash_equals($expected_request_id, $request_id)) {
        fn_talario_analytics_json_response(403, ['error' => 'pilot_request_not_allowed']);
    }
    if (!preg_match('/^[0-9]{10}$/', $timestamp_raw)) {
        fn_talario_analytics_json_response(400, ['error' => 'pilot_timestamp_invalid']);
    }
    if (abs(time() - (int) $timestamp_raw) > 120) {
        fn_talario_analytics_json_response(403, ['error' => 'pilot_request_expired']);
    }
    if ($signature_b64 === '' || strlen($signature_b64) > 8192) {
        fn_talario_analytics_json_response(400, ['error' => 'pilot_signature_invalid']);
    }

    $signature = base64_decode($signature_b64, true);
    if (!is_string($signature)
        || strlen($signature) < 128
        || strlen($signature) > 4096
        || strpos($signature, '-----BEGIN SSH SIGNATURE-----') !== 0
    ) {
        fn_talario_analytics_json_response(400, ['error' => 'pilot_signature_invalid']);
    }

    $ssh_keygen = '/usr/bin/ssh-keygen';
    $ssh_stat = @stat($ssh_keygen);
    if (!is_array($ssh_stat)
        || !is_executable($ssh_keygen)
        || (int) $ssh_stat['uid'] !== 0
        || (($ssh_stat['mode'] & 0022) !== 0)
    ) {
        fn_talario_analytics_json_response(503, ['error' => 'pilot_signature_runtime_unavailable']);
    }

    try {
        $nonce = bin2hex(random_bytes(16));
    } catch (Throwable $exception) {
        fn_talario_analytics_json_response(503, ['error' => 'pilot_signature_random_failed']);
    }

    $tmp_base = rtrim((string) sys_get_temp_dir(), DIRECTORY_SEPARATOR);
    $tmp_dir = $tmp_base . DIRECTORY_SEPARATOR . 'talario-part-sync-' . $nonce;
    if (!@mkdir($tmp_dir, 0700, false)) {
        fn_talario_analytics_json_response(503, ['error' => 'pilot_signature_temp_unavailable']);
    }
    @chmod($tmp_dir, 0700);

    $tmp_stat = @lstat($tmp_dir);
    if (!is_array($tmp_stat)
        || is_link($tmp_dir)
        || realpath($tmp_dir) !== $tmp_dir
        || (($tmp_stat['mode'] & 0777) !== 0700)
        || !is_writable($tmp_dir)
    ) {
        @rmdir($tmp_dir);
        fn_talario_analytics_json_response(503, ['error' => 'pilot_signature_temp_untrusted']);
    }

    $allowed_file = $tmp_dir . DIRECTORY_SEPARATOR . 'allow';
    $signature_file = $tmp_dir . DIRECTORY_SEPARATOR . 'sig';

    $cleanup = static function () use ($allowed_file, $signature_file, $tmp_dir): void {
        if (is_file($allowed_file)) {
            @unlink($allowed_file);
        }
        if (is_file($signature_file)) {
            @unlink($signature_file);
        }
        if (is_dir($tmp_dir) && !is_link($tmp_dir)) {
            @rmdir($tmp_dir);
        }
    };
    register_shutdown_function($cleanup);

    $allowed_handle = @fopen($allowed_file, 'xb');
    $signature_handle = @fopen($signature_file, 'xb');
    if (!is_resource($allowed_handle) || !is_resource($signature_handle)) {
        if (is_resource($allowed_handle)) {
            fclose($allowed_handle);
        }
        if (is_resource($signature_handle)) {
            fclose($signature_handle);
        }
        $cleanup();
        fn_talario_analytics_json_response(503, ['error' => 'pilot_signature_temp_failed']);
    }

    @chmod($allowed_file, 0600);
    @chmod($signature_file, 0600);

    $allowed_signer = 'github-actions-talario ssh-ed25519 '
        . 'AAAAC3NzaC1lZDI1NTE5AAAAIGidfZj2eTRsCFo/USIeuxVhS5N+s//POpGqn0gSgXqK'
        . PHP_EOL;
    $allowed_written = fwrite($allowed_handle, $allowed_signer);
    $signature_written = fwrite($signature_handle, $signature);
    fflush($allowed_handle);
    fflush($signature_handle);
    fclose($allowed_handle);
    fclose($signature_handle);

    $allowed_stat = @stat($allowed_file);
    $signature_stat = @stat($signature_file);
    if ($allowed_written !== strlen($allowed_signer)
        || $signature_written !== strlen($signature)
        || !is_array($allowed_stat)
        || !is_array($signature_stat)
        || (int) $allowed_stat['uid'] !== (int) $tmp_stat['uid']
        || (int) $signature_stat['uid'] !== (int) $tmp_stat['uid']
        || (($allowed_stat['mode'] & 0077) !== 0)
        || (($signature_stat['mode'] & 0077) !== 0)
    ) {
        $cleanup();
        fn_talario_analytics_json_response(503, ['error' => 'pilot_signature_temp_write_failed']);
    }

    $message = "talario-part-sync-penaty\n"
        . $purpose . "\n"
        . $request_id . "\n"
        . $timestamp_raw . "\n"
        . hash('sha256', $raw_body) . "\n";

    $verify = proc_open(
        [
            $ssh_keygen,
            '-Y', 'verify',
            '-f', $allowed_file,
            '-I', 'github-actions-talario',
            '-n', 'talario-part-sync',
            '-s', $signature_file,
        ],
        [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ],
        $pipes,
        DIR_ROOT,
        ['PATH' => '/usr/bin:/bin']
    );
    if (!is_resource($verify)) {
        $cleanup();
        fn_talario_analytics_json_response(503, ['error' => 'pilot_signature_verifier_failed']);
    }

    fwrite($pipes[0], $message);
    fclose($pipes[0]);
    stream_get_contents($pipes[1], 4096);
    stream_get_contents($pipes[2], 4096);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $verify_rc = proc_close($verify);
    $cleanup();

    if ($verify_rc !== 0) {
        fn_talario_analytics_json_response(403, ['error' => 'pilot_signature_rejected']);
    }
}

function fn_talario_analytics_partner_sync_run_penaty_cli(string $raw_body): void
{
    $resolve_trusted_binary = static function (array $candidates): ?string {
        foreach ($candidates as $candidate) {
            $real = realpath($candidate);
            $stat = $real !== false ? @stat($real) : false;
            if ($real !== false
                && is_array($stat)
                && is_file($real)
                && is_executable($real)
                && (int) $stat['uid'] === 0
                && (($stat['mode'] & 0022) === 0)
            ) {
                return $real;
            }
        }
        return null;
    };

    $php = $resolve_trusted_binary(['/usr/local/bin/php8.2', '/usr/bin/php8.2']);
    $timeout = $resolve_trusted_binary(['/usr/bin/timeout', '/bin/timeout']);
    if ($php === null || $timeout === null) {
        fn_talario_analytics_json_response(503, ['error' => 'pilot_cli_runtime_unavailable']);
    }

    $runner_source_path = DIR_ROOT . '/ops/partner-sync-apply.php';
    $runner_source_real = realpath($runner_source_path);
    if ($runner_source_real === false
        || $runner_source_real !== $runner_source_path
        || is_link($runner_source_path)
        || !is_file($runner_source_path)
    ) {
        fn_talario_analytics_json_response(503, ['error' => 'pilot_cli_runner_untrusted']);
    }

    $runner_source = file_get_contents($runner_source_path);
    $expected_runner_blob = 'ecda830d3384a141c32ed00e97a17f28da6b4f87';
    $actual_runner_blob = is_string($runner_source)
        ? sha1('blob ' . strlen($runner_source) . "\0" . $runner_source)
        : '';
    if (!is_string($runner_source)
        || $runner_source === ''
        || !hash_equals($expected_runner_blob, $actual_runner_blob)
    ) {
        fn_talario_analytics_json_response(503, ['error' => 'pilot_cli_runner_not_reviewed']);
    }

    try {
        $nonce = bin2hex(random_bytes(16));
    } catch (Throwable $exception) {
        fn_talario_analytics_json_response(503, ['error' => 'pilot_cli_temp_random_failed']);
    }

    $tmp_base = rtrim((string) sys_get_temp_dir(), DIRECTORY_SEPARATOR);
    $tmp_dir = $tmp_base . DIRECTORY_SEPARATOR . 'talario-part-sync-cli-' . $nonce;
    if (!@mkdir($tmp_dir, 0700, false)) {
        fn_talario_analytics_json_response(503, ['error' => 'pilot_cli_temp_unavailable']);
    }
    @chmod($tmp_dir, 0700);

    $tmp_dir_stat = @lstat($tmp_dir);
    if (!is_array($tmp_dir_stat)
        || is_link($tmp_dir)
        || realpath($tmp_dir) !== $tmp_dir
        || (($tmp_dir_stat['mode'] & 0777) !== 0700)
    ) {
        @rmdir($tmp_dir);
        fn_talario_analytics_json_response(503, ['error' => 'pilot_cli_temp_untrusted']);
    }

    $runner_tmp = $tmp_dir . DIRECTORY_SEPARATOR . 'runner.php';
    $runner_handle = @fopen($runner_tmp, 'xb');
    if (!is_resource($runner_handle)) {
        @rmdir($tmp_dir);
        fn_talario_analytics_json_response(503, ['error' => 'pilot_cli_temp_create_failed']);
    }

    $cleanup = static function () use (&$runner_handle, $runner_tmp, $tmp_dir): void {
        if (is_resource($runner_handle)) {
            @fclose($runner_handle);
        }
        if (is_file($runner_tmp)) {
            @unlink($runner_tmp);
        }
        if (is_dir($tmp_dir) && !is_link($tmp_dir)) {
            @rmdir($tmp_dir);
        }
    };

    $written = fwrite($runner_handle, $runner_source);
    if ($written !== strlen($runner_source) || !fflush($runner_handle)) {
        $cleanup();
        fn_talario_analytics_json_response(503, ['error' => 'pilot_cli_temp_write_failed']);
    }
    if (function_exists('fsync')) {
        @fsync($runner_handle);
    }
    @chmod($runner_tmp, 0600);

    $runner_stat = @fstat($runner_handle);
    if (!is_array($runner_stat)
        || (($runner_stat['mode'] & 0170000) !== 0100000)
        || (($runner_stat['mode'] & 0777) !== 0600)
    ) {
        $cleanup();
        fn_talario_analytics_json_response(503, ['error' => 'pilot_cli_temp_verify_failed']);
    }
    fclose($runner_handle);
    $runner_handle = null;

    $installed = file_get_contents($runner_tmp);
    $installed_blob = is_string($installed)
        ? sha1('blob ' . strlen($installed) . "\0" . $installed)
        : '';
    if (!hash_equals($expected_runner_blob, $installed_blob)) {
        $cleanup();
        fn_talario_analytics_json_response(503, ['error' => 'pilot_cli_temp_integrity_failed']);
    }

    $bootstrap = "define('TALARIO_PARTNER_SYNC_DEV_WRITE', true);"
        . "define('TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS', '39');"
        . 'require ' . var_export($runner_tmp, true) . ';';

    $process = proc_open(
        [
            $timeout,
            '--signal=TERM',
            '--kill-after=5s',
            '60s',
            $php,
            '-r',
            $bootstrap,
        ],
        [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ],
        $pipes,
        DIR_ROOT,
        [
            'HOME' => (string) getenv('HOME'),
            'PATH' => '/usr/bin:/bin',
            'TALARIO_PARTNER_SYNC_ROOT' => DIR_ROOT,
            'TALARIO_PARTNER_SYNC_RUNNER_UID' => (string) ((int) $runner_stat['uid']),
        ]
    );

    if (!is_resource($process)) {
        $cleanup();
        fn_talario_analytics_json_response(503, ['error' => 'pilot_cli_start_failed']);
    }

    fwrite($pipes[0], $raw_body);
    fclose($pipes[0]);

    $stdout = stream_get_contents($pipes[1], 1048576);
    $stderr = stream_get_contents($pipes[2], 65536);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $rc = proc_close($process);
    $cleanup();

    $decoded = is_string($stdout) ? json_decode(trim($stdout), true) : null;
    if (!is_array($decoded)) {
        fn_log_event('general', 'runtime', [
            'message' => 'Talario Partner Sync signed CLI bridge returned invalid response',
            'cli_rc' => $rc,
            'stderr_sha256' => is_string($stderr) ? hash('sha256', $stderr) : null,
        ]);
        fn_talario_analytics_json_response(500, [
            'error' => 'pilot_cli_invalid_response',
            'cli_rc' => $rc,
        ]);
    }

    $status = isset($decoded['http_status']) ? (int) $decoded['http_status'] : ($rc === 0 ? 200 : 500);
    unset($decoded['http_status']);
    if ($status < 100 || $status > 599) {
        $status = 500;
        $decoded = ['error' => 'pilot_cli_invalid_status'];
    }

    fn_talario_analytics_json_response($status, $decoded);
}

function fn_talario_analytics_partner_sync_enable_penaty_request_gate(): void
{
    if (defined('TALARIO_PARTNER_SYNC_DEV_WRITE')
        && TALARIO_PARTNER_SYNC_DEV_WRITE !== true
    ) {
        fn_talario_analytics_json_response(409, ['error' => 'pilot_write_gate_conflict']);
    }
    if (defined('TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS')
        && trim((string) TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS) !== '39'
    ) {
        fn_talario_analytics_json_response(409, ['error' => 'pilot_company_gate_conflict']);
    }

    if (!defined('TALARIO_PARTNER_SYNC_DEV_WRITE')) {
        define('TALARIO_PARTNER_SYNC_DEV_WRITE', true);
    }
    if (!defined('TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS')) {
        define('TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS', '39');
    }
}

function fn_talario_analytics_partner_sync_penaty_bootstrap(): void
{
    $raw = (string) file_get_contents('php://input');
    if ($raw !== '' && trim($raw) !== '{}') {
        fn_talario_analytics_json_response(400, ['error' => 'pilot_bootstrap_payload_invalid']);
    }

    fn_talario_analytics_partner_sync_verify_penaty_signature('bootstrap', $raw);
    fn_talario_analytics_partner_sync_enable_penaty_request_gate();
    fn_talario_analytics_partner_sync_dev_age_variant_bootstrap();
}

function fn_talario_analytics_partner_sync_penaty_apply(): void
{
    $raw = (string) file_get_contents('php://input');
    if ($raw === '' || strlen($raw) > 20971520) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_payload']);
    }

    $payload = json_decode($raw, true);
    if (!is_array($payload) || json_last_error() !== JSON_ERROR_NONE) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_json']);
    }

    $operation = (string) ($payload['operation'] ?? '');
    if (!in_array($operation, ['create', 'update'], true)) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_operation']);
    }
    $product = isset($payload['product']) && is_array($payload['product']) ? $payload['product'] : [];
    if ($operation === 'create' && (int) ($product['company_id'] ?? 0) !== 39) {
        fn_talario_analytics_json_response(403, ['error' => 'pilot_company_not_allowed']);
    }
    if (isset($product['company_id']) && (int) $product['company_id'] !== 39) {
        fn_talario_analytics_json_response(403, ['error' => 'pilot_company_not_allowed']);
    }

    $dry_run = !array_key_exists('dry_run', $payload) || (bool) $payload['dry_run'];
    if (!$dry_run) {
        $approval_id = trim((string) ($payload['approval_id'] ?? ''));
        if (!preg_match('/^part-sync-penaty-[A-Za-z0-9._:-]{6,96}$/', $approval_id)) {
            fn_talario_analytics_json_response(400, ['error' => 'pilot_approval_id_required']);
        }
    }

    fn_talario_analytics_partner_sync_verify_penaty_signature('apply', $raw);
    fn_talario_analytics_partner_sync_run_penaty_cli($raw);
}

function fn_talario_analytics_partner_sync_dev_age_variant_bootstrap(): void
{
    $is_development = function_exists('fn_is_development') && fn_is_development();
    $enabled = $is_development
        && defined('TALARIO_PARTNER_SYNC_DEV_COPY')
        && TALARIO_PARTNER_SYNC_DEV_COPY === true
        && defined('TALARIO_PARTNER_SYNC_DEV_WRITE')
        && TALARIO_PARTNER_SYNC_DEV_WRITE === true;
    if (!$enabled) {
        fn_talario_analytics_json_response(404, ['error' => 'not_found']);
    }

    $lang_code = (string) Registry::get('settings.Appearance.default_language') ?: 'ru';
    $features = db_get_array(
        'SELECT pf.feature_id, pf.feature_type, pfd.description'
        . ' FROM ?:product_features pf'
        . ' INNER JOIN ?:product_features_descriptions pfd'
        . ' ON pfd.feature_id = pf.feature_id AND pfd.lang_code = ?s'
        . ' WHERE pfd.description = ?s'
        . ' AND pf.purpose = ?s',
        $lang_code,
        'Возраст',
        'group_variation_catalog_item'
    );
    if (count($features) !== 1) {
        fn_talario_analytics_json_response(409, ['error' => 'age_feature_ambiguous']);
    }

    $feature_id = (int) $features[0]['feature_id'];
    $feature_type = (string) $features[0]['feature_type'];
    $variant = '2-8 лет';
    $existing = (int) db_get_field(
        'SELECT pfv.variant_id'
        . ' FROM ?:product_feature_variants pfv'
        . ' INNER JOIN ?:product_feature_variant_descriptions pfvd'
        . ' ON pfvd.variant_id = pfv.variant_id AND pfvd.lang_code = ?s'
        . ' WHERE pfv.feature_id = ?i AND pfvd.variant = ?s',
        $lang_code,
        $feature_id,
        $variant
    );
    if ($existing > 0) {
        fn_talario_analytics_json_response(200, [
            'status' => 'already_present',
            'feature' => 'Возраст',
            'variant' => $variant,
        ]);
    }

    $variant_id = fn_update_product_feature_variant(
        $feature_id,
        $feature_type,
        ['variant' => $variant],
        $lang_code
    );
    if (!$variant_id) {
        fn_talario_analytics_json_response(500, ['error' => 'age_variant_create_failed']);
    }

    fn_log_event('general', 'runtime', [
        'message' => 'Talario Partner Sync dev taxonomy bootstrap completed',
        'feature' => 'Возраст',
        'variant' => $variant,
    ]);

    fn_talario_analytics_json_response(201, [
        'status' => 'created',
        'feature' => 'Возраст',
        'variant' => $variant,
    ]);
}

if (in_array($mode, ['catalog_variant_bootstrap', 'penaty_bootstrap', 'penaty_apply'], true)) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        fn_talario_analytics_json_response(405, ['error' => 'method_not_allowed']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    fn_talario_analytics_json_response(405, ['error' => 'method_not_allowed']);
}

if (!in_array($mode, ['orders', 'catalog', 'catalog_variant_bootstrap', 'dispatcher_status', 'penaty_bootstrap', 'penaty_apply', 'crm'], true)) {
    fn_talario_analytics_json_response(404, ['error' => 'not_found']);
}

// Partner Sync catalog is enabled only when an explicit local runtime gate is present.
// Development uses the dev_copy gate. Production read access requires a separate
// production-only constant and a separately approved rollout.
if (in_array($mode, ['catalog', 'catalog_variant_bootstrap', 'dispatcher_status', 'penaty_bootstrap', 'penaty_apply'], true)) {
    $is_development = function_exists('fn_is_development') && fn_is_development();
    $dev_copy_enabled = $is_development
        && defined('TALARIO_PARTNER_SYNC_DEV_COPY')
        && TALARIO_PARTNER_SYNC_DEV_COPY === true;
    $prod_read_enabled = !$is_development
        && defined('TALARIO_PARTNER_SYNC_PROD_READ')
        && TALARIO_PARTNER_SYNC_PROD_READ === true;

    if (in_array($mode, ['catalog_variant_bootstrap', 'dispatcher_status', 'penaty_bootstrap', 'penaty_apply'], true)) {
        if (!$dev_copy_enabled) {
            fn_talario_analytics_json_response(404, ['error' => 'not_found']);
        }
    } elseif (!$dev_copy_enabled && !$prod_read_enabled) {
        fn_talario_analytics_json_response(404, ['error' => 'not_found']);
    }
}

if ($mode === 'crm') {
    $is_development = function_exists('fn_is_development') && fn_is_development();
    $dev_copy_enabled = $is_development
        && defined('TALARIO_CRM_DEV_COPY')
        && TALARIO_CRM_DEV_COPY === true;
    $prod_read_enabled = !$is_development
        && defined('TALARIO_CRM_PROD_READ')
        && TALARIO_CRM_PROD_READ === true;

    if (!$dev_copy_enabled && !$prod_read_enabled) {
        fn_talario_analytics_json_response(404, ['error' => 'not_found']);
    }
}

$rate_count = fn_talario_analytics_rate_limit();

if (in_array($mode, ['catalog', 'catalog_variant_bootstrap', 'dispatcher_status', 'penaty_bootstrap', 'penaty_apply'], true)) {
    $stored_token_hash = fn_talario_analytics_canonical_token_hash(
        defined('TALARIO_PARTNER_SYNC_TOKEN_HASH') ? (string) TALARIO_PARTNER_SYNC_TOKEN_HASH : ''
    );
    $analytics_token_hash = fn_talario_analytics_canonical_token_hash(
        (string) Registry::get('addons.talario_analytics.api_token'),
        true
    );

    if (preg_match('/^sha256:[a-f0-9]{64}$/', $analytics_token_hash)
        && preg_match('/^sha256:[a-f0-9]{64}$/', $stored_token_hash)
        && hash_equals($analytics_token_hash, $stored_token_hash)
    ) {
        fn_log_event('general', 'runtime', [
            'message' => 'Talario Partner Sync API misconfigured: credential matches Analytics API credential',
        ]);
        fn_talario_analytics_json_response(503, ['error' => 'partner_sync_api_misconfigured']);
    }
} elseif ($mode === 'crm') {
    $stored_token_hash = fn_talario_analytics_canonical_token_hash(
        defined('TALARIO_CRM_TOKEN_HASH') ? (string) TALARIO_CRM_TOKEN_HASH : ''
    );
    $analytics_token_hash = fn_talario_analytics_canonical_token_hash(
        (string) Registry::get('addons.talario_analytics.api_token'),
        true
    );
    $partner_token_hash = fn_talario_analytics_canonical_token_hash(
        defined('TALARIO_PARTNER_SYNC_TOKEN_HASH') ? (string) TALARIO_PARTNER_SYNC_TOKEN_HASH : ''
    );

    foreach ([$analytics_token_hash, $partner_token_hash] as $other_token_hash) {
        if (preg_match('/^sha256:[a-f0-9]{64}$/', $other_token_hash)
            && preg_match('/^sha256:[a-f0-9]{64}$/', $stored_token_hash)
            && hash_equals($other_token_hash, $stored_token_hash)
        ) {
            fn_log_event('general', 'runtime', [
                'message' => 'Talario CRM API misconfigured: credential is not isolated',
            ]);
            fn_talario_analytics_json_response(503, ['error' => 'crm_api_misconfigured']);
        }
    }
} else {
    $stored_token_hash = trim((string) Registry::get('addons.talario_analytics.api_token'));
}

if (!preg_match('/^sha256:[a-f0-9]{64}$/', $stored_token_hash)) {
    $error = 'analytics_api_not_configured';
    if (in_array($mode, ['catalog', 'catalog_variant_bootstrap', 'dispatcher_status', 'penaty_bootstrap', 'penaty_apply'], true)) {
        $error = 'partner_sync_api_not_configured';
    } elseif ($mode === 'crm') {
        $error = 'crm_api_not_configured';
    }
    fn_talario_analytics_json_response(503, ['error' => $error]);
}

$provided_token = fn_talario_analytics_bearer_token();
$provided_credential_hash = 'sha256:' . hash('sha256', $provided_token);

if (strlen($provided_token) < 32 || !hash_equals($stored_token_hash, $provided_credential_hash)) {
    if ($rate_count === 1) {
        fn_log_event('general', 'runtime', [
            'message' => 'Talario Analytics API unauthorized request',
        ]);
    }
    fn_talario_analytics_json_response(401, ['error' => 'unauthorized']);
}

if ($mode === 'catalog_variant_bootstrap') {
    fn_talario_analytics_partner_sync_dev_age_variant_bootstrap();
}

if ($mode === 'dispatcher_status') {
    fn_talario_analytics_partner_sync_dispatcher_status_response();
}

if ($mode === 'penaty_bootstrap') {
    fn_talario_analytics_partner_sync_penaty_bootstrap();
}

if ($mode === 'penaty_apply') {
    fn_talario_analytics_partner_sync_penaty_apply();
}

if ($mode === 'catalog') {
    // The selected bearer token was validated with hash_equals above before dispatch.
    fn_talario_analytics_catalog_response();
}

if ($mode === 'crm') {
    fn_talario_analytics_crm_rate_limit($provided_credential_hash);
    fn_talario_analytics_crm_response();
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
