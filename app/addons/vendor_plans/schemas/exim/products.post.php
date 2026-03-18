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

use Tygh\Registry;

if (fn_allowed_for('MULTIVENDOR') && Registry::get('runtime.company_id')) {
    $schema['import_process_data']['vendor_plans_import_check_object_id'] = array(
        'function' => 'fn_vendor_plans_import_check_object_id',
        'args' => array('$primary_object_id', '$processed_data', '$skip_record'),
        'import_only' => true,
    );
}

/** @var array{import_process_data: array<array<string|bool|array<string>>>} $schema */
$schema['import_process_data']['vendor_plans_import_skip_products_with_unavailable_categories'] = [
    'function'    => 'fn_vendor_plans_import_skip_products_with_unavailable_categories',
    'args'        => [
        '%company_id',
        '%company',
        '%Category',
        '%Secondary categories',
        '@category_delimiter',
        '$processed_data',
        '%lang_code',
        '$skip_record',
        '$primary_object_id',
        '$object'
    ],
    'import_only' => true,
];

return $schema;

