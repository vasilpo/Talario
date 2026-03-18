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

$schema = fn_get_schema('exim', 'products');

if (fn_allowed_for('ULTIMATE')) {
    $schema['export_fields']['Price'] = array(
        'table' => 'product_prices',
        'db_field' => 'price',
        'process_get' => array(
            'fn_data_feeds_export_price',
            '#key', '#this', '@company_id', '@price_dec_sign_delimiter'
        ),
    );

    $schema['export_fields']['Category'] = array(
        'process_get' => array('fn_data_feeds_get_product_categories', '#key', '@category_delimiter', '@company_id', '#lang_code'),
        'multilang' => true,
        'linked' => false,
    );
}

$schema['is_data_feeds'] = true;

return $schema;
