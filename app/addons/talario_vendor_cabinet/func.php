<?php

defined('BOOTSTRAP') or die('Access denied');

/**
 * Keeps the public center name/description separate from the vendor legal name.
 * CREATE TABLE IF NOT EXISTS makes the storage available on existing installations
 * without requiring a reinstall of the add-on.
 */
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
        . 'updated_at int unsigned NOT NULL DEFAULT 0,'
        . 'PRIMARY KEY (company_id)'
        . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );

    $ready = true;
}

function fn_talario_vendor_cabinet_get_center($company_id)
{
    $company_id = (int) $company_id;
    if (!$company_id) {
        return ['company_id' => 0, 'name' => '', 'description' => ''];
    }

    fn_talario_vendor_cabinet_ensure_center_storage();

    $center = db_get_row(
        'SELECT company_id, name, description, updated_at FROM ?:talario_vendor_centers WHERE company_id = ?i',
        $company_id
    );

    if (!$center) {
        return ['company_id' => $company_id, 'name' => '', 'description' => '', 'updated_at' => 0];
    }

    return $center;
}

function fn_talario_vendor_cabinet_get_center_name($company_id)
{
    $center = fn_talario_vendor_cabinet_get_center($company_id);
    return (string) ($center['name'] ?? '');
}

function fn_talario_vendor_cabinet_update_center($company_id, $name, $description = '')
{
    $company_id = (int) $company_id;
    if (!$company_id) {
        return false;
    }

    fn_talario_vendor_cabinet_ensure_center_storage();

    $data = [
        'company_id' => $company_id,
        'name' => trim((string) $name),
        'description' => trim((string) $description),
        'updated_at' => TIME,
    ];

    db_query('REPLACE INTO ?:talario_vendor_centers ?e', $data);

    return true;
}
