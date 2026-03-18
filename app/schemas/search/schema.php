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
    'products' => array(
        'condition_function' => 'fn_create_products_condition',
        'default_params' => array(
            'pshort' => 'Y',
            'pfull' => 'Y',
            'pname' => 'Y',
            'pkeywords' => 'Y',
        ),
        'title' => __('products'),
        'more_data_function' => '',
        'bulk_data_function' => 'fn_gather_additional_products_data_for_search',
        'action_link' => 'products.manage?show_select_storefront=N&compact=Y&q=%search%&pshort=Y&pfull=Y&pname=Y&pkeywords=Y&pcode_from_q=Y&pid=%search_num%&match=any&content_id=products_content',
        'detailed_link' => 'products.update?product_id=%id%',
        'show_in_search' => false,
        'default' => true
    ),
    'pages' => array(
        'condition_function' => 'fn_create_pages_condition',
        'default_params' => array(
            'pdescr' => 'Y',
            'pname' => 'Y',
        ),
        'title' => __('pages'),
        'more_data_function' => '',
        'bulk_data_function' => '',
        'action_link' => 'pages.manage?show_select_storefront=N&compact=Y&q=%search%&match=any&content_id=pages_content&pdescr=Y',
        'detailed_link' => 'pages.update?page_id=%id%',
        'show_in_search' => true
    )
);

if (AREA == 'A') {
    $schema['orders'] = array(
        'condition_function' => 'fn_create_orders_condition',
        'default_params' => array(),
        'title' => __('orders'),
        'more_data_function' => '',
        'bulk_data_function' => '',
        'action_link' => 'orders.manage?show_select_storefront=N&order_id=%search_num%&compact=Y&email=%search%&cname=%search%&content_id=order_content',
        'detailed_link' => 'orders.details?order_id=%id%',
        'show_in_search' => false
    );
    $schema['users'] = array(
        'condition_function' => 'fn_create_users_condition',
        'default_params' => array(),
        'title' => __('customers'),
        'more_data_function' => '',
        'bulk_data_function' => '',
        'action_link' => 'profiles.manage?show_select_storefront=N&name=%search%&email=%search%&user_login=%search%&compact=Y&content_id=users_content',
        'detailed_link' => 'profiles.update?user_id=%id%',
        'show_in_search' => false
     );
}

return $schema;
