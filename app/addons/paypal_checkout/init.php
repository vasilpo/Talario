<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

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
