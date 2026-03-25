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
        'button_main_label' => [
            'type' => 'input',
            'option_name' => 'lr_design_changes.option.button_main_label',
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

return $schema;
