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

$schema['store_locator'] = [
    'permissions' => ['GET' => 'view_store_locator', 'POST' => 'manage_store_locator'],
    'modes' => [
        'delete'            => [
            'permissions' => 'manage_store_locator'
        ],
        'm_delete'          => [
            'permissions' => 'manage_store_locator'
        ],
        'm_update_statuses' => [
            'permissions' => 'manage_store_locator'
        ],
    ],
];
$schema['tools']['modes']['update_status']['param_permissions']['table']['store_locations'] = 'manage_store_locator';

$schema['exim']['modes']['export']['param_permissions']['section']['pickup'] = 'view_store_locator';
$schema['exim']['modes']['import']['param_permissions']['section']['pickup'] = 'manage_store_locator';

return $schema;
