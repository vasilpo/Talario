<?php

defined('BOOTSTRAP') or die('Access denied');

function fn_talario_vendor_cabinet_ensure_center_storage()
{
    static $ready = false;

    if ($ready) {
        return;
    }

    db_query(
        'CREATE TABLE IF NOT EXISTS ?:talario_vendor_centers ('
        . 'company_id int unsigned NOT NULL,'
        . 'name varchar(255) NOT NULL DEFAULT \'\','
        . 'description varchar(180) NOT NULL DEFAULT \'\','
        . 'address varchar(500) NOT NULL DEFAULT \'\','
        . 'address_details text NULL,'
        . 'primary_location_id int unsigned NOT NULL DEFAULT 0,'
        . 'updated_at int unsigned NOT NULL DEFAULT 0,'
        . 'PRIMARY KEY (company_id)'
        . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $fields = fn_get_table_fields('talario_vendor_centers');
    if (!in_array('address', $fields, true)) {
        db_query("ALTER TABLE ?:talario_vendor_centers ADD `address` varchar(500) NOT NULL DEFAULT '' AFTER `description`");
    }
    if (!in_array('address_details', $fields, true)) {
        db_query('ALTER TABLE ?:talario_vendor_centers ADD `address_details` text NULL AFTER `address`');
    }
    if (!in_array('primary_location_id', $fields, true)) {
        db_query('ALTER TABLE ?:talario_vendor_centers ADD `primary_location_id` int unsigned NOT NULL DEFAULT 0 AFTER `address_details`');
    }

    $ready = true;
}

function fn_talario_vendor_cabinet_get_center($company_id)
{
    $company_id = (int) $company_id;
    if (!$company_id) {
        return [
            'company_id' => 0,
            'name' => '',
            'description' => '',
            'address' => '',
            'address_details' => '',
            'primary_location_id' => 0,
        ];
    }

    fn_talario_vendor_cabinet_ensure_center_storage();

    $center = db_get_row(
        'SELECT company_id, name, description, address, address_details, primary_location_id, updated_at '
        . 'FROM ?:talario_vendor_centers WHERE company_id = ?i',
        $company_id
    );

    if (!$center) {
        return [
            'company_id' => $company_id,
            'name' => '',
            'description' => '',
            'address' => '',
            'address_details' => '',
            'primary_location_id' => 0,
            'updated_at' => 0,
        ];
    }

    return $center;
}

function fn_talario_vendor_cabinet_get_center_name($company_id)
{
    $center = fn_talario_vendor_cabinet_get_center($company_id);
    return (string) ($center['name'] ?? '');
}

function fn_talario_vendor_cabinet_update_center($company_id, array $center_data)
{
    $company_id = (int) $company_id;
    if (!$company_id) {
        return false;
    }

    fn_talario_vendor_cabinet_ensure_center_storage();

    $current = fn_talario_vendor_cabinet_get_center($company_id);
    $data = [
        'company_id' => $company_id,
        'name' => trim((string) ($center_data['name'] ?? '')),
        'description' => trim((string) ($center_data['description'] ?? '')),
        'address' => trim((string) ($center_data['address'] ?? '')),
        'address_details' => trim((string) ($center_data['address_details'] ?? '')),
        'primary_location_id' => isset($center_data['primary_location_id'])
            ? (int) $center_data['primary_location_id']
            : (int) ($current['primary_location_id'] ?? 0),
        'updated_at' => TIME,
    ];

    db_query('REPLACE INTO ?:talario_vendor_centers ?e', $data);

    return true;
}

function fn_talario_vendor_cabinet_get_allowed_categories()
{
    $default_category_ids = [267, 433, 268, 269, 270, 275, 276, 277, 273, 274, 352, 434, 272];
    $category_ids = array_values(array_unique(array_filter(array_map(
        'intval',
        preg_split('/[\s,;]+/', (string) \Tygh\Registry::get('addons.talario_vendor_cabinet.allowed_category_ids'))
    ))));
    if (!$category_ids) {
        // development deploys only pull Git; use the same server-side
        // whitelist when the new setting has not been installed yet.
        $category_ids = $default_category_ids;
    }

    $rows = db_get_array(
        'SELECT c.category_id, cd.category FROM ?:categories AS c '
        . 'INNER JOIN ?:category_descriptions AS cd ON cd.category_id = c.category_id AND cd.lang_code = ?s '
        . 'WHERE c.category_id IN (?n) AND c.status = ?s',
        DESCR_SL,
        $category_ids,
        'A'
    );

    $by_id = [];
    foreach ($rows as $row) {
        $by_id[(int) $row['category_id']] = $row;
    }

    $result = [];
    foreach ($category_ids as $category_id) {
        if (isset($by_id[$category_id])) {
            $result[] = $by_id[$category_id];
        }
    }

    return $result;
}

function fn_talario_vendor_cabinet_ensure_preview_storage()
{
    db_query(
        'CREATE TABLE IF NOT EXISTS ?:talario_class_preview_revisions ('
        . 'token_hash char(64) NOT NULL, product_id int unsigned NOT NULL, company_id int unsigned NOT NULL, '
        . 'user_id int unsigned NOT NULL, revision mediumtext NOT NULL, expires_at int unsigned NOT NULL, '
        . 'created_at int unsigned NOT NULL, PRIMARY KEY (token_hash), KEY expires_at (expires_at), '
        . 'KEY owner_product (company_id,user_id,product_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8'
    );
}

function fn_talario_vendor_cabinet_store_preview_revision($product_id, $company_id, $user_id, array $revision)
{
    fn_talario_vendor_cabinet_ensure_preview_storage();
    db_query('DELETE FROM ?:talario_class_preview_revisions WHERE expires_at < ?i', TIME);
    $token = bin2hex(random_bytes(32));
    db_query('INSERT INTO ?:talario_class_preview_revisions ?e', [
        'token_hash' => hash('sha256', $token),
        'product_id' => (int) $product_id,
        'company_id' => (int) $company_id,
        'user_id' => (int) $user_id,
        'revision' => serialize($revision),
        'expires_at' => TIME + 15 * 60,
        'created_at' => TIME,
    ]);
    return $token;
}

function fn_talario_vendor_cabinet_get_preview_revision($token, $product_id, $company_id, $user_id)
{
    if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/D', $token)) { return []; }
    fn_talario_vendor_cabinet_ensure_preview_storage();
    db_query('DELETE FROM ?:talario_class_preview_revisions WHERE expires_at < ?i', TIME);
    $row = db_get_row(
        'SELECT revision FROM ?:talario_class_preview_revisions WHERE token_hash = ?s AND product_id = ?i '
        . 'AND company_id = ?i AND user_id = ?i AND expires_at >= ?i',
        hash('sha256', $token), $product_id, $company_id, $user_id, TIME
    );
    return $row ? (array) unserialize($row['revision'], ['allowed_classes' => false]) : [];
}

function fn_talario_vendor_cabinet_ensure_moderation_storage()
{
    static $ready = false;
    if ($ready) {
        return;
    }
    db_query(
        'CREATE TABLE IF NOT EXISTS ?:talario_class_moderation ('
        . 'product_id int unsigned NOT NULL, company_id int unsigned NOT NULL, group_id int unsigned NOT NULL DEFAULT 0,'
        . 'variation_ids text NOT NULL, active char(1) NOT NULL DEFAULT \'Y\', updated_at int unsigned NOT NULL DEFAULT 0,'
        . 'PRIMARY KEY (product_id), KEY active (active)'
        . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
    $ready = true;
}

function fn_talario_vendor_cabinet_mark_class_moderation($product_id, $company_id, $group_id, array $variation_ids)
{
    fn_talario_vendor_cabinet_ensure_moderation_storage();
    db_replace_into('talario_class_moderation', [
        'product_id' => (int) $product_id,
        'company_id' => (int) $company_id,
        'group_id' => (int) $group_id,
        'variation_ids' => json_encode(array_values(array_unique(array_map('intval', $variation_ids)))),
        'active' => 'Y',
        'updated_at' => TIME,
    ]);
}

function fn_talario_vendor_cabinet_clear_class_moderation($product_ids, $delete = false)
{
    fn_talario_vendor_cabinet_ensure_moderation_storage();
    $product_ids = array_values(array_filter(array_map('intval', (array) $product_ids)));
    if (!$product_ids) {
        return;
    }
    if ($delete) {
        db_query('DELETE FROM ?:talario_class_moderation WHERE product_id IN (?n)', $product_ids);
    } else {
        db_query('UPDATE ?:talario_class_moderation SET active = ?s, updated_at = ?i WHERE product_id IN (?n)', 'N', TIME, $product_ids);
    }
}

/**
 * Prevents only the automatic approval request produced while Talario saves a
 * draft. The controller always removes this request-scoped guard in finally.
 */
function fn_talario_vendor_cabinet_vendor_data_premoderation_request_approval_for_products_pre(
    array &$product_ids,
    $update_product
) {
    $guard = \Tygh\Registry::ifGet('talario_vendor_cabinet.draft_guard', []);
    if (empty($guard['enabled']) || empty($product_ids)) {
        return;
    }

    $target_id = (int) ($guard['product_id'] ?? 0);
    if (!$target_id) {
        $target_id = (int) reset($product_ids);
        \Tygh\Registry::set('talario_vendor_cabinet.draft_guard.product_id', $target_id, true);
    }
    $company_id = (int) ($guard['company_id'] ?? 0);
    $product_ids = array_values(array_filter($product_ids, static function ($product_id) use ($target_id, $company_id) {
        $product_id = (int) $product_id;
        if ($product_id === $target_id) {
            return false;
        }
        $is_guarded_variation = (bool) db_get_field(
            'SELECT 1 FROM ?:product_variation_group_products target '
            . 'INNER JOIN ?:product_variation_group_products child ON child.group_id = target.group_id '
            . 'INNER JOIN ?:products p ON p.product_id = child.product_id AND p.company_id = ?i '
            . 'WHERE target.product_id = ?i AND child.product_id = ?i',
            $company_id,
            $target_id,
            $product_id
        );
        return !$is_guarded_variation;
    }));
}

/**
 * Makes approved class variations available together with their parent. They
 * remain Hidden throughout moderation, so no child can be opened beforehand.
 */
function fn_talario_vendor_cabinet_vendor_data_premoderation_approve_products_pre(
    array &$product_ids,
    $update_product
) {
    if (!$update_product || !$product_ids) {
        return;
    }
    fn_talario_vendor_cabinet_ensure_moderation_storage();
    $markers = db_get_hash_array(
        'SELECT product_id, company_id, group_id, variation_ids FROM ?:talario_class_moderation '
        . 'WHERE product_id IN (?n) AND active = ?s',
        'product_id',
        array_map('intval', $product_ids),
        'Y'
    );
    foreach ($markers as $marker) {
        $parent_id = (int) $marker['product_id'];
        $company_id = (int) $marker['company_id'];
        $group_id = (int) $marker['group_id'];
        $marked_ids = array_values(array_unique(array_map('intval', (array) json_decode($marker['variation_ids'], true))));
        $variation_ids = $group_id && $marked_ids ? db_get_fields(
            'SELECT p.product_id FROM ?:products p INNER JOIN ?:product_variation_group_products gp '
            . 'ON gp.product_id = p.product_id INNER JOIN ?:product_variation_group_products parent_gp '
            . 'ON parent_gp.group_id = gp.group_id AND parent_gp.product_id = ?i '
            . 'INNER JOIN ?:products parent ON parent.product_id = parent_gp.product_id AND parent.company_id = ?i '
            . 'WHERE gp.group_id = ?i AND p.company_id = ?i '
            . 'AND p.product_id IN (?n) AND p.product_id <> ?i',
            $parent_id,
            $company_id,
            $group_id,
            $company_id,
            $marked_ids,
            $parent_id
        ) : [];
        foreach (array_map('intval', $variation_ids) as $variation_id) {
            if (function_exists('fn_vendor_data_premoderation_update_premoderation')) {
                fn_vendor_data_premoderation_update_premoderation($variation_id, 'A');
            }
            db_query('UPDATE ?:products SET status = ?s WHERE product_id = ?i', 'R', $variation_id);
            $product_ids[] = $variation_id;
        }
        fn_talario_vendor_cabinet_clear_class_moderation([$parent_id]);
    }
    $product_ids = array_values(array_unique(array_map('intval', $product_ids)));
}

function fn_talario_vendor_cabinet_vendor_data_premoderation_disapprove_products_pre(
    array &$product_ids,
    $update_product,
    $reason
) {
    fn_talario_vendor_cabinet_clear_class_moderation($product_ids);
}

function fn_talario_vendor_cabinet_get_product_data_post(&$product_data, $auth, $preview, $lang_code)
{
    if (!$preview || empty($product_data['product_id'])) {
        return;
    }
    $product_id = (int) $product_data['product_id'];
    $revision_record = fn_talario_vendor_cabinet_get_preview_revision(
        (string) ($_REQUEST['talario_preview_token'] ?? ''),
        $product_id,
        (int) ($product_data['company_id'] ?? 0),
        (int) ($auth['user_id'] ?? 0)
    );
    if (!$revision_record) { return; }
    $revision = (array) ($revision_record['class_data'] ?? []);
    $product_data['product'] = trim((string) ($revision['product'] ?? $product_data['product']));
    $product_data['full_description'] = (string) ($revision['full_description'] ?? $product_data['full_description']);
    $product_data['short_description'] = trim((string) ($revision['catalog_age'] ?? $product_data['short_description']));
    $product_data['meta_keywords'] = trim((string) ($revision['meta_keywords'] ?? $product_data['meta_keywords']));
    $product_data['search_words'] = $product_data['meta_keywords'];
    $deleted_ids = array_values(array_unique(array_filter(array_map(
        'intval',
        (array) ($revision['delete_variations'] ?? [])
    ))));
    $variation_prices = [];
    $preview_existing_variations = [];
    foreach ((array) ($revision['variation_prices'] ?? []) as $variation_id => $variation_price) {
        $variation_id = (int) $variation_id;
        $normalized_price = str_replace(',', '.', (string) $variation_price);
        if (!is_numeric($normalized_price)) { continue; }
        $variation_name = db_get_field(
            'SELECT pd.product FROM ?:product_descriptions pd '
            . 'INNER JOIN ?:products child ON child.product_id = pd.product_id AND child.company_id = ?i '
            . 'INNER JOIN ?:product_variation_group_products child_gp ON child_gp.product_id = child.product_id '
            . 'INNER JOIN ?:product_variation_group_products parent_gp '
            . 'ON parent_gp.group_id = child_gp.group_id AND parent_gp.product_id = ?i '
            . 'WHERE pd.product_id = ?i AND pd.lang_code = ?s',
            (int) $product_data['company_id'], $product_id, $variation_id, $lang_code
        );
        if ($variation_name === false) { continue; }
        $is_deleted = in_array($variation_id, $deleted_ids, true);
        if (!$is_deleted) { $variation_prices[] = (float) $normalized_price; }
        $preview_existing_variations[] = [
            'product_id' => $variation_id,
            'product' => (string) $variation_name,
            'price' => (float) $normalized_price,
            'deleted' => $is_deleted,
        ];
    }
    $preview_variations = [];
    foreach ((array) ($revision['new_variations'] ?? []) as $variation) {
        $raw_price = str_replace(',', '.', (string) ($variation['price'] ?? ''));
        if (!is_numeric($raw_price)) { continue; }
        $variation_prices[] = (float) $raw_price;
        $variant_ids = array_map('intval', (array) ($variation['variants'] ?? []));
        $preview_variations[] = [
            'variants' => $variant_ids,
            'variant_names' => $variant_ids ? db_get_fields(
                'SELECT variant FROM ?:product_feature_variant_descriptions '
                . 'WHERE variant_id IN (?n) AND lang_code = ?s ORDER BY FIELD(variant_id, ?n)',
                $variant_ids, $lang_code, $variant_ids
            ) : [],
            'price' => (float) $raw_price,
        ];
    }
    $product_data['price'] = $variation_prices
        ? min($variation_prices)
        : (float) str_replace(',', '.', (string) ($revision['price'] ?? $product_data['price']));
    // Storefront extensions can render these unsaved rows without creating
    // Product Variation records or changing the public product.
    $product_data['talario_preview_variations'] = $preview_variations;
    $product_data['talario_preview_existing_variations'] = $preview_existing_variations;
    $product_data['talario_preview_deleted_variation_ids'] = $deleted_ids;
    if (!empty($revision['category_id'])) {
        $product_data['main_category'] = (int) $revision['category_id'];
        $product_data['category_ids'] = [(int) $revision['category_id']];
    }
    if (!empty($revision['location_id'])) {
        $address = db_get_field(
            'SELECT address FROM ?:talario_locations WHERE location_id = ?i AND company_id = ?i',
            $revision['location_id'],
            $product_data['company_id']
        );
        if ($address !== false) { $product_data['address'] = $address; }
    }

    $product_request = (array) ($revision_record['product_data'] ?? []);
    $requested_removed_pair_ids = array_values(array_unique(array_filter(array_map(
        'intval',
        (array) ($product_request['removed_image_pair_ids'] ?? [])
    ))));
    $owned_pair_ids = array_map('intval', db_get_fields(
        'SELECT il.pair_id FROM ?:images_links il '
        . 'INNER JOIN ?:products p ON p.product_id = il.object_id AND p.company_id = ?i '
        . 'WHERE il.object_id = ?i AND il.object_type = ?s',
        (int) $product_data['company_id'], $product_id, 'product'
    ));
    $removed_pair_ids = array_values(array_intersect($requested_removed_pair_ids, $owned_pair_ids));
    if (!empty($product_data['main_pair']['pair_id'])
        && in_array((int) $product_data['main_pair']['pair_id'], $removed_pair_ids, true)) {
        $product_data['main_pair'] = [];
    }
    $product_data['image_pairs'] = array_values(array_filter(
        (array) ($product_data['image_pairs'] ?? []),
        static function (array $pair) use ($removed_pair_ids) {
            return !in_array((int) ($pair['pair_id'] ?? 0), $removed_pair_ids, true);
        }
    ));

    foreach ((array) ($revision_record['image_request'] ?? []) as $request_key => $image_rows) {
        $is_image_data = substr((string) $request_key, -11) === '_image_data';
        $is_legacy_type_data = strpos((string) $request_key, 'type_') === 0
            && substr((string) $request_key, -5) === '_data';
        if (!$is_image_data && !$is_legacy_type_data) {
            continue;
        }
        foreach ((array) $image_rows as $image_row) {
            if (!is_array($image_row) || empty($image_row['pair_id']) || empty($image_row['type'])) { continue; }
            $pair_id = (int) $image_row['pair_id'];
            if (!in_array($pair_id, $owned_pair_ids, true)) { continue; }
            $pair = null;
            if ((int) ($product_data['main_pair']['pair_id'] ?? 0) === $pair_id) {
                $pair = $product_data['main_pair'];
                $product_data['main_pair'] = [];
            } else {
                foreach ($product_data['image_pairs'] as $pair_key => $candidate) {
                    if ((int) ($candidate['pair_id'] ?? 0) === $pair_id) {
                        $pair = $candidate;
                        unset($product_data['image_pairs'][$pair_key]);
                        break;
                    }
                }
            }
            if (!$pair) { continue; }
            $pair['type'] = $image_row['type'] === 'M' ? 'M' : 'A';
            if (isset($image_row['position'])) { $pair['position'] = (int) $image_row['position']; }
            if (array_key_exists('detailed_alt', $image_row)) {
                if (!empty($pair['detailed'])) { $pair['detailed']['alt'] = (string) $image_row['detailed_alt']; }
                if (!empty($pair['icon'])) { $pair['icon']['alt'] = (string) $image_row['detailed_alt']; }
            }
            if ($pair['type'] === 'M') {
                if ($product_data['main_pair']) {
                    $product_data['main_pair']['type'] = 'A';
                    $product_data['image_pairs'][] = $product_data['main_pair'];
                }
                $product_data['main_pair'] = $pair;
            } else {
                $product_data['image_pairs'][] = $pair;
            }
        }
    }
    $product_data['image_pairs'] = array_values($product_data['image_pairs']);
    usort($product_data['image_pairs'], static function (array $left, array $right) {
        return (int) ($left['position'] ?? 0) <=> (int) ($right['position'] ?? 0);
    });

    $preview_pair_id = 1000000000;
    foreach ((array) ($revision_record['image_request'] ?? []) as $request_key => $paths) {
        if (strpos((string) $request_key, 'file_') !== 0 || substr((string) $request_key, -9) !== '_detailed') {
            continue;
        }
        $image_data_key = substr((string) $request_key, 5, -9) . '_data';
        $image_data_rows = (array) (($revision_record['image_request'][$image_data_key] ?? []));
        foreach ((array) $paths as $image_index => $path) {
            if (!is_string($path) || $path === '') { continue; }
            $image_data = is_array($image_data_rows[$image_index] ?? null)
                ? $image_data_rows[$image_index]
                : [];
            $image_url = filter_var($path, FILTER_VALIDATE_URL)
                ? $path
                : fn_url('image.custom_image?type=T&image=' . rawurlencode(fn_basename($path)), 'C');
            $pair = [
                'pair_id' => ++$preview_pair_id,
                'image_id' => 0,
                'detailed_id' => $preview_pair_id,
                'icon' => [],
                'detailed' => ['image_path' => $image_url, 'alt' => (string) ($image_data['detailed_alt'] ?? '')],
                'position' => (int) ($image_data['position'] ?? 0),
                'type' => ($image_data['type'] ?? '') === 'M'
                    || strpos($request_key, 'product_main_image') !== false ? 'M' : 'A',
            ];
            if ($pair['type'] === 'M') {
                $product_data['main_pair'] = $pair;
            } else {
                $product_data['image_pairs'][] = $pair;
            }
        }
    }
    usort($product_data['image_pairs'], static function (array $left, array $right) {
        return (int) ($left['position'] ?? 0) <=> (int) ($right['position'] ?? 0);
    });
}
