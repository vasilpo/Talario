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

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

$schema = [
    'usergroups'             => false,
    'companies.manage'       => false,
    'companies.update'       => false,
    'companies.add'          => false,
    'companies.merge'        => false,
    'storefronts'            => false,
    'taxes'                  => false,
    'countries'              => false,
    'states'                 => false,
    'datakeeper'             => false,
    'destinations'           => false,
    'statuses'               => false,
    'profile_fields'         => false,
    'currencies'             => false,
    'languages.manage'       => false,
    'languages.install_list' => false,
    'upgrade_center'         => false,
    'tools.view_changes'     => false,
    'settings_wizard.view'   => false,
    'email_templates'        => false,
    'documents'              => false,
    'storage.cdn'            => false,
    'notification_settings'  => false,

    'settings.manage' => [
        'allow'    => true,
        'variants' => [
            'store'          => [
                'allow'  => false,
                'params' => ['section_id' => 'Stores']
            ],
            'upgrade_center' => [
                'allow'  => false,
                'params' => ['section_id' => 'Upgrade_center']
            ]
        ]
    ],

    'exim' => [
        'allow'    => true,
        'variants' => [
            'states' => [
                'allow'  => false,
                'params' => ['section' => 'states']
            ]
        ]
    ]
];

if (Registry::ifGet('settings.Appearance.email_templates', 'new') === 'old') {
    $schema['statuses'] = true;
}

return $schema;
