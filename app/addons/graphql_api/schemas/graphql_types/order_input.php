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
    'name'        => 'OrderInput',
    'description' => 'Represents an order',
    'fields'      => [
        // data
        'status'          => [
            'type'         => Type::string(),
            'defaultValue' => null,
            'description'  => 'Order status',
        ],
        // shipping
        'update_shipping' => [
            'type'         => Type::listOf(Type::resolveType('shipment_info_input')),
            'defaultValue' => null,
            'description'  => 'Shipping information',
        ],
        // notes
        'notes'           => [
            'type'         => Type::string(),
            'defaultValue' => null,
            'description'  => 'Customer notes',
        ],
        'details'         => [
            'type'         => Type::string(),
            'defaultValue' => null,
            'description'  => 'Staff only notes',
        ],
    ],
];

return $schema;
