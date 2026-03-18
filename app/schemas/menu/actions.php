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

/*
    Use this structure to show actions buttons:
    'controller.mode' => [
        'action_button_name' => [
            'href' => 'products.manage',
            'text' => __('action_button_langvar')

        ],
    ]

    or just specify controller (buttons will show on all controller modes):
    'controller' => [
        'action_button_name' => [
            'href' => 'products.manage',
            'text' => __('action_button_langvar')
        ],
    ]
*/

/**
 * @var array $schema
 */
$schema = [
    'administration.view' => [
        'clear_cache' => [
            'class' => 'cm-post',
            'href' => 'storage.clear_cache?redirect_url=' . urlencode(Registry::get('config.current_url')),
            'text' => __('actions.clear_cache'),
            'position' => 100
        ]
    ],
    'datakeeper.manage' => [
        'files' => [
            'href' => 'file_editor.manage',
            'text' => __('actions.files'),
            'position' => 100
        ],
        'file_changes_detector' => [
            'href' => 'tools.view_changes?check_types=C,D',
            'text' => __('actions.file_changes_detector'),
            'position' => 200
        ],
        'logs' => [
            'href' => 'logs.manage',
            'text' => __('actions.logs'),
            'position' => 400
        ],
        'upload_backup' => [
            'type' => 'dialog',
            'text' => __('actions.upload_backup'),
            'target_id' => 'content_upload_backup',
            'form' => 'upload_backup_form',
            'class' => 'cm-dialog-auto-size',
            'position' => 500
        ],
        'datakeeper_optimize' => [
            'href' => 'datakeeper.optimize',
            'text' => __('actions.datakeeper_optimize'),
            'class' => 'cm-post cm-comet cm-ajax',
            'data' => [
                'target' => 'elm_sidebar'
            ],
            'position' => 600
        ]
    ],
    'languages.translations' => [
        'manage_languages' => [
            'href' => 'languages.manage',
            'text' => __('actions.languages'),
            'position' => 100
        ],
        'export' => [
            'href' => 'exim.export?section=translations',
            'text' => __('actions.export'),
            'position' => 300
        ],
        'live_editor' => [
            'href' => fn_url('customization.update_mode?type=live_editor&status=enable'),
            'text' => __('actions.live_editor'),
            'target' => '_blank',
            'method' => 'POST',
            'position' => 400
        ],
        'import' => [
            'href'     => 'exim.import?section=translations',
            'text'     => __('actions.import'),
            'position' => 200
        ]
    ],
    'orders.manage' => [
        'export' => [
            'href' => 'exim.export?section=orders',
            'text' => __('actions.export'),
            'position' => 200
        ],
        'view_purchased_products' => [ // For global orders search results page
            'class' => 'cm-process-items cm-submit',
            'wrapper_class' => 'bulkedit-action--legacy hide',
            'position' => 300,
            'data' => [
                'data-ca-target-form' => 'orders_list_form',
                'data-ca-dispatch' => 'dispatch[orders.products_range]'
            ]
        ],
        'export_selected' => [ // For global orders search results page
            'class' => 'cm-process-items cm-submit',
            'wrapper_class' => 'bulkedit-action--legacy hide',
            'position' => 400,
            'data' => [
                'data-ca-target-form' => 'orders_list_form',
                'data-ca-dispatch' => 'dispatch[orders.export_range]'
            ]
        ],
        'delete_selected' => [ // For global orders search results page
            'class' => 'cm-process-items cm-submit',
            'wrapper_class' => 'bulkedit-action--legacy hide',
            'position' => 500,
            'data' => [
                'data-ca-target-form' => 'orders_list_form',
                'data-ca-dispatch' => 'dispatch[orders.m_delete]'
            ]
        ],
        'import' => [
            'href'     => 'exim.import?section=orders',
            'text'     => __('actions.import'),
            'position' => 100
        ],
    ],
    'product_features.manage' => [
        'feature_groups' => [
            'href' => 'product_features.groups',
            'text' => __('actions.feature_groups'),
            'position' => 100
        ],
        'export' => [
            'href' => 'exim.export?section=features',
            'text' => __('actions.export'),
            'position' => 300
        ],
        'import' => [
            'href'     => 'exim.import?section=features',
            'text'     => __('actions.import'),
            'position' => 200
        ]
    ],
    'products.manage' => [
        'bulk_product_addition' => [
            'href' => 'products.m_add',
            'text' => __('actions.bulk_product_addition'),
            'position' => 100
        ],
        'import' => [
            'href' => 'exim.import?section=products',
            'text' => __('actions.import'),
            'position' => 200
        ],
        'export' => [
            'href' => 'exim.export?section=products',
            'text' => __('actions.export'),
            'position' => 300
        ],
        'export_found_products' => [ // If the products are available
            'href' => 'products.export_found.master',
            'text' => __('actions.export_found'),
            'position' => 400
        ],
        'global_update' => [
            'href' => 'products.global_update',
            'text' => __('actions.global_update'),
            'position' => 500
        ],
        'product_subscriptions' => [
            'href' => 'products.p_subscr',
            'text' => __('actions.product_subscriptions'),
            'position' => 600
        ],
        'clone_selected' => [ // For global products search results page
            'class' => 'cm-process-items cm-submit',
            'wrapper_class' => 'bulkedit-action--legacy hide',
            'position' => 700,
            'data' => [
                'data-ca-target-form' => 'manage_products_form',
                'data-ca-dispatch' => 'dispatch[products.m_clone]'
            ]
        ],
        'export_selected' => [ // For global products search results page
            'class' => 'cm-process-items cm-submit',
            'wrapper_class' => 'bulkedit-action--legacy hide',
            'position' => 800,
            'data' => [
                'data-ca-target-form' => 'manage_products_form',
                'data-ca-dispatch' => 'dispatch[products.export_range]'
            ]
        ],
        'delete_selected' => [ // For global products search results page
            'class' => 'cm-process-items cm-submit',
            'wrapper_class' => 'bulkedit-action--legacy hide',
            'position' => 900,
            'data' => [
                'data-ca-target-form' => 'manage_products_form',
                'data-ca-dispatch' => 'dispatch[products.m_delete]'
            ]
        ]
    ],
    'profiles.manage' => [
        'import' => [
            'href'     => 'exim.import?section=users',
            'text'     => __('actions.import'),
            'position' => 200
        ],
        'export' => [
            'href'     => 'exim.export?section=users',
            'text'     => __('actions.export'),
            'position' => 300
        ]
    ],
    'robots.manage' => [
        'seo_llms' => [
            'href'     => 'llms.manage',
            'text'     => __('actions.seo_llms'),
            'position' => 100
        ]
    ],
    'shippings.manage' => [
        'destinations' => [
            'href' => 'destinations.manage',
            'text' => __('actions.destinations'),
            'position' => 100
        ],
        'countries' => [
            'href' => 'countries.manage',
            'text' => __('actions.countries'),
            'position' => 200
        ],
        'states' => [
            'href' => 'states.manage',
            'text' => __('actions.states'),
            'position' => 300
        ],
    ],
    'states.manage' => [
        'import' => [
            'href'     => 'exim.import?section=states',
            'text'     => __('actions.import'),
            'position' => 100
        ],
        'export' => [
            'href'     => 'exim.export?section=states',
            'text'     => __('actions.export'),
            'position' => 200
        ]
    ],
    'statuses.manage' => [
        'statuses_' . strtolower(STATUSES_SHIPMENT) => [
            'href' => 'statuses.manage?type=' . STATUSES_SHIPMENT,
            'text' => __('actions.shipment_statuses'),
            'position' => 200
        ],
    ],
    'themes.manage' => [
        'layouts' => [
            'href' => 'block_manager.manage',
            'text' => __('actions.layouts'),
            'position' => 100
        ],
        'templates' => [
            'href' => 'templates.manage',
            'text' => __('actions.templates'),
            'position' => 200
        ],
        'product_tabs' => [
            'href' => 'tabs.manage',
            'text' => __('actions.product_tabs'),
            'position' => 300
        ],
        'clear_cache' => [
            'class' => 'cm-post',
            'href' => 'storage.clear_cache?redirect_url=' . urlencode(Registry::get('config.current_url')),
            'text' => __('actions.clear_cache'),
            'position' => 500
        ],
        'clear_thumbnails' => [
            'class' => 'cm-post',
            'href' => 'storage.clear_thumbnails?redirect_url=' . urlencode(Registry::get('config.current_url')),
            'text' => __('actions.clear_thumbnails'),
            'position' => 600
        ],
    ],
    'usergroups.manage' => [
        'user_group_requests' => [
            'href' => 'usergroups.requests',
            'text' => __('actions.user_group_requests'),
            'position' => 100
        ],
    ],
];

$schema['datakeeper.manage']['cdn_settings'] = [
    'href' => 'storage.cdn',
    'text' => __('actions.cdn_settings'),
    'position' => 300
];

if (!Registry::get('config.tweaks.disable_localizations')) {
    $schema['shippings.manage']['localizations'] = [
        'href' => 'localizations.manage',
        'text' => __('actions.localizations'),
        'position' => 600
    ];
}

return $schema;
