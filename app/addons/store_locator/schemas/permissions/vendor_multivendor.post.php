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

$schema['controllers']['store_locator'] = [
    'modes'       => [
        'add'               => [
            'permissions' => 'manage_store_locator',
        ],
        'update'            => [
            'permissions' => 'manage_store_locator',
        ],
        'm_update'            => [
            'permissions' => 'manage_store_locator',
        ],
        'delete'            => [
            'permissions' => 'manage_store_locator',
        ],
        'm_delete'          => [
            'permissions' => 'manage_store_locator',
        ],
        'manage'            => [
            'permissions' => 'view_store_locator',
        ],
        'm_update_statuses' => [
            'permissions' => 'manage_store_locator',
        ],
        'm_update_pickup'   => [
            'permissions' => 'manage_store_locator',
        ],
    ],
    'permissions' => false,
];

$schema['controllers']['tools']['modes']['update_status']['param_permissions']['table']['store_locations'] = ['permissions' => 'manage_store_locator'];

return $schema;
