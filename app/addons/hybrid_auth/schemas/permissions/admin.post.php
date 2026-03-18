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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$schema['hybrid_auth'] = [
    'modes' => [
        'delete_provider'   => [
            'permissions' => 'manage_providers'
        ],
        'm_delete_provider' => [
            'permissions' => 'manage_providers'
        ],
        'm_update_statuses' => [
            'permissions' => 'manage_providers'
        ],
    ],
    'permissions' => ['GET' => 'view_providers', 'POST' => 'manage_providers']
];

$schema['tools']['modes']['update_status']['param_permissions']['table']['hybrid_auth_providers'] = 'manage_providers';
$schema['tools']['modes']['update_position']['param_permissions']['table']['hybrid_auth_providers'] = 'manage_providers';

return $schema;
