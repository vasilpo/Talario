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
    'name'        => 'ProductFeatureVariant',
    'description' => 'Represents product feature variant',
    'fields'      => [
        'variant_id'  => [
            'type'        => Type::int(),
            'description' => 'Variant ID',
        ],
        'variant'     => [
            'type'        => Type::string(),
            'description' => 'Name',
        ],
        'image_pairs' => [
            'type'        => Type::listOf(Type::resolveType('image')),
            'description' => 'Variant images',
        ],
        'selected'    => [
            'type'        => Type::boolean(),
            'description' => 'Whether a variant is selected for a product',
            'resolve'     => function ($source) {
                return $source['selected'] !== null;
            },
        ],
    ],
];

return $schema;
