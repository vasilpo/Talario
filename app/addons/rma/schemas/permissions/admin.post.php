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

$schema['rma'] = [
    'permissions' => 'manage_rma',
    'modes'       => [
        'returns'          => [
            'permissions' => 'view_rma',
        ],
        'details'          => [
            'permissions' => 'view_rma',
        ],
        'update_details'   => [
            'permissions' => 'manage_rma',
        ],
        'decline_products' => [
            'permissions' => 'manage_rma',
        ],
        'accept_products'  => [
            'permissions' => 'manage_rma',
        ],
        'confirmation'     => [
            'permissions' => 'manage_rma',
        ],
    ],
];
$schema['tools']['modes']['update_status']['param_permissions']['table']['rma_properties'] = 'manage_rma';

return $schema;
