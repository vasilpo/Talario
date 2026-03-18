<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

/**
 * @var array $schema
 */

$schema['export_fields']['Unit name'] = [
    'table'    => 'product_descriptions',
    'db_field' => 'unit_name',
    'multilang'   => true,
    'process_get' => ['fn_export_product_descr', '#key', '#this', '#lang_code', 'unit_name', true],
    'process_put' => ['fn_import_product_descr', '#this', '#key', 'unit_name', '#new']
];

$schema['export_fields']['Units in product'] = [
    'db_field' => 'units_in_product'
];

$schema['export_fields']['Show price per X units'] = [
    'db_field' => 'show_price_per_x_units'
];

if (fn_allowed_for('ULTIMATE')) {
    $schema['export_fields']['Unit name']['process_put'] = ['fn_import_product_descr', '#this', '#key', 'unit_name', '#new', '#row', true];
}

return $schema;
