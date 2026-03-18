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

$schema['controllers']['vendor_communication'] = [
    'modes' => [
        'delete_thread' => [
            'permissions' => false,
        ],
        'm_delete_thread' => [
            'permissions' => false,
        ],
        'create_thread' => [
            'param_permissions' => [
                'communication_type' => [
                    CommunicationTypes::VENDOR_TO_ADMIN => true,
                    CommunicationTypes::VENDOR_TO_CUSTOMER => true,
                ],
            ],
            'default_permissions' => false,
        ],
        'threads' => [
            'param_permissions' => [
                'communication_type' => [
                    CommunicationTypes::VENDOR_TO_ADMIN => true,
                    CommunicationTypes::VENDOR_TO_CUSTOMER => true,
                ],
            ],
            'default_permissions' => false,
        ],
        'post_message' => [
            'permissions' => true
        ],
        'view' => [
            'param_permissions' => [
                'communication_type' => [
                    CommunicationTypes::VENDOR_TO_ADMIN  => true,
                    CommunicationTypes::VENDOR_TO_CUSTOMER => true,
                ],
            ],
            'default_permissions' => false,
        ],
        'm_post_message' => [
            'permissions' => false,
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
