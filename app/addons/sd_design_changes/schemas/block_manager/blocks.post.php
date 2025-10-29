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

$schema['sd_cta_block'] = [
    'content' => [
        'text' => [
            'type' => 'text',
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
    ],
    'templates' => [
        'addons/sd_design_changes/blocks/sd_cta_block.tpl' => [],
        'addons/sd_design_changes/blocks/sd_cta_partner.tpl' => [],
    ]
];


return $schema;