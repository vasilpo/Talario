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
    'name'        => 'ProductOption',
    'description' => 'Represents an option',
    'fields'      => [
        'option_id'    => [
            'type'        => Type::int(),
            'description' => 'ID',
        ],
        'option_name'  => [
            'type'        => Type::string(),
            'description' => 'Name',
        ],
        'variant_name' => [
            'type'        => Type::listOf(Type::string()),
            'description' => 'Selected variant name',
            'resolve'     => function ($source) {
                return (array) $source['variant_name'];
            },
        ],
        'option_type'  => [
            'type'        => Type::string(),
            'descrtipion' => 'Type',
        ],
        'multiupload'  => [
            'type'        => Type::boolean(),
            'description' => 'Whether an option supports multiple variants',
        ],
    ],
];

return $schema;
