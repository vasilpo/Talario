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

use Tygh\Addons\GraphqlApi\InputType as Type;

$schema = [
    'name'        => 'ProductFeatureInput',
    'description' => 'Represents a set of data to update product feature',
    'fields'      => [
        'feature_id' => [
            'type'        => Type::nonNull(Type::int()),
            'description' => 'Feature ID',
        ],
        'value'      => [
            'type'         => Type::string(),
            'defaultValue' => '',
            'description'  => 'Feature value (text features only)',
        ],
        'variants'   => [
            'type'         => Type::listOf(Type::resolveType('product_feature_variant_input')),
            'defaultValue' => [],
            'description'  => 'Selected feature variants (selectable features only)',
        ],
    ],
];

return $schema;
