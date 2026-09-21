<?php

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Api\Entities\Products;

function fn_talario_partner_sync_write_json(int $status, array $payload): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function fn_talario_partner_sync_write_bearer(): string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if ($header === '' && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        $header = is_array($headers)
            ? (string) ($headers['Authorization'] ?? $headers['authorization'] ?? '')
            : '';
    }
    if (!preg_match('/^Bearer\s+(.+)$/i', trim((string) $header), $matches)) {
        return '';
    }

    return trim((string) $matches[1]);
}

function fn_talario_partner_sync_write_table_exists(string $table): bool
{
    return (bool) db_get_row("SHOW TABLES LIKE '?:?p'", $table);
}

function fn_talario_partner_sync_write_rate_limit(): void
{
    if (!fn_talario_partner_sync_write_table_exists('talario_analytics_rate_limits')) {
        fn_talario_partner_sync_write_json(503, ['error' => 'write_rate_limit_unavailable']);
    }

    $now = time();
    $bucket = (int) floor($now / 60);
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $ip_hash = hash('sha256', $ip);

    foreach ([
        [hash('sha256', 'partner_sync_write:global'), 30],
        [hash('sha256', 'partner_sync_write:ip:' . $ip_hash), 10],
    ] as [$scope_hash, $limit]) {
        db_query(
            'INSERT INTO ?:talario_analytics_rate_limits'
            . ' (scope_hash, minute_bucket, request_count, updated_at)'
            . ' VALUES (?s, ?i, 1, ?i)'
            . ' ON DUPLICATE KEY UPDATE request_count = request_count + 1, updated_at = ?i',
            $scope_hash,
            $bucket,
            $now,
            $now
        );
        $count = (int) db_get_field(
            'SELECT request_count FROM ?:talario_analytics_rate_limits'
            . ' WHERE scope_hash = ?s AND minute_bucket = ?i',
            $scope_hash,
            $bucket
        );
        if ($count > $limit) {
            fn_log_event('general', 'runtime', [
                'message' => 'Talario Partner Sync dev write rate limit exceeded',
                'source_ip_hash' => $ip_hash,
            ]);
            fn_talario_partner_sync_write_json(429, ['error' => 'rate_limit_exceeded']);
        }
    }
}

function fn_talario_partner_sync_write_authenticate(): void
{
    $is_development = function_exists('fn_is_development') && fn_is_development();
    $request_path = (string) ($_SERVER['SCRIPT_NAME'] ?? $_SERVER['REQUEST_URI'] ?? '');
    $is_dev_copy_request = strpos($request_path, '/dev_copy/') !== false;
    $root_path = str_replace('\\', '/', (string) \Tygh\Registry::get('config.dir.root'));
    $is_dev_copy_root = strpos(rtrim($root_path, '/') . '/', '/dev_copy/') !== false;

    $dev_copy_enabled = $is_development
        && $is_dev_copy_request
        && $is_dev_copy_root
        && defined('TALARIO_PARTNER_SYNC_DEV_COPY')
        && TALARIO_PARTNER_SYNC_DEV_COPY === true
        && defined('TALARIO_PARTNER_SYNC_WRITE_ENABLED')
        && TALARIO_PARTNER_SYNC_WRITE_ENABLED === true;

    if (!$dev_copy_enabled) {
        fn_talario_partner_sync_write_json(404, ['error' => 'not_found']);
    }

    fn_talario_partner_sync_write_rate_limit();

    $stored_hash = defined('TALARIO_PARTNER_SYNC_WRITE_TOKEN_HASH')
        ? trim((string) TALARIO_PARTNER_SYNC_WRITE_TOKEN_HASH)
        : '';

    if (!preg_match('/^sha256:[a-f0-9]{64}$/', $stored_hash)) {
        fn_talario_partner_sync_write_json(503, ['error' => 'partner_sync_write_not_configured']);
    }

    if (defined('TALARIO_PARTNER_SYNC_TOKEN_HASH')
        && hash_equals(trim((string) TALARIO_PARTNER_SYNC_TOKEN_HASH), $stored_hash)
    ) {
        fn_talario_partner_sync_write_json(503, ['error' => 'partner_sync_write_credential_reused']);
    }

    $token = fn_talario_partner_sync_write_bearer();
    $provided_hash = 'sha256:' . hash('sha256', $token);
    if (strlen($token) < 32 || !hash_equals($stored_hash, $provided_hash)) {
        fn_log_event('general', 'runtime', [
            'message' => 'Talario Partner Sync dev write unauthorized',
            'source_ip_hash' => hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown')),
        ]);
        fn_talario_partner_sync_write_json(401, ['error' => 'unauthorized']);
    }

    fn_log_event('general', 'runtime', [
        'message' => 'Talario Partner Sync dev write authenticated',
        'source_ip_hash' => hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown')),
    ]);
}

