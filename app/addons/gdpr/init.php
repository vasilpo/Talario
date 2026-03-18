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

use Tygh\Addons\Gdpr\ServiceProvider;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

Tygh::$app->register(new ServiceProvider());

fn_register_hooks(
    'update_product_notifications_post',
    'checkout_place_orders_pre_route',
    'save_cart_content_post',
    'user_logout_after',
    'update_profile',
    'update_company',
    'get_users',
    'user_init',
    'login_user_post',
    'smarty_function_script_after_formation',
    'get_addons_post',
    'update_payment_post'
);
