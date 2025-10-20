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

$schema['addons/sd_design_changes/blocks/categories/sd_categories_list.tpl'] = [
    'params' => [
        'get_images' => true,
    ]
];


$schema['blocks/products/ab__grid_list.tpl']['settings'] = array_merge(
    $schema['blocks/products/ab__grid_list.tpl']['settings'],
    [
        'show_short_desc' => [
            'type' => 'checkbox',
            'default_value' => YesNo::NO,
            'option_name' => 'sd_theme_integration.option.show_short_description',
        ],
    ]
);

return $schema;
