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

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Addons\GraphqlApi\Type;

$schema = [
    'name'        => 'Currency',
    'description' => 'Represents a currency',
    'fields'      => [
        'currency_code'       => [
            'type'        => Type::string(),
            'description' => 'Code',
        ],
        'description'         => [
            'type'        => Type::string(),
            'description' => 'Name',
        ],
        'is_primary'          => [
            'type'        => Type::boolean(),
            'description' => 'Whether a currency is primary',
        ],
        'symbol'              => [
            'type'        => Type::string(),
            'description' => 'Currency symbol',
        ],
        'after'               => [
            'type'        => Type::boolean(),
            'description' => 'Whether a currency symbol must be displayed after the sum',
        ],
        'decimals'            => [
            'type'        => Type::int(),
            'description' => 'Number of digits after the decimal sign.',
        ],
        'decimals_separator'  => [
            'type'        => Type::string(),
            'description' => 'Decimal separator',
        ],
        'thousands_separator' => [
            'type'        => Type::string(),
            'description' => 'Thousand separator',
        ],
    ],
];

return $schema;
