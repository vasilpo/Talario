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
    $raw = PHP_SAPI === 'cli'
        ? (string) stream_get_contents(STDIN)
        : (string) file_get_contents('php://input');
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


function fn_talario_analytics_partner_sync_normalize_variation_plan(array $payload): ?array
{
    if (!array_key_exists('variation_plan', $payload)) {
        return null;
    }

    if (!is_array($payload['variation_plan']) || !$payload['variation_plan'] || count($payload['variation_plan']) > 100) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_plan']);
    }

    $allowed_days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    $normalized = [];
    $seen = [];

    foreach ($payload['variation_plan'] as $index => $item) {
        if (!is_array($item)) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_item', 'index' => $index]);
        }

        $group = trim((string) ($item['age_group'] ?? ''));
        $option = trim((string) ($item['purchase_option'] ?? ''));
        if ($group === '' || mb_strlen($group, 'UTF-8') > 120) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_group', 'index' => $index]);
        }
        if ($option === '' || mb_strlen($option, 'UTF-8') > 180) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_purchase_option', 'index' => $index]);
        }

        $price = $item['price'] ?? null;
        if ($price !== null && (!is_numeric($price) || (float) $price < 0 || (float) $price > 100000000)) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_price', 'index' => $index]);
        }

        $capacity = $item['capacity'] ?? null;
        if ($capacity !== null && (!is_numeric($capacity) || (int) $capacity < 1 || (int) $capacity > 100000)) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_capacity', 'index' => $index]);
        }

        $schedule = isset($item['schedule']) && is_array($item['schedule']) ? $item['schedule'] : [];
        if (count($schedule) > 32) {
            fn_talario_analytics_json_response(400, ['error' => 'variation_schedule_too_large', 'index' => $index]);
        }

        $normalized_schedule = [];
        foreach ($schedule as $session_index => $session) {
            if (!is_array($session)) {
                fn_talario_analytics_json_response(400, [
                    'error' => 'invalid_variation_schedule_item',
                    'index' => $index,
                    'session_index' => $session_index,
                ]);
            }
            $day = strtolower(trim((string) ($session['day'] ?? '')));
            $start = trim((string) ($session['start'] ?? ''));
            $end = trim((string) ($session['end'] ?? ''));
            $duration = (int) ($session['duration'] ?? 0);
            if (!in_array($day, $allowed_days, true)
                || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $start)
                || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $end)
                || $start >= $end
                || $duration < 1
                || $duration > 1440
            ) {
                fn_talario_analytics_json_response(400, [
                    'error' => 'invalid_variation_schedule_item',
                    'index' => $index,
                    'session_index' => $session_index,
                ]);
            }
            $normalized_schedule[] = [
                'day' => $day,
                'start' => $start,
                'end' => $end,
                'duration' => $duration,
            ];
        }

        $key = mb_strtolower($group . "\n" . $option, 'UTF-8');
        if (isset($seen[$key])) {
            fn_talario_analytics_json_response(400, ['error' => 'duplicate_variation_item', 'index' => $index]);
        }
        $seen[$key] = true;

        $normalized[] = [
            'age_group' => $group,
            'purchase_option' => $option,
            'price' => $price === null ? null : (float) $price,
            'capacity' => $capacity === null ? null : (int) $capacity,
            'schedule' => $normalized_schedule,
        ];
    }

    return $normalized;
}

