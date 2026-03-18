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

defined('BOOTSTRAP') or die('Access denied');

include_once __DIR__ . '/products.functions.php';

/** @var array $schema */
$schema['export_fields']['Payment object (tag 1212)'] = [
    'db_field'    => 'fiscal_data_1212',
    'process_put' => ['fn_rus_taxes_set_payment_object', '#row', 'fiscal_data_1212'],
];

$schema['export_fields']['Is fur ware'] = [
    'db_field'    => 'mark_code_type',
    'process_put' => ['fn_rus_taxes_set_mark_code_type', '#row', 'mark_code_type'],
];

return $schema;
