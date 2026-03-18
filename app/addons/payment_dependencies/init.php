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

fn_register_hooks(
    'get_shipping_info_post',
    'update_shipping_post',
    'prepare_checkout_payment_methods',
    'shippings_get_shippings_list_post',
    'checkout_select_default_payment_method',
    'update_payment_post',
    'delete_payment_post',
    'delete_shipping',
    'prepare_checkout_payment_methods_before_get_payments',
    'calculate_cart_content_before_shipping_calculation',
    'get_access_to_checkout',
    'allow_place_order_post'
);
