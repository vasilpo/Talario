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
    ];
}


function fn_talario_analytics_partner_sync_write_normalize_variation_plan(array $payload): ?array
{
    if (!array_key_exists('variation_plan', $payload)) {
        return null;
    }
    if (!is_array($payload['variation_plan']) || !$payload['variation_plan']) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_plan']);
    }

    $items = $payload['variation_plan'];
    if (count($items) > 100) {
        fn_talario_analytics_json_response(400, ['error' => 'too_many_variations', 'max' => 100]);
    }

    $age_feature = trim((string) ($payload['variation_axes']['age_feature'] ?? ''));
    $purchase_feature = trim((string) ($payload['variation_axes']['purchase_feature'] ?? 'Занятия'));
    if ($age_feature === '') {
        $age_feature = 'Возраст';
        foreach ($items as $item) {
            if (is_array($item) && preg_match('/класс/iu', (string) ($item['age_group'] ?? ''))) {
                $age_feature = 'Класс';
                break;
            }
        }
    }
    if (mb_strlen($age_feature, 'UTF-8') > 100 || mb_strlen($purchase_feature, 'UTF-8') > 100) {
        fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_axis']);
    }

    $normalized = [];
    $seen = [];
    foreach ($items as $index => $item) {
        if (!is_array($item)) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_item', 'index' => $index]);
        }
        $age_group = trim((string) ($item['age_group'] ?? ''));
        $purchase_option = trim((string) ($item['purchase_option'] ?? ''));
        if ($age_group === '' || $purchase_option === ''
            || mb_strlen($age_group, 'UTF-8') > 100
            || mb_strlen($purchase_option, 'UTF-8') > 160
        ) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_label', 'index' => $index]);
        }
        if (!array_key_exists('price', $item) || !is_numeric($item['price']) || (float) $item['price'] < 0) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_price', 'index' => $index]);
        }

        $capacity = isset($item['capacity']) ? (int) $item['capacity'] : (int) ($payload['capacity'] ?? 0);
        if ($capacity < 0 || $capacity > 100000) {
            fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_capacity', 'index' => $index]);
        }

        $sessions = isset($item['schedule']) && is_array($item['schedule']) ? $item['schedule'] : [];
        if (!$sessions) {
            fn_talario_analytics_json_response(400, ['error' => 'variation_schedule_required', 'index' => $index]);
        }

        $by_day = [];
        $duration = null;
        foreach ($sessions as $session_index => $session) {
            if (!is_array($session)) {
                fn_talario_analytics_json_response(400, ['error' => 'invalid_variation_session', 'index' => $index]);
            }
            $day = trim((string) ($session['day'] ?? ''));
            $start = trim((string) ($session['start'] ?? ''));
            $end = trim((string) ($session['end'] ?? ''));
            $session_duration = (int) ($session['duration'] ?? 0);
            if (!in_array($day, ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'], true)
                || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $start)
                || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $end)
                || $start >= $end
                || $session_duration < 1
                || $session_duration > 1440
            ) {
                fn_talario_analytics_json_response(400, [
                    'error' => 'invalid_variation_session',
                    'index' => $index,
                    'session' => $session_index,
                ]);
            }
            if ($duration === null) {
                $duration = $session_duration;
            } elseif ($duration !== $session_duration) {
                fn_talario_analytics_json_response(409, [
                    'error' => 'schedule_not_representable',
                    'reason' => 'mixed_duration_within_variation',
                    'index' => $index,
                ]);
            }
            if (isset($by_day[$day])) {
                fn_talario_analytics_json_response(409, [
                    'error' => 'schedule_not_representable',
                    'reason' => 'multiple_disjoint_sessions_same_day',
                    'index' => $index,
                    'day' => $day,
                ]);
            }
            $by_day[$day] = [
                'day' => $day,
                'start' => $start,
                'end' => $end,
                'duration' => $session_duration,
                'capacity' => $capacity,
            ];
        }

        $key = mb_strtolower($age_group . '|' . $purchase_option, 'UTF-8');
        if (isset($seen[$key])) {
            fn_talario_analytics_json_response(400, ['error' => 'duplicate_variation_combination', 'index' => $index]);
        }
        $seen[$key] = true;

        $normalized[] = [
            'age_group' => $age_group,
            'purchase_option' => $purchase_option,
            'price' => (float) $item['price'],
            'duration' => (int) $duration,
            'sessions' => array_values($by_day),
        ];
    }

    return [
        'age_feature' => $age_feature,
        'purchase_feature' => $purchase_feature,
        'items' => $normalized,
    ];
}

