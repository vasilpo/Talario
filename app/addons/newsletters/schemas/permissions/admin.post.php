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

$schema['newsletters'] = [
    'modes' => [
        'delete'                      => [
            'permissions' => 'manage_newsletters'
        ],
        'm_delete'                    => [
            'permissions' => 'manage_newsletters'
        ],
        'delete_campaign'             => [
            'permissions' => 'manage_newsletters'
        ],
        'm_delete_campaigns'          => [
            'permissions' => 'manage_newsletters'
        ],
        'm_update_statuses_campaigns' => [
            'permissions' => 'manage_newsletters'
        ],
        'm_update_statuses'           => [
            'permissions' => 'manage_newsletters'
        ],
    ],
    'permissions' => ['GET' => 'view_newsletters', 'POST' => 'manage_newsletters']
];
$schema['subscribers'] = [
    'modes' => [
        'delete'       => [
            'permissions' => 'manage_newsletters'
        ],
        'm_delete'     => [
            'permissions' => 'manage_newsletters'
        ],
        'export_range' => [
            'permissions' => 'exim_access'
        ],
    ],
    'permissions' => ['GET' => 'view_newsletters', 'POST' => 'manage_newsletters']
];
$schema['campaigns'] = [
    'permissions' => ['GET' => 'view_newsletters', 'POST' => 'manage_newsletters']
];
$schema['mailing_lists'] = [
    'modes' => [
        'delete'            => [
            'permissions' => 'manage_newsletters'
        ],
        'm_delete'          => [
            'permissions' => 'manage_newsletters'
        ],
        'm_set_display'     => [
            'permissions' => 'manage_newsletters'
        ],
        'm_update_statuses' => [
            'permissions' => 'manage_newsletters'
        ],
    ],
    'permissions' => ['GET' => 'view_newsletters', 'POST' => 'manage_newsletters']
];
$schema['tools']['modes']['update_status']['param_permissions']['table']['newsletter_campaigns'] = 'manage_newsletters';
$schema['tools']['modes']['update_status']['param_permissions']['table']['mailing_lists'] = 'manage_newsletters';
$schema['tools']['modes']['update_status']['param_permissions']['table']['newsletters'] = 'manage_newsletters';

$schema['exim']['modes']['export']['param_permissions']['section']['subscribers'] = 'view_newsletters';
$schema['exim']['modes']['import']['param_permissions']['section']['subscribers'] = 'manage_newsletters';

return $schema;