function fn_talario_analytics_partner_sync_resolve_variation_axis(
    array $candidate_names,
    array $variant_labels,
    string $lang_code
): array {
    $candidate_names = array_values(array_unique(array_filter(array_map('trim', $candidate_names))));
    $variant_labels = array_values(array_unique(array_filter(array_map('trim', $variant_labels))));
    if (!$candidate_names || !$variant_labels) {
        return ['resolved' => false, 'feature_id' => null, 'missing_variants' => $variant_labels];
    }

    $features = db_get_array(
        'SELECT pf.feature_id, pf.purpose, pfd.description'
        . ' FROM ?:product_features pf'
        . ' INNER JOIN ?:product_features_descriptions pfd'
        . ' ON pfd.feature_id = pf.feature_id AND pfd.lang_code = ?s'
        . ' WHERE pfd.description IN (?a)'
        . ' AND pf.purpose IN (?a)'
        . ' ORDER BY pf.feature_id',
        $lang_code,
        $candidate_names,
        ['group_catalog_item', 'group_variation_catalog_item']
    );

    usort($features, static function (array $left, array $right) use ($candidate_names): int {
        $left_pos = array_search((string) $left['description'], $candidate_names, true);
        $right_pos = array_search((string) $right['description'], $candidate_names, true);
        $left_pos = $left_pos === false ? PHP_INT_MAX : $left_pos;
        $right_pos = $right_pos === false ? PHP_INT_MAX : $right_pos;
        return $left_pos <=> $right_pos;
    });

    foreach ($features as $feature) {
        $rows = db_get_array(
            'SELECT pfv.variant_id, pfvd.variant'
            . ' FROM ?:product_feature_variants pfv'
            . ' INNER JOIN ?:product_feature_variant_descriptions pfvd'
            . ' ON pfvd.variant_id = pfv.variant_id AND pfvd.lang_code = ?s'
            . ' WHERE pfv.feature_id = ?i',
            $lang_code,
            (int) $feature['feature_id']
        );

        $by_label = [];
        foreach ($rows as $row) {
            $label = trim((string) $row['variant']);
            if ($label !== '') {
                $by_label[mb_strtolower($label, 'UTF-8')] = (int) $row['variant_id'];
            }
        }

        $resolved_variants = [];
        $missing = [];
        foreach ($variant_labels as $label) {
            $key = mb_strtolower($label, 'UTF-8');
            if (!isset($by_label[$key])) {
                $missing[] = $label;
            } else {
                $resolved_variants[$label] = $by_label[$key];
            }
        }

        if (!$missing) {
            return [
                'resolved' => true,
                'feature_id' => (int) $feature['feature_id'],
                'feature_name' => (string) $feature['description'],
                'purpose' => (string) $feature['purpose'],
                'variants' => $resolved_variants,
                'missing_variants' => [],
            ];
        }
    }

    return [
        'resolved' => false,
        'feature_id' => null,
        'feature_name' => null,
        'purpose' => null,
        'variants' => [],
        'missing_variants' => $variant_labels,
    ];
}

function fn_talario_analytics_partner_sync_resolve_variation_plan(array $variation_plan): array
{
    $lang_code = (string) \Tygh\Registry::get('settings.Appearance.default_language') ?: 'ru';
    $groups = [];
    $options = [];
    foreach ($variation_plan as $item) {
        $groups[] = (string) $item['age_group'];
        $options[] = (string) $item['purchase_option'];
    }
    $groups = array_values(array_unique($groups));
    $options = array_values(array_unique($options));

    $group_axis = fn_talario_analytics_partner_sync_resolve_variation_axis(
        ['Возраст', 'Возрастная группа', 'Класс'],
        $groups,
        $lang_code
    );
    $purchase_axis = fn_talario_analytics_partner_sync_resolve_variation_axis(
        ['Занятия', 'Занятие'],
        $options,
        $lang_code
    );

    $resolved_items = [];
    foreach ($variation_plan as $item) {
        $resolved_items[] = [
            'age_group' => $item['age_group'],
            'purchase_option' => $item['purchase_option'],
            'price' => $item['price'],
            'capacity' => $item['capacity'],
            'schedule' => $item['schedule'],
            'group_variant_id' => $group_axis['resolved']
                ? $group_axis['variants'][$item['age_group']]
                : null,
            'purchase_variant_id' => $purchase_axis['resolved']
                ? $purchase_axis['variants'][$item['purchase_option']]
                : null,
        ];
    }

    return [
        'resolved' => $group_axis['resolved'] && $purchase_axis['resolved'],
        'group_axis' => $group_axis,
        'purchase_axis' => $purchase_axis,
        'items' => $resolved_items,
    ];
}


