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
    'permissions' => ['GET' => 'view_product_bundles', 'POST' => 'manage_product_bundles'],
    'modes' => [
        'delete'            => [
            'permissions' => 'manage_product_bundles'
        ],
        'm_delete'          => [
            'permissions' => 'manage_product_bundles'
        ],
        'm_update_statuses' => [
            'permissions' => 'manage_product_bundles'
        ],
    ],
];
$schema['tools']['modes']['update_status']['param_permissions']['table']['product_bundles'] = 'manage_product_bundles';

return $schema;
