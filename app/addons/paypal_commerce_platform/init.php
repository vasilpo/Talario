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

use Tygh\Addons\PaypalCommercePlatform\ServiceProvider;
use Tygh\Tygh;

Tygh::$app->register(new ServiceProvider());

fn_register_hooks(
    /** @see \fn_paypal_commerce_platform_get_payments() */
    'get_payments',
    /** @see \fn_paypal_commerce_platform_rma_update_details_post() */
    'rma_update_details_post',
    /** @see \fn_paypal_commerce_platform_get_companies() */
    'get_companies',
    /** @see \fn_paypal_commerce_platform_vendor_data_premoderation_diff_company_data_post() */
    'vendor_data_premoderation_diff_company_data_post',
    /** @see \fn_paypal_commerce_platform_update_addon_status_post() */
    'update_addon_status_post',
    /** @see \fn_paypal_commerce_platform_save_log() */
    'save_log',
    /** @see \fn_paypal_commerce_platform_update_shipment_before_send_notification() */
    'update_shipment_before_send_notification',
    /** @see \fn_paypal_commerce_platform_update_shipment_post() */
    'update_shipment_post',
    /** @see \fn_paypal_commerce_platform_api_update_shipment_pre() */
    'api_update_shipment_pre'
);
