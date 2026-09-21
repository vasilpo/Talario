<?php

defined('BOOTSTRAP') or die('Access denied');

/**
 * Development-only Partner Sync write capability.
 *
 * This file deliberately contains no production enablement path. The caller
 * must already be authenticated with the dedicated Partner Sync credential.
 */

function fn_talario_analytics_partner_sync_write_payload(): array
{
    $raw = (string) file_get_contents('php://input');
    if ($raw === '' || strlen($raw) > 20971520) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_payload']);
    }

    $payload = json_decode($raw, true);
    if (!is_array($payload) || json_last_error() !== JSON_ERROR_NONE) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_json']);
    }

    return $payload;
}

function fn_talario_analytics_partner_sync_write_allowed_company_ids(): array
{
    if (!defined('TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS')) {
        return [];
    }

    $raw = trim((string) TALARIO_PARTNER_SYNC_DEV_WRITE_COMPANY_IDS);
    if ($raw === '') {
        return [];
    }

    $ids = array_values(array_unique(array_filter(array_map(
        'intval',
        preg_split('/\s*,\s*/', $raw) ?: []
    ))));
    sort($ids);

    return $ids;
}

function fn_talario_analytics_partner_sync_write_require_company_allowed(int $company_id): void
{
    $allowed = fn_talario_analytics_partner_sync_write_allowed_company_ids();
    if (!$allowed || !in_array($company_id, $allowed, true)) {
        fn_talario_analytics_json_response(403, ['error' => 'company_not_write_allowed']);
    }
}

function fn_talario_analytics_partner_sync_write_normalize_product(array $payload): array
{
    $operation = (string) ($payload['operation'] ?? '');
    if (!in_array($operation, ['create', 'update'], true)) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_operation']);
    }

    $product = isset($payload['product']) && is_array($payload['product'])
        ? $payload['product']
        : [];

    $data = [];

    if (array_key_exists('name', $product)) {
        $name = trim((string) $product['name']);
        if ($name === '' || mb_strlen($name, 'UTF-8') > 255) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_product_name']);
        }
        $data['product'] = $name;
    } elseif ($operation === 'create') {
        fn_talario_analytics_json_response(400, ['error' => 'product_name_required']);
    }

    if (array_key_exists('company_id', $product)) {
        $company_id = (int) $product['company_id'];
        if ($company_id <= 0) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_company_id']);
        }
        $data['company_id'] = $company_id;
    } elseif ($operation === 'create') {
        fn_talario_analytics_json_response(400, ['error' => 'company_id_required']);
    }

    if (array_key_exists('price', $product)) {
        if (!is_numeric($product['price']) || (float) $product['price'] < 0) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_price']);
        }
        $data['price'] = (float) $product['price'];
    } elseif ($operation === 'create') {
        fn_talario_analytics_json_response(400, ['error' => 'price_required']);
    }

    if (array_key_exists('status', $product)) {
        $status = (string) $product['status'];
        if (!in_array($status, ['A', 'H'], true)) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_status']);
        }
        $data['status'] = $status;
    } elseif ($operation === 'create') {
        // New Partner Sync cards are hidden by default until readback/review.
        $data['status'] = 'H';
    }

    if (array_key_exists('category_ids', $product)) {
        if (!is_array($product['category_ids']) || !$product['category_ids']) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_category_ids']);
        }
        $category_ids = array_values(array_unique(array_filter(array_map('intval', $product['category_ids']))));
        if (!$category_ids) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_category_ids']);
        }
        $data['category_ids'] = $category_ids;
    } elseif ($operation === 'create') {
        fn_talario_analytics_json_response(400, ['error' => 'category_ids_required']);
    }

    foreach (['short_description', 'full_description', 'meta_keywords'] as $field) {
        if (!array_key_exists($field, $product)) {
            continue;
        }
        $value = (string) $product[$field];
        $max = $field === 'full_description' ? 50000 : 5000;
        if (mb_strlen($value, 'UTF-8') > $max) {
            fn_talario_analytics_json_response(400, ['error' => 'field_too_long', 'field' => $field]);
        }
        $data[$field] = $value;
    }

    return $data;
}

