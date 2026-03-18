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
    'name'        => 'ImageInput',
    'description' => 'Represents a set of data to update an image',
    'fields'      => [
        'upload'     => [
            'type'        => Type::resolveType('file_upload'),
            'description' => 'File upload',
        ],
        'image_path' => [
            'type'        => Type::string(),
            'description' => 'Image URL or path on server',
        ],
        'alt'        => [
            'type'        => Type::string(),
            'description' => 'Image alt',
        ],
    ],
];

return $schema;
