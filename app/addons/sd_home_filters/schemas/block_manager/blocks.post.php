<?php
/****************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

use Tygh\Enum\YesNo;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */

$schema['product_filters_home']['settings']['sd_apply_button'] = [
    'type'          => 'checkbox',
    'default_value' => YesNo::NO,
    'tooltip'       => __('sd_home_filters.apply_button_tooltip'),
];

return $schema;
