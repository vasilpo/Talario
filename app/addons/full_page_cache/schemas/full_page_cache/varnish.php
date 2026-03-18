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

return [
    // TTL for objects at cache, seconds
    'cache_ttl' => 180,

    // Which dispatches should be cached
    'dispatches' => [
        'index.index',
        'products.*',
        'categories.*',
        'pages.*',
        'companies.view',
        'companies.catalog',
        'companies.products',
        'product_features.view',
        'product_features.view_all',
        'promotions.*',
        'discussion.*',
        'tags.*',
    ],

    // Which dispatches must not be cached
    'disable_for_dispatches' => [
        'products.search'
    ],

    // TTL for objects at cache by dispatches, seconds
    'cache_ttl_for_dispatches' => [
        //'products.*' => 180,
        //'products.view' => 240,
        //'products.view.action' => 20,
    ],

    // Which tables(cache tags) must be ignored on changes
    'ignore_cache_tags' => [
        'cache_handlers',
        'lock_keys',
        'user_data',
        'logs',
        'sessions',
        'views',
        'users',
        'user_profiles',
        'user_session_products',
        'ekeys',
        'product_popularity'
    ],

    // Which tables(cache tags) must be used for all cache records
    'global_cache_tags' => [
        'addons',
        'categories',
        'companies',
        'storefronts',
        'settings_objects'
    ],
];