function fn_talario_analytics_partner_sync_public_variation_axis(array $axis): array
{
    return [
        'resolved' => !empty($axis['resolved']),
        'feature_name' => $axis['feature_name'] ?? null,
        'missing_variants' => array_values((array) ($axis['missing_variants'] ?? [])),
    ];
}

function fn_talario_analytics_partner_sync_public_variation_resolution(array $resolution): array
{
    return [
        'resolved' => !empty($resolution['resolved']),
        'group_axis' => fn_talario_analytics_partner_sync_public_variation_axis((array) $resolution['group_axis']),
        'purchase_axis' => fn_talario_analytics_partner_sync_public_variation_axis((array) $resolution['purchase_axis']),
        'count' => count((array) ($resolution['items'] ?? [])),
        'prices_complete' => !array_filter(
            (array) ($resolution['items'] ?? []),
            static fn(array $item): bool => $item['price'] === null
        ),
        'capacities_complete' => !array_filter(
            (array) ($resolution['items'] ?? []),
            static fn(array $item): bool => $item['capacity'] === null
        ),
    ];
}


function fn_talario_analytics_partner_sync_variation_booking(
    array $item,
    string $from,
    string $to
): array {
    $schedule = (array) ($item['schedule'] ?? []);
    if (!$schedule) {
        throw new RuntimeException('variation_schedule_required');
    }

    $durations = [];
    $by_day = [];
    foreach ($schedule as $session) {
        $duration = (int) ($session['duration'] ?? 0);
        $durations[$duration] = true;
        $day = (string) ($session['day'] ?? '');
        $by_day[$day][] = $session;
    }
    if (count($durations) !== 1) {
        throw new RuntimeException('variation_duration_must_be_uniform');
    }
    $slot_time = (int) array_key_first($durations);

    $days = [];
    foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
        $sessions = $by_day[$day] ?? [];
        if (!$sessions) {
            $days[$day] = ['enabled' => false, 'start' => '', 'end' => ''];
            continue;
        }
        $starts = array_column($sessions, 'start');
        $ends = array_column($sessions, 'end');
        sort($starts);
        sort($ends);
        $days[$day] = [
            'enabled' => true,
            'start' => reset($starts),
            'end' => end($ends),
        ];
    }

    return fn_talario_analytics_partner_sync_write_normalize_booking([
        'from' => $from,
        'to' => $to,
        'slot_time' => $slot_time,
        'free_time' => 0,
        'days' => $days,
    ]);
}

function fn_talario_analytics_partner_sync_exact_slots_by_day(array $item): array
{
    $capacity = $item['capacity'] ?? null;
    if ($capacity === null) {
        throw new RuntimeException('variation_capacity_required');
    }

    $result = [];
    foreach ((array) ($item['schedule'] ?? []) as $session) {
        $day = (string) $session['day'];
        $result[$day][] = [
            'start_time' => (string) $session['start'],
            'end_time' => (string) $session['end'],
            'amount' => (int) $capacity,
        ];
    }
    return $result;
}

function fn_talario_analytics_partner_sync_find_variation_products(
    array $product_ids,
    array $resolution
): array {
    $group_feature_id = (int) $resolution['group_axis']['feature_id'];
    $purchase_feature_id = (int) $resolution['purchase_axis']['feature_id'];
    $rows = db_get_array(
        'SELECT product_id, feature_id, variant_id'
        . ' FROM ?:product_features_values'
        . ' WHERE product_id IN (?n) AND feature_id IN (?n)',
        $product_ids,
        [$group_feature_id, $purchase_feature_id]
    );

    $features = [];
    foreach ($rows as $row) {
        $features[(int) $row['product_id']][(int) $row['feature_id']] = (int) $row['variant_id'];
    }

    $map = [];
    foreach ($features as $product_id => $values) {
        if (!isset($values[$group_feature_id], $values[$purchase_feature_id])) {
            continue;
        }
        $key = $values[$group_feature_id] . ':' . $values[$purchase_feature_id];
        if (isset($map[$key])) {
            throw new RuntimeException('duplicate_variation_combination');
        }
        $map[$key] = (int) $product_id;
    }
    return $map;
}