function fn_talario_partner_sync_write_input(): array
{
    $max_bytes = 33554432;
    $length = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    if ($length > $max_bytes) {
        fn_talario_partner_sync_write_json(413, ['error' => 'payload_too_large']);
    }

    $handle = fopen('php://input', 'rb');
    if ($handle === false) {
        fn_talario_partner_sync_write_json(400, ['error' => 'invalid_json']);
    }

    $raw = stream_get_contents($handle, $max_bytes + 1);
    fclose($handle);

    if ($raw === false || $raw === '') {
        fn_talario_partner_sync_write_json(400, ['error' => 'invalid_json']);
    }
    if (strlen($raw) > $max_bytes) {
        fn_talario_partner_sync_write_json(413, ['error' => 'payload_too_large']);
    }

    $payload = json_decode($raw, true);
    if (!is_array($payload)) {
        fn_talario_partner_sync_write_json(400, ['error' => 'invalid_json']);
    }

    return $payload;
}

function fn_talario_partner_sync_write_assert_product_company(int $product_id, int $company_id): void
{
    $actual_company_id = (int) db_get_field(
        'SELECT company_id FROM ?:products WHERE product_id = ?i',
        $product_id
    );

    if (!$actual_company_id) {
        fn_talario_partner_sync_write_json(404, ['error' => 'product_not_found']);
    }

    if ($actual_company_id !== $company_id) {
        fn_talario_partner_sync_write_json(409, ['error' => 'product_company_mismatch']);
    }
}

function fn_talario_partner_sync_write_product(array $payload): void
{
    $company_id = (int) ($payload['company_id'] ?? 0);
    $product_id = (int) ($payload['product_id'] ?? 0);
    $fields = isset($payload['fields']) && is_array($payload['fields']) ? $payload['fields'] : [];

    if ($company_id <= 0) {
        fn_talario_partner_sync_write_json(400, ['error' => 'company_id_required']);
    }

    if (!(bool) db_get_field('SELECT 1 FROM ?:companies WHERE company_id = ?i', $company_id)) {
        fn_talario_partner_sync_write_json(404, ['error' => 'company_not_found']);
    }

    if ($product_id > 0) {
        fn_talario_partner_sync_write_assert_product_company($product_id, $company_id);
    }

    $allowed = [
        'product',
        'full_description',
        'short_description',
        'search_words',
        'meta_keywords',
        'meta_description',
        'price',
        'status',
        'category_ids',
        'amount',
        'tracking',
    ];
    $product_data = array_intersect_key($fields, array_flip($allowed));

    if (isset($product_data['status']) && !in_array($product_data['status'], ['A', 'H', 'D'], true)) {
        fn_talario_partner_sync_write_json(400, ['error' => 'invalid_status']);
    }

    if (isset($product_data['price']) && (!is_numeric($product_data['price']) || (float) $product_data['price'] < 0)) {
        fn_talario_partner_sync_write_json(400, ['error' => 'invalid_price']);
    }

    if (isset($product_data['category_ids'])) {
        if (!is_array($product_data['category_ids']) || !$product_data['category_ids']) {
            fn_talario_partner_sync_write_json(400, ['error' => 'invalid_category_ids']);
        }
        $category_ids = array_values(array_unique(array_map('intval', $product_data['category_ids'])));
        if (in_array(0, $category_ids, true)) {
            fn_talario_partner_sync_write_json(400, ['error' => 'invalid_category_ids']);
        }
        $existing_category_ids = array_map(
            'intval',
            db_get_fields('SELECT category_id FROM ?:categories WHERE category_id IN (?n)', $category_ids)
        );
        sort($category_ids);
        sort($existing_category_ids);
        if ($category_ids !== $existing_category_ids) {
            fn_talario_partner_sync_write_json(400, ['error' => 'category_not_found']);
        }
        $product_data['category_ids'] = $category_ids;
    }

    if ($product_id === 0) {
        if (trim((string) ($product_data['product'] ?? '')) === '') {
            fn_talario_partner_sync_write_json(400, ['error' => 'product_name_required']);
        }
        if (!isset($product_data['price'])) {
            fn_talario_partner_sync_write_json(400, ['error' => 'price_required']);
        }
        if (empty($product_data['category_ids']) || !is_array($product_data['category_ids'])) {
            fn_talario_partner_sync_write_json(400, ['error' => 'category_ids_required']);
        }
        $product_data['company_id'] = $company_id;
        $product_data['status'] = $product_data['status'] ?? 'H';
    } else {
        unset($product_data['company_id']);
    }

    $result_id = (int) fn_update_product($product_data, $product_id, CART_LANGUAGE);
    if ($result_id <= 0) {
        fn_talario_partner_sync_write_json(500, ['error' => 'product_write_failed']);
    }

    $row = db_get_row(
        'SELECT p.product_id, p.company_id, p.status, pd.product, COALESCE(pp.price, 0) AS price'
        . ' FROM ?:products p'
        . ' INNER JOIN ?:product_descriptions pd ON pd.product_id = p.product_id AND pd.lang_code = ?s'
        . ' LEFT JOIN ?:product_prices pp ON pp.product_id = p.product_id'
        . ' AND pp.lower_limit = 1 AND pp.usergroup_id = 0'
        . ' WHERE p.product_id = ?i',
        CART_LANGUAGE,
        $result_id
    );

    fn_log_event('general', 'runtime', [
        'message' => 'Talario Partner Sync dev product write completed',
        'product_id' => $result_id,
        'company_id' => $company_id,
        'operation' => $product_id > 0 ? 'update' : 'create',
    ]);

    fn_talario_partner_sync_write_json($product_id > 0 ? 200 : 201, [
        'ok' => true,
        'operation' => $product_id > 0 ? 'update' : 'create',
        'product' => $row,
    ]);
}

