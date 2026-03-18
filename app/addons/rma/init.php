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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

Tygh::$app->register(new \Tygh\Addons\Rma\ServiceProvider());

fn_register_hooks(
    'delete_gift_certificate',
    'get_order_info',
    'change_order_status',
    'get_product_data',
    'add_to_cart',
    'get_status_params_definition',
    'delete_order',
    'paypal_get_ipn_order_ids',
    'reorder_product',
    'form_cart_pre_fill',
    'is_cart_empty',
    'update_product_pre'
);
