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

use Tygh\Registry;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}

$schema = include_once(__DIR__ . '/admin.php');

$schema['companies.update'] = [
    'from' => [
        'dispatch' => 'companies.update',
        'company_id'
    ],
    'to_customer' => [
        'dispatch' => 'companies.view',
        'company_id' => '%company_id%'
    ]
];

$schema['companies.manage'] = [
    'from' => [
        'dispatch' => 'companies.manage',
    ],
    'to_customer' => [
        'dispatch' => 'companies.catalog'
    ]
];

/** @var array<string, string> $schema */
$schema['products.manage'] = [
    'from' => [
        'dispatch' => 'products.manage'
    ],
    'to_customer' => [
        'dispatch' => 'companies.products',
        'company_id' => Registry::get('runtime.company_id')
    ]
];

$schema['products.manage&cid'] = [
    'from' => [
        'dispatch' => 'products.manage',
        'cid'
    ],
    'to_customer' => [
        'dispatch' => 'companies.products',
        'category_id' => '%cid%',
        'company_id' => Registry::get('runtime.company_id')
    ]
];

return $schema;