function fn_talario_analytics_partner_sync_write_find_feature(string $description): array
{
    $lang_code = (string) \Tygh\Registry::get('settings.Appearance.default_language') ?: 'ru';
    $rows = db_get_array(
        'SELECT pf.feature_id, pf.purpose, pfd.description'
        . ' FROM ?:product_features pf'
        . ' INNER JOIN ?:product_features_descriptions pfd'
        . ' ON pfd.feature_id = pf.feature_id AND pfd.lang_code = ?s'
        . ' WHERE pf.status IN (?a) AND LOWER(TRIM(pfd.description)) = LOWER(TRIM(?s))',
        $lang_code,
        ['A', 'H'],
        $description
    );
    if (count($rows) !== 1) {
        fn_talario_analytics_json_response(409, [
            'error' => 'variation_feature_not_unique',
            'feature' => $description,
            'matches' => count($rows),
        ]);
    }
    $row = reset($rows);
    $purpose = (string) ($row['purpose'] ?? '');
    if (!in_array($purpose, [
        \Tygh\Addons\ProductVariations\Product\FeaturePurposes::CREATE_CATALOG_ITEM,
        \Tygh\Addons\ProductVariations\Product\FeaturePurposes::CREATE_VARIATION_OF_CATALOG_ITEM,
    ], true)) {
        fn_talario_analytics_json_response(409, [
            'error' => 'variation_feature_wrong_purpose',
            'feature' => $description,
        ]);
    }
    return [
        'feature_id' => (int) $row['feature_id'],
        'description' => (string) $row['description'],
        'purpose' => $purpose,
    ];
}

function fn_talario_analytics_partner_sync_write_find_variant(int $feature_id, string $label): array
{
    $lang_code = (string) \Tygh\Registry::get('settings.Appearance.default_language') ?: 'ru';
    $rows = db_get_array(
        'SELECT fv.variant_id, fvd.variant'
        . ' FROM ?:product_feature_variants fv'
        . ' INNER JOIN ?:product_feature_variant_descriptions fvd'
        . ' ON fvd.variant_id = fv.variant_id AND fvd.lang_code = ?s'
        . ' WHERE fv.feature_id = ?i AND LOWER(TRIM(fvd.variant)) = LOWER(TRIM(?s))',
        $lang_code,
        $feature_id,
        $label
    );
    if (count($rows) !== 1) {
        fn_talario_analytics_json_response(409, [
            'error' => 'variation_variant_not_unique',
            'feature_id' => $feature_id,
            'variant' => $label,
            'matches' => count($rows),
        ]);
    }
    $row = reset($rows);
    return ['variant_id' => (int) $row['variant_id'], 'variant' => (string) $row['variant']];
}

function fn_talario_analytics_partner_sync_write_resolve_variation_plan(?array $plan): ?array
{
    if ($plan === null) {
        return null;
    }
    $age_feature = fn_talario_analytics_partner_sync_write_find_feature($plan['age_feature']);
    $purchase_feature = fn_talario_analytics_partner_sync_write_find_feature($plan['purchase_feature']);
    if ($age_feature['feature_id'] === $purchase_feature['feature_id']) {
        fn_talario_analytics_json_response(409, ['error' => 'variation_axes_must_differ']);
    }

    $age_variants = [];
    $purchase_variants = [];
    $items = [];
    foreach ($plan['items'] as $item) {
        if (!isset($age_variants[$item['age_group']])) {
            $age_variants[$item['age_group']] = fn_talario_analytics_partner_sync_write_find_variant(
                $age_feature['feature_id'],
                $item['age_group']
            );
        }
        if (!isset($purchase_variants[$item['purchase_option']])) {
            $purchase_variants[$item['purchase_option']] = fn_talario_analytics_partner_sync_write_find_variant(
                $purchase_feature['feature_id'],
                $item['purchase_option']
            );
        }
        $items[] = array_merge($item, [
            'age_variant_id' => $age_variants[$item['age_group']]['variant_id'],
            'purchase_variant_id' => $purchase_variants[$item['purchase_option']]['variant_id'],
        ]);
    }

    return [
        'age_feature' => $age_feature,
        'purchase_feature' => $purchase_feature,
        'items' => $items,
    ];
}

