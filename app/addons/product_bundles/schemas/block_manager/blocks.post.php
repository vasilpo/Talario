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

/** @var array $schema */

$schema['product_bundles'] = [
    'show_on_locations' => ['product_tabs'],
    'templates'         => 'addons/product_bundles/blocks/product_bundles.tpl',
    'cache'             => [
        'request_handlers'  => ['product_id'],
        'update_handlers'   => [
            'products',
            'product_descriptions',
            'product_tabs',
            'product_tabs_descriptions',
            'product_prices',
        ],
    ],
];

return $schema;
