<?php
/***************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

use Tygh\Registry;
use Tygh\Enum\YesNo;
use Tygh\Enum\ObjectStatuses;

defined('BOOTSTRAP') or die('Access denied');

$base_config = [
    'text' => [
        'type' => 'text'
    ],
    'button_main' => [
        'type' => 'input',
        'default_value' => '',
        'option_name' => 'sd_design_changes.option.button_main'
    ],
    'button_main_url' => [
        'type' => 'input',
        'default_value' => '',
        'option_name' => 'sd_design_changes.option.button_main_url'
    ],
    'button_secondary' => [
        'type' => 'input',
        'default_value' => '',
        'option_name' => 'sd_design_changes.option.button_secondary'
    ],
    'button_secondary_url' => [
        'type' => 'input',
        'default_value' => '',
        'option_name' => 'sd_design_changes.option.button_secondary_url'
    ],
    'buttons_only_for_registered' => [
        'type' => 'checkbox',
        'default_value' => '',
        'option_name' => 'sd_design_changes.option.buttons_only_for_registered'
    ],
];

$schema['sd_cta_block'] = [
    'content' => $base_config,
    'templates' => [
        'addons/sd_design_changes/blocks/sd_cta_block.tpl' => [],
        'addons/sd_design_changes/blocks/sd_cta_partner.tpl' => [],
    ]
];

$schema['sd_cta_block_banners'] = [
    'content' => array_merge($base_config, [
        'button_main_label' => [
            'type' => 'input',
            'option_name' => 'sd_design_changes.option.button_main_label'
        ],
        'items' => [
            'remove_indent' => true,
            'hide_label' => true,
            'type' => 'enum',
            'object' => 'banners',
            'items_function' => 'fn_get_banners',
            'fillings' => [
                'manually' => [
                    'picker' => 'addons/banners/pickers/banners/picker.tpl',
                    'picker_params' => ['type' => 'links', 'positions' => true]
                ],
            ],
        ],
    ]),
    'templates' => ['addons/sd_design_changes/blocks/sd_cta_banners.tpl' => []],
    'cache' => [
        'update_handlers' => [
            'banners', 'banner_descriptions', 'banner_images'
        ]
    ],
    'brief_info_function' => 'fn_block_get_block_with_items_info'
];

$schema['sd_banner_text'] = [
    'content' => [
        'items' => [
            'remove_indent' => true,
            'hide_label' => true,
            'type' => 'enum',
            'object' => 'banners',
            'items_function' => 'fn_get_banners',
            'fillings' => [
                'manually' => [
                    'picker' => 'addons/banners/pickers/banners/picker.tpl',
                    'picker_params' => ['type' => 'links', 'positions' => true]
                ],
            ],
        ],
    ],
    'templates' => ['addons/sd_design_changes/blocks/sd_banner_text.tpl' => []],
    'cache' => [
        'update_handlers' => [
            'banners', 'banner_descriptions', 'banner_images'
        ]
    ],
    'brief_info_function' => 'fn_block_get_block_with_items_info'
];

return $schema;