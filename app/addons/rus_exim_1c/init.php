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
    'get_orders',
    'before_dispatch',
    'store_locator_delete_store_location_post',
    'store_locator_get_store_location_before_select',
    'store_locator_update_store_location_post',
    'get_product_feature_data_before_select',
    'get_product_feature_variants',
    'get_store_locations_before_select',
    'store_locator_get_store_location_post',
    'store_locator_get_store_locations_post',
    'product_variations_convert_find_usage_options_post',
    'product_variations_convert_get_products_using_combinations_before_select',
    'product_variations_convert_get_products_using_combinations',
    'product_variations_convert_process_product_with_combinations_pre',
    'product_variations_convert_process_product_with_combinations_after_prepare_data',
    'product_variations_convert_get_features_post',
    'product_variations_convert_process_feature_post',
    'product_variations_convert_process_product_with_combinations_post',
    'variations_convert_process_post',
    'update_product_feature_variant_before_select'
);
