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

use Tygh\Addons\GraphqlApi\Api;
use Tygh\Addons\GraphqlApi\InputType;
use Tygh\Addons\GraphqlApi\Type;

$schema = [
    'name'         => 'Mutation',
    'fields'       => [
        'delete_product' => [
            'type'        => Type::boolean(),
            'description' => 'Deletes product by ID',
            'args'        => [
                'id' => InputType::nonNull(InputType::int()),
            ],
        ],
        'create_product' => [
            'type'        => Type::int(),
            'description' => 'Creates product',
            'args'        => [
                'product' => InputType::nonNull(InputType::resolveType('create_product_input')),
            ],
        ],
        'update_product' => [
            'type'        => Type::boolean(),
            'description' => 'Updates product by ID',
            'args'        => [
                'id'      => InputType::nonNull(InputType::int()),
                'product' => InputType::nonNull(InputType::resolveType('update_product_input')),
            ],
        ],
        'update_order'   => [
            'type'        => Type::boolean(),
            'description' => 'Updates order by ID',
            'args'        => [
                'id'                => [
                    'type'        => InputType::nonNull(InputType::int()),
                    'description' => 'ID',
                ],
                'order' => [
                    'type'        => InputType::nonNull(InputType::resolveType('order_input')),
                    'description' => 'Order details',
                ],
                'notify_user'       => [
                    'type'         => InputType::boolean(),
                    'defaultValue' => true,
                    'description'  => 'Whether to notify customer',
                ],
                'notify_department' => [
                    'type'         => InputType::boolean(),
                    'defaultValue' => true,
                    'description'  => 'Whether to notify orders department',
                ],
                'notify_vendor'     => [
                    'type'         => InputType::boolean(),
                    'defaultValue' => true,
                    'description'  => 'Whether to notify vendor',
                ],
            ],
        ],
    ],
    'resolveField' => [Api::class, 'dispatchRequest'],
];

return $schema;
