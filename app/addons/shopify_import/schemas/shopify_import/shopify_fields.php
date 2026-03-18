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

return [
    'simple_mapping_fields'  => [
        'product' => 'Title',
        'full_description' => 'Body (HTML)',
        'price' => 'Variant Price',
        'Images' => 'Image Src',
        'amount' => 'Variant Inventory Qty',
        'page_title' => 'SEO Title',
        'meta_description' => 'SEO Description'
    ],
    'main_product_fields'    => [
        'Handle',
        'Title',
        'Body (HTML)',
        'Vendor'
    ],
    'variation_empty_fields' => [
        'Title',
        'Body (HTML)'
    ],
    'option_name_columns'    => [
        'Option1 Name',
        'Option2 Name',
        'Option3 Name'
    ]
];
