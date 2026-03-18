<?php

return [
    'default_permission' => false,
    'controllers' => [
        'auth' => [
            'permissions'         => true,
            'permissions_blocked' => true,
        ],
        'index' => [
            'permissions'         => true,
            'permissions_blocked' => true,
        ],
        'elf_connector' => [
            'permissions' => true,
        ],
        'profiles' => [
            'modes' => [
                'update_cards' => [
                    'permissions' => false,
                ],
                'delete_profile' => [
                    'permissions' => false,
                ],
                'delete_card' => [
                    'permissions' => false,
                ],
                'request_usergroup' => [
                    'permissions' => false,
                ],
                'manage' => [
                    'param_permissions' => [
                        'user_type' => [
                            'P' => false,
                        ],
                        'default_permission' => true,
                    ],
                    'condition' => [
                        'user_type' => [
                            'A' => [
                                'operator' => 'and',
                                'function' => [
                                    0 => 'fn_check_permission_manage_profiles',
                                    1 => 'A',
                                ],
                            ],
                            'V' => [
                                'operator' => 'and',
                                'function' => [
                                    0 => 'fn_check_permission_manage_profiles',
                                    1 => 'V',
                                ],
                            ],
                        ],
                    ],
                    'permissions_blocked' => false,
                ],
                'view_product_as_user' => [
                    'permissions' => true,
                ],
                'act_as_user' => [
                    'permissions' => false,
                    'condition' => [
                        'operator' => 'or',
                        'function' => [
                            0 => 'fn_check_permission_act_as_user',
                        ],
                    ],
                ],
            ],
            'permissions' => true,
            'permissions_blocked' => true,
        ],
        'companies' => [
            'modes' => [
                'add' => [
                    'permissions' => false,
                ],
                'delete' => [
                    'permissions' => false,
                ],
                'update_status' => [
                    'permissions' => false,
                ],
                'm_activate' => [
                    'permissions' => false,
                ],
                'm_disable' => [
                    'permissions' => false,
                ],
                'm_delete' => [
                    'permissions' => false,
                ],
                'export_range' => [
                    'permissions' => false,
                ],
                'update' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => true,
                    ],
                ],
                'get_companies_list' => [
                    'permissions'         => true,
                    'permissions_blocked' => true,
                ],
            ],
            'permissions' => true,
        ],
        'profile_fields' => [
            'permissions' => false,
        ],
        'usergroups' => [
            'permissions' => false,
        ],
        'sales_reports' => [
            'modes' => [
                'view' => [
                    'permissions' => true,
                ],
                'set_report_view' => [
                    'permissions' => true,
                ],
            ],
            'permissions' => false,
        ],
        'categories' => [
            'modes' => [
                'delete' => [
                    'permissions' => false,
                ],
                'add' => [
                    'permissions' => false,
                ],
                'm_add' => [
                    'permissions' => false,
                ],
                'm_update' => [
                    'permissions' => false,
                ],
                'picker' => [
                    'permissions' => true,
                ],
            ],
            'permissions' => [
                'GET'  => true,
                'POST' => false,
            ],
        ],
        'taxes' => [
            'modes' => [
                'update' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => false,
                    ],
                ],
                'manage' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => false,
                    ],
                ],
            ],
            'permissions' => false,
        ],
        'image' => [
            'modes' => [
                'barcode' => [
                    'permissions' => true,
                ],
                'delete_image' => [
                    'permissions' => true,
                ],
                'thumbnail' => [
                    'permissions' => true,
                ],
                'upload' => [
                    'permissions' => true,
                ],
            ],
            'permissions' => false,
        ],
        'search' => [
            'modes' => [
                'results' => [
                    'permissions' => true,
                ],
            ],
            'permissions' => false,
        ],
        'states' => [
            'modes' => [
                'manage' => [
                    'permissions' => true,
                ],
            ],
            'permissions' => false,
        ],
        'countries' => [
            'modes' => [
                'manage' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => false,
                    ],
                ],
            ],
            'permissions' => false,
        ],
        'destinations' => [
            'modes' => [
                'update' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => false,
                    ],
                ],
                'manage' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => false,
                    ],
                ],
            ],
            'permissions' => false,
        ],
        'localizations' => [
            'permissions' => false,
        ],
        'languages' => [
            'permissions' => false,
        ],
        'product_features' => [
            'modes' => [
                'update' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => false,
                    ],
                ],
                'manage' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => false,
                    ],
                ],
                'groups' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => false,
                    ],
                ],
                'get_feature_variants_list' => [
                    'permissions' => true,
                ],
                'get_variants_list' => [
                    'permissions' => true,
                ],
                'get_variants' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => false,
                    ],
                ],
            ],
            'permissions' => false,
        ],
        'statuses' => [
            'permissions' => false,
        ],
        'currencies' => [
            'modes' => [
                'update' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => false,
                    ],
                ],
                'manage' => [
                    'permissions' => true,
                ],
            ],
            'permissions' => false,
        ],
        'exim' => [
            'modes' => [
                'export' => [
                    'param_permissions' => [
                        'section' => [
                            'features'     => false,
                            'orders'       => false,
                            'products'     => true,
                            'translations' => false,
                            'users'        => false,
                            'subscribers'  => false,
                        ],
                    ],
                ],
                'import' => [
                    'param_permissions' => [
                        'section' => [
                            'features'     => false,
                            'orders'       => false,
                            'products'     => true,
                            'translations' => false,
                            'users'        => false,
                            'subscribers'  => false,
                        ],
                    ],
                ],
            ],
            'permissions' => true,
        ],
        'product_filters' => [
            'modes' => [
                'update' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => false,
                    ],
                ],
                'manage' => [
                    'permissions' => [
                        'GET'  => true,
                        'POST' => false,
                    ],
                ],
                'delete' => [
                    'permissions' => false,
                ],
            ],
            'permissions' => true,
        ],
        'orders' => [
            'modes' => [
                'details' => [
                    'permissions' => true,
                ],
                'delete' => [
                    'permissions' => false,
                ],
                'delete_orders' => [
                    'permissions' => false,
                ],
                'manage' => [
                    'permissions' => true,
                ],
            ],
            'permissions' => true,
        ],
        'shippings' => [
            'permissions' => true,
        ],
        'tags' => [
            'modes' => [
                'list' => [
                    'permissions' => true,
                ],
            ],
            'permissions' => false,
        ],
        'pages' => [
            'modes' => [],
            'permissions' => true,
        ],
        'products' => [
            'modes' => [],
            'permissions' => true,
        ],
        'product_options' => [
            'permissions' => true,
        ],
        'promotions' => [
            'permissions' => false,
        ],
        'shipments' => [
            'permissions' => true,
        ],
        'attachments' => [
            'permissions' => true,
        ],
        'block_manager' => [
            'modes' => [
                'manage' => [
                    'permissions' => false,
                    'condition' => [
                        'operator' => 'or',
                        'function' => [
                            0 => 'fn_get_blocks_owner',
                        ],
                    ],
                ],
                'update_block' => [
                    'permissions' => false,
                    'condition' => [
                        'operator' => 'or',
                        'function' => [
                            0 => 'fn_get_blocks_owner',
                        ],
                    ],
                ],
                'update_status' => [
                    'permissions' => false,
                    'condition' => [
                        'operator' => 'or',
                        'function' => [
                            0 => 'fn_get_blocks_owner',
                        ],
                    ],
                ],
                'snapping' => [
                    'permissions' => false,
                    'condition' => [
                        'operator' => 'or',
                        'function' => [
                            0 => 'fn_get_blocks_owner',
                        ],
                    ],
                ],
                'grid' => [
                    'permissions' => false,
                    'condition' => [
                        'operator' => 'or',
                        'function' => [
                            0 => 'fn_get_blocks_owner',
                        ],
                    ],
                ],
                'block_selection' => [
                    'permissions' => false,
                    'condition' => [
                        'operator' => 'or',
                        'function' => [
                            0 => 'fn_get_blocks_owner',
                        ],
                    ],
                ],
                'manage_in_tab' => [
                    'permissions' => false,
                    'condition' => [
                        'operator' => 'or',
                        'function' => [
                            0 => 'fn_get_blocks_owner',
                        ],
                    ],
                ],
                'set_custom_container' => [
                    'permissions' => false,
                    'condition' => [
                        'operator' => 'or',
                        'function' => [
                            0 => 'fn_get_blocks_owner',
                        ],
                    ],
                ],
                'update_grid' => [
                    'permissions' => false,
                    'condition' => [
                        'operator' => 'or',
                        'function' => [
                            0 => 'fn_get_blocks_owner',
                        ],
                    ],
                ],
            ],
        ],
        'tools' => [
            'modes' => [
                'update_position' => [
                    'param_permissions' => [
                        'table' => [
                            'images_links' => true,
                        ],
                    ],
                ],
                'update_status' => [
                    'param_permissions' => [
                        'table' => [
                            'shippings'       => true,
                            'products'        => true,
                            'product_options' => true,
                            'attachments'     => true,
                            'product_files'   => true,
                            'pages'           => true,
                            'shipments'       => true,
                            'call_requests'   => true,
                            'subscribers'     => false,
                        ],
                    ],
                ],
                'cleanup_history' => [
                    'permissions' => true,
                ],
                'view_changes' => [
                    'permissions' => false,
                ],
            ],
        ],
        'logs' => [
            'permissions' => true,
        ],
        'debugger' => [
            'permissions' => true,
        ],
        'file_editor' => [
            'permissions' => true,
        ],
        'themes' => [
            'modes' => [
                'manage' => [
                    'permissions' => false,
                    'condition' => [
                        'operator' => 'or',
                        'function' => [
                            0 => 'fn_get_styles_owner',
                        ],
                    ],
                ],
                'styles' => [
                    'permissions' => false,
                    'condition' => [
                        'operator' => 'or',
                        'function' => [
                            0 => 'fn_get_styles_owner',
                        ],
                    ],
                ],
            ],
        ],
        'customization' => [
            'modes' => [
                'update_mode' => [
                    'param_permissions' => [
                        'type' => [
                            'theme_editor' => [
                                'permissions' => false,
                                'condition' => [
                                    'operator' => 'or',
                                    'function' => [
                                        0 => 'fn_get_styles_owner',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'notifications' => [
            'permissions'         => true,
            'permissions_blocked' => true,
        ],
        'vendor_communication' => [
            'permissions' => true,
            'modes' => [
                'delete_thread' => [
                    'permissions' => false,
                ],
                'm_delete_thread' => [
                    'permissions' => false,
                ],
                'create_thread' => [
                    'permissions' => false,
                ],
            ],
        ],
        'import_presets' => [
            'permissions' => true,
        ],
        'advanced_import' => [
            'permissions' => true,
        ],
        'premoderation' => [
            'modes' => [
                'products_approval' => [
                    'permissions' => false,
                ],
            ],
        ],
        'vendor_plans' => [
            'permissions' => false,
        ],
        'product_variations' => [
            'permissions' => true,
        ],
        'call_requests' => [
            'permissions' => true,
        ],
        'discussion' => [
            'modes' => [
                'add' => [
                    'permissions' => true,
                ],
                'update' => [
                    'permissions' => false,
                ],
                'delete' => [
                    'permissions' => false,
                ],
                'm_delete' => [
                    'permissions' => false,
                ],
            ],
            'permissions' => false,
        ],
        'discussion_manager' => [
            'modes' => [
                'manage' => [
                    'permissions' => false,
                ],
            ],
            'permissions' => true,
        ],
    ],
    'export' => [
        'sections' => [
            'translations' => [
                'permission' => false,
            ],
            'users' => [
                'permission' => false,
            ],
            'features' => [
                'permission' => false,
            ],
            'vendors' => [
                'permission' => false,
            ],
            'subscribers' => [
                'permission' => false,
            ],
        ],
        'patterns' => [
            'google' => [
                'permission' => false,
            ],
        ],
    ],
    'import' => [
        'sections' => [
            'translations' => [
                'permission' => false,
            ],
            'orders' => [
                'permission' => false,
            ],
            'users' => [
                'permission' => false,
            ],
            'features' => [
                'permission' => false,
            ],
            'vendors' => [
                'permission' => false,
            ],
            'subscribers' => [
                'permission' => false,
            ],
        ],
        'patterns' => [],
    ],
];