function fn_talario_analytics_partner_sync_write_normalize_booking(array $booking): array
{
    $from = fn_talario_analytics_parse_date((string) ($booking['from'] ?? ''));
    $to = fn_talario_analytics_parse_date((string) ($booking['to'] ?? ''));
    if (!$from || !$to || $to < $from) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_booking_range']);
    }

    $slot_time = (int) ($booking['slot_time'] ?? 0);
    $free_time = (int) ($booking['free_time'] ?? 0);
    if ($slot_time < 1 || $slot_time > 1440 || $free_time < 0 || $free_time > 1440) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_booking_duration']);
    }

    $days = isset($booking['days']) && is_array($booking['days']) ? $booking['days'] : [];
    $names = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    $data = [
        'booking_type' => 'T',
        'from_date' => $from->format('d/m/Y'),
        'to_date' => $to->format('d/m/Y'),
        'slot_time' => $slot_time,
        'free_time' => $free_time,
        'quantity_selector' => 'Y',
        'show_price_date' => 'N',
        'blocked_date' => '',
    ];

    $enabled_count = 0;
    foreach ($names as $day) {
        $item = isset($days[$day]) && is_array($days[$day]) ? $days[$day] : [];
        $enabled = !empty($item['enabled']);
        $start = trim((string) ($item['start'] ?? ''));
        $end = trim((string) ($item['end'] ?? ''));

        if ($enabled) {
            if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $start)
                || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $end)
                || $start >= $end
            ) {
                fn_talario_analytics_json_response(400, ['error' => 'invalid_booking_time', 'day' => $day]);
            }
            $enabled_count++;
        } else {
            $start = '';
            $end = '';
        }

        $data[$day . '_status'] = $enabled ? '1' : 0;
        $data[$day . '_timing_start_time'] = $start;
        $data[$day . '_timing_end_time'] = $end;
    }

    if ($enabled_count === 0) {
        fn_talario_analytics_json_response(400, ['error' => 'booking_days_required']);
    }

    return $data;
}

function fn_talario_analytics_partner_sync_write_prepare_images(array $images, int $product_id): array
{
    if (count($images) > 12) {
        fn_talario_analytics_json_response(400, ['error' => 'too_many_images', 'max_images' => 12]);
    }

    $temp_files = [];
    $decoded_images = [];
    $total_bytes = 0;
    $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];

    foreach ($images as $index => $image) {
        if (!is_array($image)) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_image', 'index' => $index]);
        }

        $encoded = (string) ($image['content_base64'] ?? '');
        if ($encoded === '' || strlen($encoded) > 7340032) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_image_payload', 'index' => $index]);
        }

        $binary = base64_decode($encoded, true);
        if ($binary === false || strlen($binary) === 0 || strlen($binary) > 5242880) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_image_payload', 'index' => $index]);
        }

        $info = @getimagesizefromstring($binary);
        $mime = is_array($info) ? (string) ($info['mime'] ?? '') : '';
        if (!in_array($mime, $allowed_mimes, true)) {
            fn_talario_analytics_json_response(400, ['error' => 'unsupported_image_type', 'index' => $index]);
        }

        $total_bytes += strlen($binary);
        if ($total_bytes > 16777216) {
            fn_talario_analytics_json_response(400, ['error' => 'images_too_large', 'max_bytes' => 16777216]);
        }

        $decoded_images[] = [
            'binary' => $binary,
            'alt' => mb_substr(trim((string) ($image['alt'] ?? '')), 0, 255, 'UTF-8'),
        ];
    }

    if (!$decoded_images) {
        return [];
    }

    if ($product_id > 0) {
        $main = fn_get_image_pairs($product_id, 'product', 'M', true, true, DEFAULT_LANGUAGE);
        if (!empty($main['pair_id'])) {
            fn_delete_image_pair((int) $main['pair_id']);
        }
        foreach ((array) fn_get_image_pairs($product_id, 'product', 'A', true, true, DEFAULT_LANGUAGE) as $pair) {
            if (!empty($pair['pair_id'])) {
                fn_delete_image_pair((int) $pair['pair_id']);
            }
        }
    }

    $_REQUEST['file_product_main_image_icon'] = [];
    $_REQUEST['type_product_main_image_icon'] = [];
    $_REQUEST['file_product_main_image_detailed'] = [];
    $_REQUEST['type_product_main_image_detailed'] = [];
    $_REQUEST['product_main_image_data'] = [];

    $_REQUEST['file_product_add_additional_image_icon'] = [];
    $_REQUEST['type_product_add_additional_image_icon'] = [];
    $_REQUEST['file_product_add_additional_image_detailed'] = [];
    $_REQUEST['type_product_add_additional_image_detailed'] = [];
    $_REQUEST['product_add_additional_image_data'] = [];

    foreach ($decoded_images as $index => $image) {
        $tmp = fn_create_temp_file();
        fn_put_contents($tmp, $image['binary']);
        @chmod($tmp, 0600);
        $temp_files[] = $tmp;

        if ($index === 0) {
            $_REQUEST['file_product_main_image_detailed'][] = $tmp;
            $_REQUEST['type_product_main_image_detailed'][] = 'server';
            $_REQUEST['product_main_image_data'][] = [
                'pair_id' => 0,
                'type' => 'M',
                'object_id' => 0,
                'image_alt' => '',
                'detailed_alt' => $image['alt'],
            ];
        } else {
            $_REQUEST['file_product_add_additional_image_detailed'][] = $tmp;
            $_REQUEST['type_product_add_additional_image_detailed'][] = 'server';
            $_REQUEST['product_add_additional_image_data'][] = [
                'position' => $index,
                'pair_id' => 0,
                'type' => 'A',
                'object_id' => 0,
                'image_alt' => '',
                'detailed_alt' => $image['alt'],
            ];
        }
    }

    return $temp_files;
}

