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
    'pre_add_to_cart',
    'generate_cart_id',
    'calculate_cart',
    'delete_cart_product',
    'reorder',
    'calculate_cart_items',
    'add_to_cart',
    'delete_product_pre',
    'update_cart_products_post',
    'update_cart_products_pre',
    'init_product_tabs_post',
    ['buy_together_update_chain_post', '', 'product_variations'],
    ['buy_together_delete_chain_post', '', 'product_variations']
);
