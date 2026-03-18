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
