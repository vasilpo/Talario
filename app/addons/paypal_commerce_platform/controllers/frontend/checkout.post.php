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

use Tygh\Addons\PaypalCommercePlatform\Payments\PaypalCommercePlatform;
use Tygh\Enum\YesNo;
use Tygh\Registry;

if ($mode === 'checkout') {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];

    /** @var array $payment_method */
    $payment_method = $view->getTemplateVars('payment_method');

    /** @var array $payment_info */
    $payment_info = $view->getTemplateVars('payment_info');

    /** @var array $cart */
    $cart = $view->getTemplateVars('cart');

    if (
        isset($payment_method['processor_params']['is_paypal_commerce_platform'])
        && YesNo::toBool($payment_method['processor_params']['is_paypal_commerce_platform'])
    ) {
        $processor_params = $payment_method['processor_params'];
        $total = $cart['total'] + $cart['payment_surcharge'];

        if (!isset($cart['companies'])) {
            $cart['companies'] = fn_get_products_companies($cart['products']);
        }

        foreach ($cart['companies'] as $company_id) {
            $processor_params['merchant_ids'][] = PaypalCommercePlatform::getChargeReceiver($company_id);
        }

        $payment_method['processor_params']
            = $payment_info['processor_params']
            = $cart['payment_method_data']['processor_params']
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
                'cart'                                => $cart,
                'payment_info'                        => $payment_info,
                'payment_method'                      => $payment_method,
                'paypal_commerce_platform_cart_total' => $total,
            ]
        );
    }

    return [CONTROLLER_STATUS_OK];
}