function fn_talario_partner_sync_write_schedule(array $payload): void
{
    $product_id = (int) ($payload['product_id'] ?? 0);
    $company_id = (int) ($payload['company_id'] ?? 0);
    $schedule = isset($payload['schedule']) && is_array($payload['schedule']) ? $payload['schedule'] : [];

    if ($product_id <= 0 || $company_id <= 0) {
        fn_talario_partner_sync_write_json(400, ['error' => 'product_id_and_company_id_required']);
    }
    fn_talario_partner_sync_write_assert_product_company($product_id, $company_id);

    $from_date = trim((string) ($schedule['from_date'] ?? ''));
    $to_date = trim((string) ($schedule['to_date'] ?? ''));
    if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $from_date)
        || !preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $to_date)
    ) {
        fn_talario_partner_sync_write_json(400, ['error' => 'schedule_dates_must_be_dd_mm_yyyy']);
    }

    $booking_data = [
        'booking_type' => 'T',
        'from_date' => $from_date,
        'to_date' => $to_date,
        'slot_time' => (string) max(0, (int) ($schedule['slot_time'] ?? 0)),
        'free_time' => (string) max(0, (int) ($schedule['free_time'] ?? 0)),
    ];

    $weekdays = [
        'monday', 'tuesday', 'wednesday', 'thursday',
        'friday', 'saturday', 'sunday',
    ];
    $days = isset($schedule['days']) && is_array($schedule['days']) ? $schedule['days'] : [];

    foreach ($weekdays as $day) {
        $item = isset($days[$day]) && is_array($days[$day]) ? $days[$day] : [];
        $enabled = !empty($item['enabled']);
        $start = trim((string) ($item['start'] ?? ''));
        $end = trim((string) ($item['end'] ?? ''));

        if ($enabled && (!preg_match('/^\d{2}:\d{2}$/', $start) || !preg_match('/^\d{2}:\d{2}$/', $end))) {
            fn_talario_partner_sync_write_json(400, ['error' => 'invalid_schedule_time', 'day' => $day]);
        }

        $booking_data[$day . '_status'] = $enabled ? '1' : '0';
        $booking_data[$day . '_timing_start_time'] = $enabled ? $start : '';
        $booking_data[$day . '_timing_end_time'] = $enabled ? $end : '';
    }

    if (!function_exists('Fn_Ec_Table_Booking_System_Update_Booking_data')) {
        fn_talario_partner_sync_write_json(503, ['error' => 'ecarter_booking_not_available']);
    }

    if (!Fn_Ec_Table_Booking_System_Update_Booking_data($booking_data, $product_id)) {
        fn_talario_partner_sync_write_json(500, ['error' => 'schedule_write_failed']);
    }

    $row = db_get_row(
        'SELECT product_id, booking_type, from_date, to_date, slot_time, free_time, days_data'
        . ' FROM ?:ec_table_booking_system WHERE product_id = ?i',
        $product_id
    );
    if ($row) {
        unset($row['days_data']);
    }

    fn_log_event('general', 'runtime', [
        'message' => 'Talario Partner Sync dev schedule write completed',
        'product_id' => $product_id,
        'company_id' => $company_id,
    ]);

    fn_talario_partner_sync_write_json(200, [
        'ok' => true,
        'operation' => 'schedule',
        'schedule' => $row,
    ]);
}

