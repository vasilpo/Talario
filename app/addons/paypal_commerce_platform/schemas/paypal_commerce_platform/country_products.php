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

use Tygh\Addons\PaypalCommercePlatform\Enum\ProductType;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Describes which product should be used when connecting a vendor from the specific country.
 *
 * @see https://developer.paypal.com/docs/platforms/checkout/reference/country-availability-advanced-cards/
 * @see https://developer.paypal.com/docs/platforms/seller-onboarding/before-payment/#modify-the-code
 */
return [
    // Regions that support advanced credit and debit card payments will use PayPal Complete Payments
    'AU'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'AT'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'BE'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'BG'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'CA'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'CN'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'CY'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'CZ'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'DK'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'EE'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'FI'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'FR'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'DE'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'GR'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'HK'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'HU'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'IE'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'IT'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'JP'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'LV'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'LI'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'LT'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'LU'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'MT'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'MX'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'NL'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'NO'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'PL'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'PT'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'RO'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'SG'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'SK'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'SI'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'ES'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'SE'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'GB'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    'US'        => ProductType::PAYPAL_COMPLETE_PAYMENTS,
    // All other regions will use Express Checkout payments
    '__default' => ProductType::EXPRESS_CHECKOUT,
];
