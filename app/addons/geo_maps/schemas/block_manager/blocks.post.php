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

$schema['geo_maps_customer_location'] = [
    'templates' => 'addons/geo_maps/blocks/customer_location.tpl',
    'wrappers' => 'blocks/wrappers',
    'content' => [
        'location' => [
            'type' => 'function',
            'function' => ['fn_geo_maps_get_customer_stored_geolocation'],
        ],
        'location_detected' => [
            'type' => 'function',
            'function' => ['fn_geo_maps_is_customer_location_detected'],
        ],
    ],
];

return $schema;