function fn_talario_partner_sync_write_media(array $payload): void
{
    $product_id = (int) ($payload['product_id'] ?? 0);
    $company_id = (int) ($payload['company_id'] ?? 0);
    $images = isset($payload['images']) && is_array($payload['images']) ? $payload['images'] : [];

    if ($product_id <= 0 || $company_id <= 0) {
        fn_talario_partner_sync_write_json(400, ['error' => 'product_id_and_company_id_required']);
    }
    fn_talario_partner_sync_write_assert_product_company($product_id, $company_id);

    if (!$images || count($images) > 8) {
        fn_talario_partner_sync_write_json(400, ['error' => 'images_count_invalid', 'max' => 8]);
    }

    $validated = [];
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    foreach ($images as $index => $image) {
        if (!is_array($image)) {
            fn_talario_partner_sync_write_json(400, ['error' => 'invalid_image', 'index' => $index]);
        }

        $encoded = (string) ($image['data_base64'] ?? '');
        $binary = base64_decode($encoded, true);
        if ($binary === false || strlen($binary) === 0 || strlen($binary) > 4194304) {
            fn_talario_partner_sync_write_json(400, ['error' => 'invalid_image_data', 'index' => $index]);
        }

        $info = @getimagesizefromstring($binary);
        $mime = is_array($info) ? (string) ($info['mime'] ?? '') : '';
        if (!isset($extensions[$mime])) {
            fn_talario_partner_sync_write_json(400, ['error' => 'unsupported_image_type', 'index' => $index]);
        }

        $validated[] = [
            'binary' => $binary,
            'extension' => $extensions[$mime],
            'alt' => trim((string) ($image['alt'] ?? '')),
            'position' => $index,
        ];
    }

    $prepared = [];
    $temporary_files = [];

    $media_error = null;

    try {
        foreach ($validated as $image) {
            $path = fn_create_temp_file() . '.' . $image['extension'];
            if (fn_put_contents($path, $image['binary']) === false) {
                throw new RuntimeException('image_temp_write_failed');
            }

            $temporary_files[] = $path;
            $prepared[] = [
                'detailed' => [
                    'image_path' => $path,
                    'alt' => $image['alt'],
                ],
                'position' => $image['position'],
            ];
        }

        $entity = new Products([], 'A');
        $image_params = [
            'main_pair' => $prepared[0],
            'image_pairs' => array_slice($prepared, 1),
        ];
        $entity->prepareImages($image_params, $product_id);

        $result_id = (int) fn_update_product(['updated_timestamp' => TIME], $product_id, CART_LANGUAGE);
        if ($result_id !== $product_id) {
            throw new RuntimeException('media_write_failed');
        }
    } catch (RuntimeException $exception) {
        $media_error = $exception->getMessage();
    } finally {
        foreach ($temporary_files as $path) {
            if (is_file($path)) {
                fn_rm($path);
            }
        }
    }

    if ($media_error !== null) {
        fn_talario_partner_sync_write_json(500, ['error' => $media_error]);
    }

    $main = fn_get_image_pairs($product_id, 'product', 'M', true, true, CART_LANGUAGE);
    $additional = fn_get_image_pairs($product_id, 'product', 'A', true, true, CART_LANGUAGE);

    fn_log_event('general', 'runtime', [
        'message' => 'Talario Partner Sync dev media write completed',
        'product_id' => $product_id,
        'company_id' => $company_id,
        'image_count' => count($images),
    ]);

    fn_talario_partner_sync_write_json(200, [
        'ok' => true,
        'operation' => 'media',
        'product_id' => $product_id,
        'main_image_present' => !empty($main),
        'additional_image_count' => is_array($additional) ? count($additional) : 0,
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fn_talario_partner_sync_write_json(405, ['error' => 'method_not_allowed']);
}

fn_talario_partner_sync_write_authenticate();
$payload = fn_talario_partner_sync_write_input();
$mode = (string) ($mode ?? '');

if ($mode === 'product') {
    fn_talario_partner_sync_write_product($payload);
}
if ($mode === 'schedule') {
    fn_talario_partner_sync_write_schedule($payload);
}
if ($mode === 'media') {
    fn_talario_partner_sync_write_media($payload);
}

fn_talario_partner_sync_write_json(404, ['error' => 'not_found']);
