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

/** @var array $schema */
$schema['services']['google-analytics'] = [
    'purposes'     => ['performance'],
    'name'         => 'google-analytics',
    'translations' => [
        'zz' => [
            'title'       => 'google_analytics.google_analytics_cookies_title',
            'description' => 'google_analytics.google_analytics_cookies_description'
        ],
    ],
];

$schema['services']['google-ads'] = [
    'purposes'     => ['marketing'],
    'name'         => 'google-ads',
    'translations' => [
        'zz' => [
            'title'       => 'google_analytics.google_ads_cookies_title',
            'description' => 'google_analytics.google_ads_cookies_description'
        ],
    ],
];

return $schema;
