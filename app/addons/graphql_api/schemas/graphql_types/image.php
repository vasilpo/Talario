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
    'name'        => 'Image',
    'description' => 'Represents an image',
    'fields'      => [
        'image_path'       => [
            'type'       => Type::string(),
            'description' => 'Image URL',
        ],
        'alt'        => [
            'type'        => Type::string(),
            'description' => 'Image alt',
        ],
        'image_x'          => [
            'type'       => Type::int(),
            'description' => 'Image width',
        ],
        'image_y'          => [
            'type'       => Type::int(),
            'description' => 'Image height',
        ],
        'http_image_path'  => [
            'type'       => Type::string(),
            'description' => 'Image URL with HTTP',
        ],
        'https_image_path' => [
            'type'       => Type::string(),
            'description' => 'Image URL with HTTPS',
        ],
    ],
];

return $schema;
