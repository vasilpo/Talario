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
$schema['products']['cache']['update_handlers'][] = 'product_review_prepared_data';

$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'product_reviews';
$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'product_review_votes';
$schema['main']['cache_overrides_by_dispatch']['products.view']['update_handlers'][] = 'product_review_prepared_data';

$schema['products']['content']['items']['fillings']['rating'] = [
    'params' => [
        'rating'          => true,
        'sort_by'         => 'rating',
        'show_only_rated' => true
    ],
];

return $schema;