function fn_talario_analytics_partner_sync_write_cleanup_images(array $temp_files): void
{
    foreach ($temp_files as $file) {
        if (is_string($file) && $file !== '' && file_exists($file)) {
            fn_rm($file);
        }
    }
}

function fn_talario_analytics_partner_sync_write_readback(int $product_id): array
{
    $row = db_get_row(
        'SELECT p.product_id, p.company_id, p.status, pd.product, pd.short_description,'
        . ' pd.full_description, COALESCE(pp.price, 0) AS price'
        . ' FROM ?:products p'
        . ' INNER JOIN ?:product_descriptions pd ON pd.product_id = p.product_id AND pd.lang_code = ?s'
        . ' LEFT JOIN ?:product_prices pp ON pp.product_id = p.product_id'
        . ' AND pp.lower_limit = 1 AND pp.usergroup_id = 0'
        . ' WHERE p.product_id = ?i',
        (string) \Tygh\Registry::get('settings.Appearance.default_language') ?: 'ru',
        $product_id
    );

    if (!$row) {
        return [];
    }

    $booking = db_get_row(
        'SELECT booking_type, from_date, to_date, slot_time, free_time, days_data'
        . ' FROM ?:ec_table_booking_system WHERE product_id = ?i',
        $product_id
    );

    $days_data = [];
    $serialized_days_data = (string) ($booking['days_data'] ?? '');
    if ($serialized_days_data !== ''
        && strlen($serialized_days_data) <= 8192
        && preg_match('/^a:\\d+:\\{/', $serialized_days_data)
    ) {
        $decoded = @unserialize($serialized_days_data, ['allowed_classes' => false, 'max_depth' => 8]);
        if (is_array($decoded) && count($decoded) <= 32) {
            foreach ($decoded as $key => $value) {
                if (is_string($key) && is_scalar($value)) {
                    $days_data[$key] = $value;
                }
            }
        }
    }

    $main_image = fn_get_image_pairs($product_id, 'product', 'M', true, true, DEFAULT_LANGUAGE);
    $additional_images = (array) fn_get_image_pairs($product_id, 'product', 'A', true, true, DEFAULT_LANGUAGE);

    return [
        'product_id' => (int) $row['product_id'],
        'company_id' => (int) $row['company_id'],
        'name' => (string) $row['product'],
        'status' => (string) $row['status'],
        'price' => (float) $row['price'],
        'short_description' => (string) $row['short_description'],
        'full_description' => (string) $row['full_description'],
        'images' => [
            'main' => !empty($main_image['pair_id']) ? 1 : 0,
            'additional' => count($additional_images),
        ],
        'booking' => $booking ? [
            'booking_type' => (string) $booking['booking_type'],
            'from_date' => (string) $booking['from_date'],
            'to_date' => (string) $booking['to_date'],
            'slot_time' => (int) $booking['slot_time'],
            'free_time' => (int) $booking['free_time'],
            'days_data' => $days_data,
        ] : null,
    ];
}

