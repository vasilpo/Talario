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

Tygh::$app->register(new \Tygh\Addons\Suppliers\ServiceProvider());

fn_register_hooks(
    'update_product_post',
    'get_product_data',
    'get_products',
    'get_product_fields',
    'get_shipping_info',
    'update_shipping_post',
    'shippings_group_products_list',
    'shippings_get_shippings_list',
    'pre_place_order',
    'change_order_status_post',
    'place_order_manually_post',
    'update_order_details_post',
    'get_notification_rules',
    'get_shipments_info_post',
    'get_orders_post',
    'get_order_info',
    'clone_product',
    'store_shipping_rates_pre',
    'delete_product_post',
    ['update_supplier_products_post', '', 'product_variations'],
    ['suppliers_link_product_post', '', 'product_variations'],
    'template_email_get_name',
    'update_status_pre',
    'delete_status_post'
);
