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

$schema['products']['content']['items']['fillings']['bestsellers'] = array (
    'params' => array (
        'bestsellers' => true,
        'sales_amount_from' => 1,
        'include_child_variations' => true,
        'sort_by' => 'sales_amount',
        'sort_order' => 'desc',
        'request' => array (
            'cid' => '%CATEGORY_ID'
        )
    ),
);

$schema['products']['content']['items']['fillings']['on_sale'] = array (
    'params' => array (
        'on_sale' => true,
        'sort_by' => 'on_sale',
        'sort_order' => 'desc',
        'extend' => [
            'prices2' => true
        ]
    ),
);

$schema['products']['content']['items']['fillings']['similar'] = array (
    'params' => array (
        'similar' => true,
        'request' => array (
            'main_product_id' => '%PRODUCT_ID%'
        )
    )
);

$schema['products']['cache']['request_handlers'][] = '%PRODUCT_ID%';

return $schema;
