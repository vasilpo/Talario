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
    if (!preg_match('/^Bearer\s+(.+)$/i', trim((string) $header), $matches)) {
        return '';
    }

    return trim((string) $matches[1]);
}

function fn_talario_partner_sync_write_authenticate(): void
{
    $is_development = function_exists('fn_is_development') && fn_is_development();
    $dev_copy_enabled = $is_development
        && defined('TALARIO_PARTNER_SYNC_DEV_COPY')
        && TALARIO_PARTNER_SYNC_DEV_COPY === true;

    if (!$dev_copy_enabled) {
        fn_talario_partner_sync_write_json(404, ['error' => 'not_found']);
    }

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
        fn_talario_partner_sync_write_json(401, ['error' => 'unauthorized']);
    }
}

function fn_talario_partner_sync_write_input(): array
{
    $length = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    if ($length > 33554432) {
        fn_talario_partner_sync_write_json(413, ['error' => 'payload_too_large']);
    }

    $raw = (string) file_get_contents('php://input');
    if ($raw === '' || strlen($raw) > 33554432) {
        fn_talario_partner_sync_write_json(400, ['error' => 'invalid_json']);
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

    $prepared = [];
    $temporary_files = [];
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
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($extensions[$mime])) {
            fn_talario_partner_sync_write_json(400, ['error' => 'unsupported_image_type', 'index' => $index]);
        }

        $path = fn_create_temp_file() . '.' . $extensions[$mime];
        if (fn_put_contents($path, $binary) === false) {
            fn_talario_partner_sync_write_json(500, ['error' => 'image_temp_write_failed']);
        }
        $temporary_files[] = $path;
        $prepared[] = [
            'detailed' => [
                'image_path' => $path,
                'alt' => trim((string) ($image['alt'] ?? '')),
            ],
            'position' => $index,
        ];
    }

    try {
        $entity = new Products([], 'A');
        $image_params = [
            'main_pair' => $prepared[0],
            'image_pairs' => array_slice($prepared, 1),
        ];
        $entity->prepareImages($image_params, $product_id);

        $result_id = (int) fn_update_product(['updated_timestamp' => TIME], $product_id, CART_LANGUAGE);
        if ($result_id !== $product_id) {
            fn_talario_partner_sync_write_json(500, ['error' => 'media_write_failed']);
        }
    } finally {
        foreach ($temporary_files as $path) {
            if (is_file($path)) {
                fn_rm($path);
            }
        }
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
