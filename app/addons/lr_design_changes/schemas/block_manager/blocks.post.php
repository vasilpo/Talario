<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

$base_config = [
    'text' => [
        'type' => 'text',
    ],
    'button_main' => [
        'type' => 'input',
        'default_value' => '',
        'option_name' => 'lr_design_changes.option.button_main',
    ],
    'button_main_url' => [
        'type' => 'input',
        'default_value' => '',
        'option_name' => 'lr_design_changes.option.button_main_url',
    ],
    'button_secondary' => [
        'type' => 'input',
        'default_value' => '',
        'option_name' => 'lr_design_changes.option.button_secondary',
    ],
    'button_secondary_url' => [
        'type' => 'input',
        'default_value' => '',
        'option_name' => 'lr_design_changes.option.button_secondary_url',
    ],
    'buttons_only_for_registered' => [
        'type' => 'checkbox',
        'default_value' => '',
        'option_name' => 'lr_design_changes.option.buttons_only_for_registered',
    ],
];

$banner_picker = [
    'remove_indent' => true,
    'hide_label' => true,
    'type' => 'enum',
    'object' => 'banners',
    'items_function' => 'fn_get_banners',
    'fillings' => [
        'manually' => [
            'picker' => 'addons/banners/pickers/banners/picker.tpl',
            'picker_params' => [
                'type' => 'links',
                'positions' => true,
            ],
        ],
    ],
];

$schema['lr_cta_block_banners_right'] = [
    'content' => array_merge($base_config, [
        'registered_text' => [
            'type' => 'text',
            'option_name' => 'lr_design_changes.option.registered_text',
        ],
        'registered_button_main' => [
            'type' => 'input',
            'default_value' => '',
            'option_name' => 'lr_design_changes.option.registered_button_main',
        ],
        'registered_button_main_url' => [
            'type' => 'input',
            'default_value' => '',
            'option_name' => 'lr_design_changes.option.registered_button_main_url',
        ],
        'button_main_label' => [
            'type' => 'input',
            'option_name' => 'lr_design_changes.option.button_main_label',
        ],
        'registered_button_main_label' => [
            'type' => 'input',
            'option_name' => 'lr_design_changes.option.registered_button_main_label',
        ],
        'items' => $banner_picker,
    ]),
    'templates' => [
        'addons/lr_design_changes/blocks/lr_cta_banners_right.tpl' => [],
    ],
    'cache' => [
        'update_handlers' => [
            'banners',
            'banner_descriptions',
            'banner_images',
        ],
    ],
    'brief_info_function' => 'fn_block_get_block_with_items_info',
];

$schema['lr_vendor_block_product_tab'] = [
    'templates' => [
        'addons/lr_design_changes/blocks/lr_vendor_block_product_tab.tpl' => [],
    ],
];

$schema['lr_motivation_block_product_tab'] = [
    'templates' => [
        'addons/lr_design_changes/blocks/lr_motivation_block_product_tab.tpl' => [],
    ],
];

$schema['lr_homepage_catalog'] = [
    'content' => [
        'catalog_data' => [
            'type' => 'function',
            'function' => ['fn_lr_design_changes_get_homepage_catalog_data'],
        ],
    ],
    'settings' => [
        'category_filter_id' => [
            'type' => 'input',
            'default_value' => '316',
            'option_name' => 'lr_design_changes.option.category_filter_id',
        ],
    ],
    'templates' => [
        'addons/lr_design_changes/blocks/homepage_catalog.tpl' => [],
    ],
    'wrappers' => 'blocks/wrappers',
    'show_on_locations' => ['index.index'],
];

$schema['lr_homepage_search_filters'] = [
    'content' => [
        'filter_data' => [
            'type' => 'function',
            'function' => ['fn_lr_design_changes_get_homepage_search_filters_data'],
        ],
    ],
    'settings' => [
        'city_filter_id' => [
            'type' => 'input',
            'default_value' => '321',
            'option_name' => 'lr_design_changes.option.city_filter_id',
        ],
        'age_filter_id' => [
            'type' => 'input',
            'default_value' => '322',
            'option_name' => 'lr_design_changes.option.age_filter_id',
        ],
        'category_filter_id' => [
            'type' => 'input',
            'default_value' => '316',
            'option_name' => 'lr_design_changes.option.category_filter_id',
        ],
        'free_trial_filter_id' => [
            'type' => 'input',
            'default_value' => '319',
            'option_name' => 'lr_design_changes.option.free_trial_filter_id',
        ],
        'products_dropdown_limit' => [
            'type' => 'input',
            'default_value' => '10',
            'option_name' => 'lr_design_changes.option.products_dropdown_limit',
        ],
    ],
    'templates' => [
        'addons/lr_design_changes/blocks/homepage_search_filters.tpl' => [],
    ],
    'wrappers' => 'blocks/wrappers',
    'show_on_locations' => ['index.index'],
    'cache' => [
        'update_handlers' => [
            'categories',
            'category_descriptions',
            'product_filters',
            'product_filter_descriptions',
            'product_feature_variants',
            'product_feature_variant_descriptions',
            'product_features',
            'product_features_descriptions',
        ],
    ],
];

return $schema;
