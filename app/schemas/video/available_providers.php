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

//phpcs:ignore
$schema = [
    'Vimeo' => [
        'source_name'           => 'Vimeo',
        'url_constructor_class' => '\Tygh\Video\UrlConstructors\VimeoUrlConstructor',
    ],
    'Youtube' => [
        'source_name'           => 'YouTube',
        'url_constructor_class' => '\Tygh\Video\UrlConstructors\YoutubeUrlConstructor',
    ],
    'Rutube' => [
        'source_name'           => 'RuTube',
        'url_constructor_class' => '\Tygh\Video\UrlConstructors\RutubeUrlConstructor',
    ]
];

return $schema;
