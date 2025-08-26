<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
$schema['commerceml.currencies']['commerceml_prices'] = [
    'href'     => 'commerceml.offers',
    'text'     => __('rus_exim_1c.actions.commerceml_prices'),
    'position' => 200,
];

$schema['commerceml.currencies']['commerceml_taxes'] = [
    'href'     => 'commerceml.taxes',
    'text'     => __('rus_exim_1c.actions.commerceml_taxes'),
    'position' => 300,
];

return $schema;
