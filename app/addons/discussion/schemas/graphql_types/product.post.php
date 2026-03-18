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
use Tygh\Registry;

/** @var array $schema */

$schema['fields']['comments'] = [
    'type'        => Type::listOf(Type::resolveType('comment')),
    'description' => 'Comments and reviews',
    'args'        => [
        'page'           => [
            'type'         => Type::int(),
            'defaultValue' => 1,
            'description'  => 'Page',
        ],
        'items_per_page' => [
            'type'         => Type::int(),
            'defaultValue' => Registry::get('settings.Appearance.admin_elements_per_page'),
            'description'  => 'Items per page',
        ],
    ],
    'resolve'     => function ($source, $args) {
        if (empty($source['product_id'])) {
            return [];
        }
        $params = [
            'object_id'   => (int) $source['product_id'],
            'object_type' => DISCUSSION_OBJECT_TYPE_PRODUCT,
            'page'        => $args['page'],
        ];
        list($discussions, $search) = fn_get_discussions($params, $args['items_per_page']);

        return $discussions;
    },
];

return $schema;
