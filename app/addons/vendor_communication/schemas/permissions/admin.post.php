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

use Tygh\Enum\Addons\VendorCommunication\CommunicationTypes;

defined('BOOTSTRAP') or die('Access denied');

$schema['vendor_communication'] = [
    'modes' => [
        'delete_thread' => [
            'permissions' => 'manage_vendor_communication',
        ],
        'm_delete_thread' => [
            'permissions' => 'manage_vendor_communication',
        ],
        'create_thread' => [
            'param_permissions' => [
                'communication_type' => [
                    CommunicationTypes::VENDOR_TO_ADMIN => 'manage_admin_communication',
                    CommunicationTypes::VENDOR_TO_CUSTOMER => 'manage_vendor_communication',
                ],
            ],
        ],
        'threads' => [
            'param_permissions' => [
                'communication_type' => [
                    CommunicationTypes::VENDOR_TO_ADMIN => 'view_admin_communication',
                    CommunicationTypes::VENDOR_TO_CUSTOMER => 'view_vendor_communication',
                ],
            ],
        ],
        'post_message' => [
            'param_permissions' => [
                'communication_type' => [
                    CommunicationTypes::VENDOR_TO_ADMIN  => 'manage_admin_communication',
                    CommunicationTypes::VENDOR_TO_CUSTOMER => 'manage_vendor_communication',
                ],
            ],
        ],
        'view' => [
            'param_permissions' => [
                'communication_type' => [
                    CommunicationTypes::VENDOR_TO_ADMIN  => 'view_admin_communication',
                    CommunicationTypes::VENDOR_TO_CUSTOMER => 'view_vendor_communication',
                ],
            ],
        ],
        'm_post_message' => [
            'param_permissions' => [
                'communication_type' => [
                    CommunicationTypes::VENDOR_TO_ADMIN    => 'manage_admin_communication',
                    CommunicationTypes::VENDOR_TO_CUSTOMER => 'manage_vendor_communication',
                ],
            ],
        ],
        // for vendor_privileges add-on
        'view_customer_order_thread' => [
            'permissions' => 'view_order_communication',
        ],
        'manage_customer_order_thread' => [
            'permissions' => 'manage_order_communication',
        ],
    ],
];

return $schema;