function fn_talario_analytics_partner_sync_apply_create_variations(
    int $base_product_id,
    array $resolution,
    array $booking_payload,
    string $lang_code
): array {
    if (empty($resolution['resolved'])) {
        throw new RuntimeException('variation_resolution_required');
    }
    if (!function_exists('fn_ec_save_booking_data_by_amount')) {
        throw new RuntimeException('ecarter_slot_writer_unavailable');
    }

    $group_feature_id = (int) $resolution['group_axis']['feature_id'];
    $purchase_feature_id = (int) $resolution['purchase_axis']['feature_id'];
    $feature_ids = [$group_feature_id, $purchase_feature_id];

    $repository = \Tygh\Addons\ProductVariations\ServiceProvider::getProductRepository();
    $features = $repository->findFeatures($feature_ids);
    if (count($features) !== 2) {
        throw new RuntimeException('variation_features_unavailable');
    }

    $group_features = \Tygh\Addons\ProductVariations\Product\Group\GroupFeatureCollection::createFromFeatureList(
        $features
    );
    if ($group_features->count() !== 2) {
        throw new RuntimeException('variation_group_features_invalid');
    }

    $feature_variants = [
        $group_feature_id => [],
        $purchase_feature_id => [],
    ];
    foreach ((array) $resolution['items'] as $item) {
        if ($item['price'] === null) {
            throw new RuntimeException('variation_price_required');
        }
        if ($item['capacity'] === null) {
            throw new RuntimeException('variation_capacity_required');
        }
        $feature_variants[$group_feature_id][(int) $item['group_variant_id']] = (int) $item['group_variant_id'];
        $feature_variants[$purchase_feature_id][(int) $item['purchase_variant_id']] = (int) $item['purchase_variant_id'];
    }

    $request = new \Tygh\Addons\ProductVariations\Request\GenerateProductsAndCreateGroupRequest(
        $base_product_id,
        [],
        $group_features
    );
    $request->setFeaturesVariantsMap($feature_variants);

    $service = \Tygh\Addons\ProductVariations\ServiceProvider::getService();
    $result = $service->generateProductsAndCreateGroup($request);
    if (!$result->isSuccess()) {
        throw new RuntimeException('variation_group_create_failed');
    }

    $group = $result->getData('group');
    if (!$group instanceof \Tygh\Addons\ProductVariations\Product\Group\Group) {
        throw new RuntimeException('variation_group_readback_failed');
    }
    $product_ids = array_map('intval', $group->getProductIds());
    if (count($product_ids) !== count($resolution['items'])) {
        throw new RuntimeException('variation_count_mismatch');
    }

    $product_map = fn_talario_analytics_partner_sync_find_variation_products($product_ids, $resolution);
    $applied = [];

    foreach ((array) $resolution['items'] as $item) {
        $key = (int) $item['group_variant_id'] . ':' . (int) $item['purchase_variant_id'];
        if (!isset($product_map[$key])) {
            throw new RuntimeException('variation_product_mapping_failed');
        }
        $variation_product_id = (int) $product_map[$key];

        $variation_booking = fn_talario_analytics_partner_sync_variation_booking(
            $item,
            (string) ($booking_payload['from'] ?? ''),
            (string) ($booking_payload['to'] ?? '')
        );
        $updated_id = fn_update_product(
            [
                'price' => (float) $item['price'],
                'booking_data' => $variation_booking,
            ],
            $variation_product_id,
            $lang_code
        );
        if (!$updated_id) {
            throw new RuntimeException('variation_product_update_failed');
        }

        foreach (fn_talario_analytics_partner_sync_exact_slots_by_day($item) as $day => $slots) {
            fn_ec_save_booking_data_by_amount([
                'product_id' => $variation_product_id,
                'day' => $day,
                'booking_data' => $slots,
            ]);
        }

        $applied[] = [
            'product_id' => $variation_product_id,
            'age_group' => (string) $item['age_group'],
            'purchase_option' => (string) $item['purchase_option'],
            'price' => (float) $item['price'],
            'slot_count' => count((array) $item['schedule']),
        ];
    }

    return [
        'group_id' => (int) $group->getId(),
        'count' => count($applied),
        'items' => $applied,
    ];
}

