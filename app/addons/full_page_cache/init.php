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

fn_register_hooks(
    'render_block_pre',
    'render_block_post',
    'dispatch_before_send_response',
    'dispatch_before_display',
    'registry_save_pre',
    'register_cache',
    'user_init',
    'init_currency_pre',
    'clear_cache_post',
    'db_query_executed',
    'sucess_user_login',
    'user_logout_after',
    'update_customization_mode',
    ['get_route', 1],
    ['get_route_runtime', 1]
);

fn_init_stack([
    static function () {
        Registry::set('runtime.full_page_cache.inited', true);
    }
]);
