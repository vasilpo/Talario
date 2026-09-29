<?php

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "PARTNER_SYNC_FEATURE_DISCOVERY_CLI_ONLY\n");
    exit(2);
}

$requested_root = getenv('TALARIO_PARTNER_SYNC_ROOT');
if (!is_string($requested_root) || $requested_root === '' || $requested_root[0] !== '/') {
    fwrite(STDERR, "PARTNER_SYNC_FEATURE_DISCOVERY_DEV_COPY_ONLY\n");
    exit(3);
}

$root = realpath($requested_root);
if ($root === false) {
    fwrite(STDERR, "PARTNER_SYNC_FEATURE_DISCOVERY_ROOT_NOT_FOUND\n");
    exit(3);
}

$normalized_root = str_replace('\\', '/', $root);
if (!str_ends_with($normalized_root, '/talario.ru/public_html/dev_copy')) {
    fwrite(STDERR, "PARTNER_SYNC_FEATURE_DISCOVERY_DEV_COPY_ONLY\n");
    exit(3);
}

define('AREA', 'A');
define('ACCOUNT_TYPE', 'admin');

require $root . '/init.php';

if (!function_exists('fn_is_development') || !fn_is_development()) {
    fwrite(STDERR, "PARTNER_SYNC_FEATURE_DISCOVERY_DEVELOPMENT_REQUIRED\n");
    exit(4);
}
if (!defined('TALARIO_PARTNER_SYNC_DEV_COPY') || TALARIO_PARTNER_SYNC_DEV_COPY !== true) {
    fwrite(STDERR, "PARTNER_SYNC_FEATURE_DISCOVERY_GATE_DISABLED\n");
    exit(4);
}

$lang_code = (string) \Tygh\Registry::get('settings.Appearance.default_language');
if ($lang_code === '') {
    $lang_code = 'ru';
}

$filter_rows = db_get_array(
    'SELECT pf.filter_id, pf.feature_id, pf.status AS filter_status,'
    . ' pfd.filter, f.feature_type, f.purpose, fd.description'
    . ' FROM ?:product_filters pf'
    . ' LEFT JOIN ?:product_filter_descriptions pfd'
    . ' ON pfd.filter_id = pf.filter_id AND pfd.lang_code = ?s'
    . ' INNER JOIN ?:product_features f ON f.feature_id = pf.feature_id'
    . ' LEFT JOIN ?:product_features_descriptions fd'
    . ' ON fd.feature_id = f.feature_id AND fd.lang_code = ?s'
    . ' WHERE pfd.filter LIKE ?s OR fd.description LIKE ?s'
    . ' OR pfd.filter LIKE ?s OR fd.description LIKE ?s'
    . ' ORDER BY pf.filter_id ASC, pf.feature_id ASC',
    $lang_code,
    $lang_code,
    '%Возраст%',
    '%Возраст%',
    '%Катег%',
    '%Катег%'
);

$features = [];
$feature_ids = [];
foreach ($filter_rows as $row) {
    $feature_id = (int) $row['feature_id'];
    if ($feature_id <= 0) {
        continue;
    }
    $feature_ids[$feature_id] = true;
    if (!isset($features[$feature_id])) {
        $features[$feature_id] = [
            'feature_id' => $feature_id,
            'feature_name' => (string) ($row['description'] ?? ''),
            'feature_type' => (string) ($row['feature_type'] ?? ''),
            'purpose' => (string) ($row['purpose'] ?? ''),
            'filters' => [],
            'variants' => [],
        ];
    }
    $features[$feature_id]['filters'][] = [
        'filter_id' => (int) $row['filter_id'],
        'filter_name' => (string) ($row['filter'] ?? ''),
        'status' => (string) ($row['filter_status'] ?? ''),
    ];
}

foreach (array_keys($feature_ids) as $feature_id) {
    foreach (db_get_array(
        'SELECT pfv.variant_id, pfvd.variant'
        . ' FROM ?:product_feature_variants pfv'
        . ' INNER JOIN ?:product_feature_variant_descriptions pfvd'
        . ' ON pfvd.variant_id = pfv.variant_id AND pfvd.lang_code = ?s'
        . ' WHERE pfv.feature_id = ?i'
        . ' ORDER BY pfv.position ASC, pfv.variant_id ASC'
        . ' LIMIT 500',
        $lang_code,
        $feature_id
    ) as $variant) {
        $features[$feature_id]['variants'][] = [
            'variant_id' => (int) $variant['variant_id'],
            'label' => (string) $variant['variant'],
        ];
    }
}

$samples = [];
if ($feature_ids) {
    $sample_rows = db_get_array(
        'SELECT pfv.product_id, pfv.feature_id, pfv.variant_id,'
        . ' COALESCE(pfvd.variant, ?s) AS variant'
        . ' FROM ?:product_features_values pfv'
        . ' LEFT JOIN ?:product_feature_variant_descriptions pfvd'
        . ' ON pfvd.variant_id = pfv.variant_id AND pfvd.lang_code = ?s'
        . ' WHERE pfv.feature_id IN (?n) AND pfv.lang_code = ?s'
        . ' AND pfv.variant_id > 0'
        . ' ORDER BY pfv.product_id DESC, pfv.feature_id ASC, pfv.variant_id ASC'
        . ' LIMIT 120',
        '',
        $lang_code,
        array_keys($feature_ids),
        $lang_code
    );

    $sample_product_ids = [];
    foreach ($sample_rows as $row) {
        $sample_product_ids[(int) $row['product_id']] = true;
    }

    $categories_by_product = [];
    if ($sample_product_ids) {
        foreach (db_get_array(
            'SELECT pc.product_id, pc.category_id, cd.category'
            . ' FROM ?:products_categories pc'
            . ' INNER JOIN ?:category_descriptions cd'
            . ' ON cd.category_id = pc.category_id AND cd.lang_code = ?s'
            . ' WHERE pc.product_id IN (?n)'
            . ' ORDER BY pc.product_id ASC, pc.position ASC, pc.category_id ASC',
            $lang_code,
            array_keys($sample_product_ids)
        ) as $row) {
            $product_id = (int) $row['product_id'];
            $categories_by_product[$product_id][] = [
                'category_id' => (int) $row['category_id'],
                'name' => (string) $row['category'],
            ];
        }
    }

    foreach ($sample_rows as $row) {
        $product_id = (int) $row['product_id'];
        $samples[] = [
            'product_id' => $product_id,
            'feature_id' => (int) $row['feature_id'],
            'variant_id' => (int) $row['variant_id'],
            'variant' => (string) $row['variant'],
            'categories' => $categories_by_product[$product_id] ?? [],
        ];
    }
}

echo json_encode([
    'schema_version' => 'partner-sync.feature-discovery.v1',
    'source' => 'dev_copy',
    'features' => array_values($features),
    'samples' => $samples,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
