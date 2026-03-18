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

use Tygh\Addons\VendorDataPremoderation\ServiceProvider;

defined('BOOTSTRAP') or die('Access denied');

Tygh::$app->register(new ServiceProvider());

fn_register_hooks(
    ['get_product_data_post', 1600],
    'update_company_pre',
    'set_admin_notification',
    'clone_product_post',
    'update_product_pre',
    'update_product_before_update',
    ['update_product_post', 4000],
    'update_product_file_pre',
    'update_product_file_post',
    'update_product_file_folder_pre',
    'update_product_file_folder_post',
    'delete_product_files_before_delete',
    'delete_product_file_folders_before_delete',
    'delete_product_files_post',
    'delete_product_file_folders_post',
    'data_feeds_export_before_get_products',
    'tools_update_status_before_query',
    'gather_additional_products_data_post',
    'get_product_statuses_post',
    'get_all_product_statuses_post',
    'delete_product_post',
    'get_dashboard_products_block_data',
    'update_product_categories_pre',
    'update_product_categories_post',
    'change_company_status_pre',
    'smarty_component_configurable_page_field_before_output'
);
