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

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Addons\MobileApp\ServiceProvider;

require_once __DIR__ . '/lib/vendor/autoload.php';
define('CACHE_ITEM_TTL', 3600);

Tygh::$app->register(new ServiceProvider());

fn_register_hooks(
    /** @see \fn_mobile_app_change_order_status() */
    'change_order_status',
    /** @see \fn_mobile_app_delete_image_pre() */
    'delete_image_pre',
    /** @see \fn_mobile_app_storefront_repository_save_post() */
    'storefront_repository_save_post',
    /** @see \fn_mobile_app_storefront_rest_api_get_storefront() */
    'storefront_rest_api_get_storefront'
);
