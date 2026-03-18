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
use Tygh\Addons\GraphqlApi\Type\BooleanInputType;

$schema = [
    'name'        => 'ProductFeatureVariantInput',
    'description' => 'Represents a set of data to update product feature variant',
    'fields'      => [
        'variant_id' => [
            'type'         => Type::int(),
            'defaultValue' => null,
            'description'  => 'Variant ID',
        ],
        'variant'    => [
            'type'         => Type::string(),
            'defaultValue' => '',
            'description'  => 'Name',
        ],
        'selected'   => [
            'type'         => Type::nonNull(Type::resolveType(BooleanInputType::class)),
            'defaultValue' => false,
            'description'  => 'Whether a variant is selected for a product',
        ],
    ],
];

return $schema;
