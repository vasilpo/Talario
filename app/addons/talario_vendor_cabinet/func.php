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
        . 'WHERE cd.category IN (?a) AND c.status = ?s',
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
