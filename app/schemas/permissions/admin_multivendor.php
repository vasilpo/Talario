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

/** @var array $schema */
$schema['companies'] = [
    'modes' => [
        'manage' => [
            'permissions' => ['GET' => 'view_vendors', 'POST' => 'manage_vendors'],
        ],
        'add' => [
            'permissions' => 'manage_vendors',
        ],
        'invite' => [
            'permissions' => 'manage_vendors',
        ],
        'invitations' => [
            'permissions' => 'manage_vendors',
        ],
        'm_delete_invitations' => [
            'permissions' => 'manage_vendors',
        ],
        'delete_invitation' => [
            'permissions' => 'manage_vendors',
        ],
        'update' => [
            'permissions' => ['GET' => 'view_vendors', 'POST' => 'manage_vendors'],
        ],
        'get_companies_list' => [
            'permissions' => 'view_vendors',
        ],
        'payouts_m_delete' => [
            'permissions' => 'manage_payouts',
        ],
        'payouts_add' => [
            'permissions' => 'manage_payouts',
        ],
        'payout_delete' => [
            'permissions' => 'manage_payouts',
        ],
        'update_payout_comments' => [
            'permissions' => 'manage_payouts',
        ],
        'balance' => [
            'permissions' => 'view_payouts',
        ],
        'payouts' => [
            'permissions' => 'manage_payouts',
        ],
        'm_delete_payouts' => [
            'permissions' => 'manage_payouts',
        ],
        'merge' => [
            'permissions' => 'merge_vendors',
            'condition' => [
                'operator' => 'and',
                'function' => ['fn_check_current_user_access', 'manage_vendors'],
            ],
        ],
    ],
    'permissions' => 'manage_vendors',
];

$schema['profiles']['modes']['login_as_vendor'] = [
    'permissions' => 'manage_vendors',
    'condition'   => [
        'operator' => 'or',
        'function' => ['fn_check_permission_act_as_user'],
    ]
];

$schema['block_manager']['modes']['update_custom_block'] = [
    'permissions' => false,
    'condition'   => [
        'operator' => 'or',
        'function' => ['fn_vendor_customization_permissions'],
    ],
];

$schema['exim']['modes']['export']['param_permissions']['section']['vendors'] = 'view_vendors';
$schema['exim']['modes']['import']['param_permissions']['section']['vendors'] = 'manage_vendors';

$schema['customization']['modes']['update_mode']['condition'] = [
    'operator' => 'or',
    'function' => ['fn_vendor_customization_permissions']
];

$schema['shippings']['modes']['apply_to_vendors'] = [
    'permissions' => 'manage_vendors',
];

$storefront_admin_permission =  [
    'permissions' => true,
    'condition'   => [
        'operator' => 'and',
        'function' => ['fn_check_permission_storefronts'],
    ]
];

$schema['root']['statuses'] = $storefront_admin_permission;
$schema['root']['statuses']['modes']['manage'] = $storefront_admin_permission;
$schema['root']['statuses']['modes']['update'] = $storefront_admin_permission;
$schema['root']['statuses']['modes']['delete'] = $storefront_admin_permission;

$schema['root']['profile_fields'] = $storefront_admin_permission;

$schema['root']['languages']['modes']['update_status'] = $storefront_admin_permission;
$schema['root']['languages']['modes']['update'] = $storefront_admin_permission;
$schema['root']['languages']['modes']['install'] = $storefront_admin_permission;
$schema['root']['languages']['modes']['delete_language'] = $storefront_admin_permission;
$schema['root']['languages']['modes']['m_delete'] = $storefront_admin_permission;
$schema['root']['languages']['modes']['translations'] = $storefront_admin_permission;
$schema['root']['languages']['modes']['delete_variable'] = $storefront_admin_permission;
$schema['root']['languages']['modes']['m_delete_variables'] = $storefront_admin_permission;
$schema['root']['languages']['modes']['update_translation'] = $storefront_admin_permission;
$schema['root']['languages']['modes']['m_update_variables'] = $storefront_admin_permission;
$schema['root']['languages']['modes']['export_language'] = $storefront_admin_permission;
$schema['root']['languages']['modes']['clone_language'] = $storefront_admin_permission;

$schema['root']['currencies']['modes']['update'] = $storefront_admin_permission;
$schema['root']['currencies']['modes']['delete'] = $storefront_admin_permission;
$schema['root']['currencies']['modes']['update_status'] = $storefront_admin_permission;

return $schema;
