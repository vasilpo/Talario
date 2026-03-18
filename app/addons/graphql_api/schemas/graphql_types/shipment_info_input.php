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
    'name'        => 'ShipmentInfoInput',
    'description' => 'Represents a shipment details',
    'fields'      => [
        'shipment_id'     => [
            'type'         => Type::int(),
            'defaultValue' => 0,
            'description'  => 'ID',
        ],
        'group_id'        => [
            'type'         => Type::nonNull(Type::int()),
            'defaultValue' => 0,
            'description'  => 'Product group ID',
        ],
        'shipping_id'     => [
            'type'         => Type::nonNull(Type::int()),
            'defaultValue' => 0,
            'description'  => 'Shipping method ID',
        ],
        'carrier'         => [
            'type'         => Type::string(),
            'defaultValue' => '',
            'description'  => 'Carrier ID',
        ],
        'tracking_number' => [
            'type'         => Type::string(),
            'defaultValue' => '',
            'description'  => 'Tracking number',
        ],
    ],
];

return $schema;