function fn_talario_analytics_partner_sync_write_response(): void
{
    $payload = fn_talario_analytics_partner_sync_write_payload();
    $operation = (string) ($payload['operation'] ?? '');
    $dry_run = !array_key_exists('dry_run', $payload) || (bool) $payload['dry_run'];
    $product_id = (int) ($payload['product_id'] ?? 0);
    $product_data = fn_talario_analytics_partner_sync_write_normalize_product($payload);

    if ($operation === 'update') {
        if ($product_id <= 0) {
            fn_talario_analytics_json_response(400, ['error' => 'product_id_required']);
        }
        $existing = db_get_row(
            'SELECT product_id, company_id FROM ?:products WHERE product_id = ?i',
            $product_id
        );
        if (!$existing) {
            fn_talario_analytics_json_response(404, ['error' => 'product_not_found']);
        }
        if (isset($product_data['company_id']) && (int) $existing['company_id'] !== (int) $product_data['company_id']) {
            fn_talario_analytics_json_response(409, ['error' => 'company_change_forbidden']);
        }
        fn_talario_analytics_partner_sync_write_require_company_allowed((int) $existing['company_id']);
    }

    if ($operation === 'create') {
        fn_talario_analytics_partner_sync_write_require_company_allowed((int) $product_data['company_id']);
    }

    if (isset($product_data['company_id'])) {
        $company_status = (string) db_get_field(
            'SELECT status FROM ?:companies WHERE company_id = ?i',
            (int) $product_data['company_id']
        );
        if ($company_status !== 'A') {
            fn_talario_analytics_json_response(400, ['error' => 'company_not_active']);
        }
    }

    if (isset($product_data['category_ids'])) {
        $found = array_map('intval', db_get_fields(
            'SELECT category_id FROM ?:categories WHERE category_id IN (?n)',
            $product_data['category_ids']
        ));
        sort($found);
        $expected = $product_data['category_ids'];
        sort($expected);
        if ($found !== $expected) {
            fn_talario_analytics_json_response(400, ['error' => 'category_not_found']);
        }
    }

    $images = null;
    if (isset($payload['images'])) {
        if (!is_array($payload['images'])) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_images']);
        }
        $images = $payload['images'];
    }

    $booking_data = null;
    if (isset($payload['booking'])) {
        if (!is_array($payload['booking'])) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_booking']);
        }
        $booking_data = fn_talario_analytics_partner_sync_write_normalize_booking($payload['booking']);
        $product_data['booking_data'] = $booking_data;
    }

    $plan = [
        'operation' => $operation,
        'product_id' => $operation === 'update' ? $product_id : null,
        'product' => array_diff_key($product_data, ['booking_data' => true]),
        'booking' => $booking_data,
        'images' => $images === null ? null : ['count' => count($images), 'replace' => true],
    ];

    if ($dry_run) {
        fn_talario_analytics_json_response(200, [
            'schema_version' => 'partner-sync.write-plan.v1',
            'dry_run' => true,
            'plan' => $plan,
        ]);
    }

    $approval_id = trim((string) ($payload['approval_id'] ?? ''));
    if (!preg_match('/^[A-Za-z0-9._:-]{6,128}$/', $approval_id)) {
        fn_talario_analytics_json_response(400, ['error' => 'approval_id_required']);
    }

    if (!defined('TALARIO_PARTNER_SYNC_DEV_WRITE') || TALARIO_PARTNER_SYNC_DEV_WRITE !== true) {
        fn_talario_analytics_json_response(403, ['error' => 'partner_sync_write_disabled']);
    }

    $approval_id_hash = hash('sha256', $approval_id);
    $temp_files = [];
    db_query('START TRANSACTION');
    try {
        if ($images !== null) {
            $temp_files = fn_talario_analytics_partner_sync_write_prepare_images(
                $images,
                $operation === 'update' ? $product_id : 0
            );
        }

        $lang_code = (string) \Tygh\Registry::get('settings.Appearance.default_language') ?: 'ru';
        $result_id = fn_update_product(
            $product_data,
            $operation === 'update' ? $product_id : 0,
            $lang_code
        );
        if (!$result_id) {
            throw new RuntimeException('product_update_failed');
        }
        $product_id = (int) $result_id;
        db_query('COMMIT');
        fn_talario_analytics_partner_sync_write_cleanup_images($temp_files);
    } catch (Throwable $exception) {
        db_query('ROLLBACK');
        fn_talario_analytics_partner_sync_write_cleanup_images($temp_files);
        fn_log_event('general', 'runtime', [
            'message' => 'Talario Partner Sync dev write failed',
            'operation' => $operation,
            'approval_id_hash' => $approval_id_hash,
            'error_class' => get_class($exception),
        ]);
        fn_talario_analytics_json_response(500, ['error' => 'partner_sync_write_failed']);
    }

    $readback = fn_talario_analytics_partner_sync_write_readback($product_id);

    fn_log_event('general', 'runtime', [
        'message' => 'Talario Partner Sync dev write completed',
        'operation' => $operation,
        'product_id' => $product_id,
        'approval_id_hash' => $approval_id_hash,
        'payload_sha256' => hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
    ]);

    fn_talario_analytics_json_response($operation === 'create' ? 201 : 200, [
        'schema_version' => 'partner-sync.write-result.v1',
        'dry_run' => false,
        'operation' => $operation,
        'approval_id_hash' => $approval_id_hash,
        'product_id' => $product_id,
        'readback' => $readback,
    ]);
}
