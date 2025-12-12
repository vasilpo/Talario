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

use Tygh\Enum\YesNo;
use Tygh\Registry;

if ($mode === 'details') {
    $view = Tygh::$app['view'];

    $payment_method = $view->getTemplateVars('payment_method');

    $order_info = $view->getTemplateVars('order_info');
    $payment_info = $order_info['payment_info'];

    if (
        isset($payment_method['processor_params']['is_paypal_checkout'])
        && YesNo::toBool($payment_method['processor_params']['is_paypal_checkout'])
    ) {
        $processor_params = $payment_method['processor_params'];
        $total = $order_info['total'] - $order_info['payment_surcharge'] + $payment_method['surcharge_value'];

        if (!isset($order_info['companies'])) {
            $order_info['companies'] = fn_get_products_companies($order_info['products']);
        }

        $payment_method['processor_params']
            = $payment_info['processor_params']
            = $processor_params;

        if (CART_PRIMARY_CURRENCY !== $processor_params['currency']) {
            $total = fn_format_price_by_currency($total, CART_PRIMARY_CURRENCY, $processor_params['currency']);
        }
        $currency_data = Registry::get('currencies.' . $processor_params['currency']);

        /** @var float $total */
        $total = fn_format_rate_value(
            $total,
            'F',
            $currency_data['decimals'],
            '.',
            ''
        );

        $view->assign(
            [
                'order_info'                 => $order_info,
                'payment_method'             => $payment_method,
                'paypal_checkout_cart_total' => $total,
            ]
        );
    }
}

return [CONTROLLER_STATUS_OK];
