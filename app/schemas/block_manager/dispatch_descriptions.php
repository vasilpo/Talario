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

$schema = array(
    'categories' => 'categories',
    'categories.catalog' => 'catalog',
    'categories.view' => 'view_categories',
    'checkout' => 'checkout',
    'checkout.cart' => 'cart',
    'checkout.complete' => 'order_landing_page',
    'index' => 'index',
    'orders' => 'orders',
    'orders.details' => 'order_details',
    'orders.search' => 'order_search',
    'pages' => 'pages',
    'pages.view' => 'view_page',
    'product_features' => 'features',
    'product_features.compare' => 'compare_product_features',
    'product_features.view' => 'view_product_features',
    'product_features.view_all' => 'view_all_product_features',
    'products' => 'products',
    'products.search' => 'search_product',
    'products.view' => 'view_product',
    'profiles' => 'profiles',
    'promotions' => 'promotions',
    'search' => 'search',
    'search.results' => 'search_results',
);

if (fn_allowed_for('MULTIVENDOR')) {
    $schema['companies.view'] = 'vendors';
}

return $schema;
