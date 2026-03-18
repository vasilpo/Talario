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

include_once __DIR__ . '/products.functions.php';

/** @var array<string, array<string,string>> $schema */

$schema['import_process_data']['prepare_rma_fields'] = [
    'function'    => 'fn_import_prepare_rma_data',
    'args'        => ['$primary_object_id', '$object'],
    'import_only' => true,
];

$schema['export_fields']['Returnable'] = [
    'db_field' => 'is_returnable'
];
$schema['export_fields']['Return period (days)'] = [
    'db_field' => 'return_period'
];

return $schema;
