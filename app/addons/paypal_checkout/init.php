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

use Tygh\Addons\PaypalCheckout\ServiceProvider;
use Tygh\Tygh;

Tygh::$app->register(new ServiceProvider());

fn_register_hooks(
    /** @see \fn_paypal_checkout_save_log() */
    'save_log',
    /** @see \fn_paypal_checkout_update_shipment_before_send_notification() */
    'update_shipment_before_send_notification',
    /** @see \fn_paypal_checkout_update_shipment_post() */
    'update_shipment_post',
    /** @see \fn_paypal_checkout_api_update_shipment_pre() */
    'api_update_shipment_pre'
);
