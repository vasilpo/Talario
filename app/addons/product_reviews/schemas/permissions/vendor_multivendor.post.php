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

use Tygh\Http;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
$schema['controllers']['product_reviews'] = [
    'modes' => [
        /**
         * The product_reviews.view is not used in the administration panel,
         * but this action is required for proper permissions check of vendors
         */
        'view'     => [
            'permissions' => 'view_product_reviews',
        ],
        'update'   => [
            'permissions' => [Http::GET => 'view_product_reviews', Http::POST => 'manage_product_reviews'],
        ],
        'delete'   => [
            'permissions' => false,
        ],
        'm_delete' => [
            'permissions' => false,
        ],
        'manage'    => [
            'permissions' => 'view_product_reviews',
        ],
        'permissions' => false,
    ],
];

$schema['tools']['modes']['update_status']['product_reviews']['table']['product_reviews'] = false;

return $schema;
