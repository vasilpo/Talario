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

require_once __DIR__ . '/products.functions.php';

/**
 * @var array<string, array> $schema
 */
$schema['import_process_data']['load_initial_product_state'] = [
    'function'    => 'fn_exim_vendor_data_premoderation_load_initial_product_state',
    'args'        => ['$primary_object_id', '$object'],
    'import_only' => true,
];

$schema['post_processing']['set_approval_status'] = [
    'function'    => 'fn_exim_vendor_data_premoderation_set_approval_status',
    'args'        => ['$primary_object_ids'],
    'import_only' => true,
];

return $schema;
