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

$schema = array(
    'products' => array (
        'admin_dispatch' => 'products.update',
        'customer_dispatch' => 'products.view',
        'key' => 'product_id',
        'picker' => 'pickers/products/picker.tpl',
        'picker_params' => array (
            'type' => 'links',
        ),
    ),
    'pages' => array (
        'admin_dispatch' => 'pages.update',
        'customer_dispatch' => 'pages.view',
        'key' => 'page_id',
        'picker' => 'pickers/pages/picker.tpl',
        'picker_params' => array (
            'multiple' => true,
            'status' => 'A',
        )
    ),
    'categories' => array (
        'key' => 'category_id',
        'admin_dispatch' => 'categories.update',
        'customer_dispatch' => 'categories.view',
        'picker' => 'pickers/categories/picker.tpl',
        'picker_params' => array (
            'view_mode' => 'blocks',
            'multiple' => true,
            'use_keys' => 'N',
            'status' => 'A',
        ),
    ),
);

if (fn_allowed_for('MULTIVENDOR')) {
    $schema['companies'] = array(
        'admin_dispatch' => 'companies.update',
        'customer_dispatch' => 'companies.view',
        'key' => 'company_id',
        'picker' => 'pickers/companies/picker.tpl',
        'picker_params' => array(
            'type' => 'links',
            'multiple' => true
        ),
    );
}

return $schema;
