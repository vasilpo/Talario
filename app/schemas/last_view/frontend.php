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

include_once(Registry::get('config.dir.schemas') . 'last_view/frontend.functions.php');

return [
    'products'         => [
        'list_mode' => 'search',
        'view_mode' => 'quick_view',
        'func'      => 'fn_get_products',
        'item_id'   => 'product_id'
    ],
    'categories'       => [
        'list_mode'       => 'view',
        'view_controller' => 'products',
        'func'            => 'fn_get_products',
        'item_id'         => 'product_id'
    ],
    'orders'           => [
        'list_mode'    => 'search',
        'func'         => 'fn_get_orders',
        'item_id'      => 'order_id',
        'links_label'  => 'order',
        'show_item_id' => true,
    ],
    'product_features' => [
        'list_mode'       => 'view',
        'view_controller' => 'products',
        'func'            => 'fn_get_products',
        'item_id'         => 'product_id'
    ],
];
