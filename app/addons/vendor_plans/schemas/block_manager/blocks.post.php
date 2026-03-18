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

use Tygh\Models\VendorPlan;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @var array<string, array> $schema
 */
$schema['vendor_plan_info'] = [
    'content' => [
        'vendor_plans' => [
            'type' => 'function',
            'function' => [[VendorPlan::class, 'getAvailablePlans']],
        ],
    ],
    'templates' => [
        'addons/vendor_plans/blocks/vendor_plan_info.tpl' => [],
    ],
    'wrappers' => 'blocks/wrappers',
    'cache' => [
        'request_handlers' => ['plan_id'],
        'update_handlers' => [
            'vendor_plans', 'vendor_plan_descriptions'
        ]
    ]
];

$schema['vendor_categories']['cache']['update_handlers'][] = 'vendor_plans';

return $schema;
