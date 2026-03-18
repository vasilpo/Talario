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
    'name'        => 'ProductShippingParameters',
    'description' => 'Represents product shipping parameters',
    'fields'      => [
        'min_items_in_box' => [
            'type'        => Type::int(),
            'description' => 'Items in a box: min',
        ],
        'max_items_in_box' => [
            'type'        => Type::int(),
            'description' => 'Items in a box: max',
        ],
        'box_length'       => [
            'type'        => Type::int(),
            'description' => 'Box length',
        ],
        'box_width'        => [
            'type'        => Type::int(),
            'description' => 'Box width',
        ],
        'box_height'       => [
            'type'        => Type::int(),
            'description' => 'Box height',
        ],
    ],
];

return $schema;
