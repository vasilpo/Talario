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

use Tygh\Enum\DashboardSections;

defined('BOOTSTRAP') or die('Access denied');

/**
 * This schema contains all blocks on dashboard in administration panel
 *
 * @var array{
 *     array-key: array{
 *         array-key: array{
 *              id: string,
 *              position: int,
 *              dispatch: string,
 *              title: string,
 *              title_button?: array{
 *                  name: string,
 *                  href: string
 *              },
 *              content_data_function: string,
 *              is_selected_date?: bool,
 *              use_price_for_number?: bool,
 *         }
 *     }
 * } $schema
 */

$schema[DashboardSections::TERTIARY]['plans'] = [
    'id' => 'vendor_plans_analytics_card_vendor_plan',
    'title' => __('vendor_plans.dashboard.analytics_card.vendor_plan'),
    'position' => 15,
    'dispatch' => 'companies.balance',
    'content_data_function' => 'fn_get_vendor_plans_dashboard_block_data'
];

return $schema;
