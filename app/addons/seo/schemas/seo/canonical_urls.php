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

$schema = array();

$schema['categories']['view'] = array(
    'base_url' => 'categories.view?category_id=[category_id]',
    'request_handlers' => array(
        'category_id' => true
    ),
    'search' => true
);
$schema['companies']['products'] = array(
    'base_url' => array('fn_seo_filter_current_url' => array('result_ids', 'full_render', 'filter_id', 'view_all', 'req_range_id', 'features_hash', 'subcats', 'page', 'total', 'hint_q', 'sort_by', 'sort_order', 'items_per_page', 'layout')),
    'search' => true
);
$schema['companies']['catalog'] = array(
    'base_url' => 'companies.catalog',
    'search' => true
);
$schema['index']['index'] = array(
    'base_url' => array('fn_url' => array('')),
    'search' => array()
);
$schema['product_features']['view'] = array(
    'base_url' => 'product_features.view?variant_id=[variant_id]',
    'request_handlers' => array(
        'variant_id' => true
    ),
    'search' => true
);
$schema['products']['view'] = array(
    'base_url' => 'products.view?product_id=[product_id]',
    'request_handlers' => array(
        'product_id' => true
    ),
    'search' => array()
);
$schema['products']['search'] = array(
    'base_url' => array('fn_seo_filter_current_url' => array('result_ids', 'full_render', 'filter_id', 'view_all', 'req_range_id', 'features_hash', 'subcats', 'page', 'total', 'hint_q')),
    'request_handlers' => array(
        'search_performed' => true
    ),
    'search' => true
);

return $schema;
