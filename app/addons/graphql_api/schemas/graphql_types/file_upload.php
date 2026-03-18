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
    'name'        => 'FileUpload',
    'description' => 'Represents a set of data to upload a file',
    'fields'      => [
        'name'     => [
            'type'        => Type::nonNull(Type::listOf(Type::string())),
            'description' => 'File upload name',
        ],
        'error'    => [
            'type'        => Type::nonNull(Type::listOf(Type::int())),
            'description' => 'Error code',
        ],
        'size'     => [
            'type'        => Type::nonNull(Type::listOf(Type::int())),
            'description' => 'File size in bytes',
        ],
        'tmp_name' => [
            'type'        => Type::nonNull(Type::listOf(Type::string())),
            'description' => 'Temporary file location',
        ],
        'type'     => [
            'type'        => Type::nonNull(Type::listOf(Type::string())),
            'description' => 'MIME-type of uploaded file',
        ],
        'full_path' => [
            'type'        => Type::listOf(Type::string()),
            'description' => 'Temporary full file location',
        ]
    ],
];

return $schema;
