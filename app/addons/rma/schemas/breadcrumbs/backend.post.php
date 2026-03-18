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

include_once(Registry::get('config.dir.addons') . 'rma/schemas/breadcrumbs/backend.functions.php');

$schema['rma.confirmation'] = array (
    array(
        'title' => 'return_requests',
        'link' => 'rma.returns'
    )
);
$schema['rma.details'] = array (
    array (
        'type' => 'search',
        'prev_dispatch' => 'rma.returns',
        'title' => 'search_results',
        'link' => 'rma.returns.last_view'
    ),
    array (
        'title' => 'return_requests',
        'link' => 'rma.returns.reset_view'
    )
);
$schema['rma.create_return'] = array (
    array(
        'title' => array(
            'function' => array('fn_br_rma_order_title', '@order_id')
        ),
        'link' => 'orders.details?order_id=%ORDER_ID'
    )
);

return $schema;