function fn_talario_analytics_partner_sync_write_prepare_images(array $images, int $product_id): array
{
    if (count($images) > 12) {
        fn_talario_analytics_json_response(400, ['error' => 'too_many_images', 'max_images' => 12]);
    }

    $temp_files = [];
    $old_pair_ids = [];
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
            $old_pair_ids[] = (int) $main['pair_id'];
        }
        foreach ((array) fn_get_image_pairs($product_id, 'product', 'A', true, true, DEFAULT_LANGUAGE) as $pair) {
            if (!empty($pair['pair_id'])) {
                $old_pair_ids[] = (int) $pair['pair_id'];
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

    return [
        'temp_files' => $temp_files,
        'old_pair_ids' => array_values(array_unique($old_pair_ids)),
    ];
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

    $variation_plan = fn_talario_analytics_partner_sync_normalize_variation_plan($payload);
    $variation_resolution = $variation_plan === null
        ? null
        : fn_talario_analytics_partner_sync_resolve_variation_plan($variation_plan);

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
        'variations' => $variation_resolution === null
            ? null
            : fn_talario_analytics_partner_sync_public_variation_resolution($variation_resolution),
    ];

    if ($dry_run) {
        fn_talario_analytics_json_response(200, [
            'schema_version' => 'partner-sync.write-plan.v1',
            'dry_run' => true,
            'plan' => $plan,
        ]);
    }

    if ($variation_resolution !== null && !$variation_resolution['resolved']) {
        fn_talario_analytics_json_response(409, [
            'error' => 'variation_resolution_required',
            'variation_resolution' => fn_talario_analytics_partner_sync_public_variation_resolution(
                $variation_resolution
            ),
        ]);
    }

    if ($variation_resolution !== null && $operation === 'update') {
        fn_talario_analytics_json_response(409, ['error' => 'variation_update_not_implemented']);
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
    $old_pair_ids = [];
    $variation_apply = null;
    db_query('START TRANSACTION');
    try {
        if ($images !== null) {
            $prepared_images = fn_talario_analytics_partner_sync_write_prepare_images(
                $images,
                $operation === 'update' ? $product_id : 0
            );
            $temp_files = $prepared_images['temp_files'];
            $old_pair_ids = $prepared_images['old_pair_ids'];
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

        $variation_apply = null;
        if ($variation_resolution !== null) {
            if (!isset($payload['booking']) || !is_array($payload['booking'])) {
                throw new RuntimeException('variation_booking_range_required');
            }
            $variation_apply = fn_talario_analytics_partner_sync_apply_create_variations(
                $product_id,
                $variation_resolution,
                $payload['booking'],
                $lang_code
            );
        }

        db_query('COMMIT');

        // Only after the new product/images are safely saved do we remove the prior image pairs.
        foreach ($old_pair_ids as $old_pair_id) {
            fn_delete_image_pair((int) $old_pair_id);
        }

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
        'variations' => $variation_apply,
        'readback' => $readback,
    ]);
}
