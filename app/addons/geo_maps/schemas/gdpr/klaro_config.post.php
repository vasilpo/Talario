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

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
if (Registry::get('addons.geo_maps.provider') === 'google') {
    if (!isset($schema['services']['google_maps'])) {
        $schema['services']['google_maps'] = [
            'purposes' => ['functional'],
            'name' => 'google_maps',
            'translations' => [
                'zz' => [
                    'title' => 'geo_maps.google_maps_cookie_title',
                    'description' => 'geo_maps.google_maps_cookie_description'
                ],
            ],
        ];
    }
} else {
    $schema['services']['yandex_maps'] = [
        'purposes' => ['functional'],
        'name' => 'yandex_maps',
        'translations' => [
            'zz' => [
                'title' => 'geo_maps.yandex_maps_cookie_title',
                'description' => 'geo_maps.yandex_maps_cookie_description'
            ],
        ],
    ];
}

return $schema;
