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

use Tygh\Addons\TildaPages\ServiceProvider;

defined('BOOTSTRAP') or die('Access denied');

Tygh::$app->register(new ServiceProvider());

fn_register_hooks(
    'pre_get_page_data',
    'get_page_data',
    'delete_page',
    'get_pages',
    'page_object_by_type',
    'update_page_post',
    'update_location_post',
    'remove_location',
    'block_manager_location_get_list',
    'get_location_post',
    'block_manager_block_find_post',
    'clone_page',
    'location_copy'
);