function fn_talario_analytics_partner_sync_write_booking_from_variation(
    array $item,
    array $base_booking_input
): array {
    $days = [];
    foreach (['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day) {
        $days[$day] = ['enabled' => false, 'start' => '', 'end' => ''];
    }
    foreach ($item['sessions'] as $session) {
        $days[$session['day']] = [
            'enabled' => true,
            'start' => $session['start'],
            'end' => $session['end'],
        ];
    }
    return fn_talario_analytics_partner_sync_write_normalize_booking([
        'from' => (string) ($base_booking_input['from'] ?? ''),
        'to' => (string) ($base_booking_input['to'] ?? ''),
        'slot_time' => $item['duration'],
        'free_time' => 0,
        'days' => $days,
    ]);
}

function fn_talario_analytics_partner_sync_write_apply_slot_capacities(int $product_id, array $item): void
{
    foreach ($item['sessions'] as $session) {
        if ((int) $session['capacity'] <= 0) {
            continue;
        }
        fn_ec_save_booking_data_by_amount([
            'product_id' => $product_id,
            'day' => $session['day'],
            'booking_data' => [[
                'start_time' => $session['start'],
                'end_time' => $session['end'],
                'amount' => (int) $session['capacity'],
            ]],
        ]);
    }
}

function fn_talario_analytics_partner_sync_write_apply_variations(
    int $base_product_id,
    array $resolved_plan,
    array $base_booking_input,
    string $lang_code
): array {
    $age_feature = $resolved_plan['age_feature'];
    $purchase_feature = $resolved_plan['purchase_feature'];
    $items = $resolved_plan['items'];
    $first = reset($items);

    $base_feature_values = [
        $age_feature['feature_id'] => $first['age_variant_id'],
        $purchase_feature['feature_id'] => $first['purchase_variant_id'],
    ];
    if (!fn_update_product_features_value($base_product_id, $base_feature_values, [], $lang_code)) {
        throw new RuntimeException('base_variation_features_update_failed');
    }

    $features = new \Tygh\Addons\ProductVariations\Product\Group\GroupFeatureCollection([
        new \Tygh\Addons\ProductVariations\Product\Group\GroupFeature(
            $age_feature['feature_id'],
            $age_feature['purpose']
        ),
        new \Tygh\Addons\ProductVariations\Product\Group\GroupFeature(
            $purchase_feature['feature_id'],
            $purchase_feature['purpose']
        ),
    ]);

    $request = new \Tygh\Addons\ProductVariations\Request\GenerateProductsAndCreateGroupRequest(
        $base_product_id,
        [],
        $features
    );
    $request->setFeaturesVariantsMap([
        $age_feature['feature_id'] => array_values(array_unique(array_column($items, 'age_variant_id'))),
        $purchase_feature['feature_id'] => array_values(array_unique(array_column($items, 'purchase_variant_id'))),
    ]);
    $result = \Tygh\Addons\ProductVariations\ServiceProvider::getService()
        ->generateProductsAndCreateGroup($request);
    if (!$result->isSuccess()) {
        throw new RuntimeException('variation_group_create_failed');
    }

    $group = \Tygh\Addons\ProductVariations\ServiceProvider::getGroupRepository()
        ->findGroupByProductId($base_product_id);
    if (!$group) {
        throw new RuntimeException('variation_group_readback_failed');
    }
    $product_ids = $group->getProductIds();
    $repository = \Tygh\Addons\ProductVariations\ServiceProvider::getProductRepository();
    $products = $repository->findProducts($product_ids);
    $products = $repository->loadProductsFeatures($products, $features);

    $combination_map = [];
    foreach ($products as $product) {
        $values = [];
        foreach ((array) ($product['variation_features'] ?? []) as $feature_id => $feature) {
            $values[(int) $feature_id] = (int) ($feature['variant_id'] ?? 0);
        }
        $key = ($values[$age_feature['feature_id']] ?? 0) . ':'
            . ($values[$purchase_feature['feature_id']] ?? 0);
        $combination_map[$key] = (int) $product['product_id'];
    }

    $updated = [];
    foreach ($items as $item) {
        $key = $item['age_variant_id'] . ':' . $item['purchase_variant_id'];
        if (empty($combination_map[$key])) {
            throw new RuntimeException('variation_product_mapping_failed');
        }
        $product_id = $combination_map[$key];
        $booking_data = fn_talario_analytics_partner_sync_write_booking_from_variation($item, $base_booking_input);
        $result_id = fn_update_product([
            'price' => $item['price'],
            'booking_data' => $booking_data,
        ], $product_id, $lang_code);
        if (!$result_id) {
            throw new RuntimeException('variation_product_update_failed');
        }
        fn_talario_analytics_partner_sync_write_apply_slot_capacities($product_id, $item);
        $updated[] = [
            'product_id' => $product_id,
            'age_group' => $item['age_group'],
            'purchase_option' => $item['purchase_option'],
            'price' => $item['price'],
        ];
    }

    return [
        'group_id' => (int) $group->getId(),
        'products' => $updated,
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
        'readback' => $readback,
    ]);
}
