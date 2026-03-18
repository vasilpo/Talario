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

return [
    'name'        => 'Comment',
    'description' => 'Represents product comment or review',
    'fields'      => [
        'post_id'   => [
            'type'        => Type::int(),
            'description' => 'Comment ID',
        ],
        'thread_id' => [
            'type'        => Type::int(),
            'description' => 'Thread ID',
        ],
        'message'   => [
            'type'        => Type::string(),
            'description' => 'Comment text',
        ],
        'user_id'   => [
            'type'        => Type::int(),
            'description' => 'Comment author user ID',
        ],
        'name'      => [
            'type'        => Type::string(),
            'description' => 'Comment author name',
        ],
    ],
];
