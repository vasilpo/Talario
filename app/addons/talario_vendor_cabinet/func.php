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
    $names = [
        'Спорт',
        'Единоборства',
        'Творчество',
        'Танцы',
        'Раннее развитие',
        'Школьные предметы',
        'Иностранные языки',
        'Интеллектуальные занятия',
        'Программирование',
        'Робототехника',
        'Детские лагеря',
        'Летние секции',
        'Музыка',
    ];

    $rows = db_get_array(
        'SELECT c.category_id, cd.category FROM ?:categories AS c '
        . 'INNER JOIN ?:category_descriptions AS cd ON cd.category_id = c.category_id AND cd.lang_code = ?s '
        . 'WHERE cd.category IN (?a) AND c.status = ?s AND c.parent_id = 0',
        DESCR_SL,
        $names,
        'A'
    );

    $by_name = [];
    foreach ($rows as $row) {
        $by_name[(string) $row['category']] = $row;
    }

    $result = [];
    foreach ($names as $name) {
        if (isset($by_name[$name])) {
            $result[] = $by_name[$name];
        }
    }

    return $result;
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
