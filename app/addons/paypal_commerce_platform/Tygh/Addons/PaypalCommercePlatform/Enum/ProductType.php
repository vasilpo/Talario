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

namespace Tygh\Addons\PaypalCommercePlatform\Enum;

/**
 * Class ProductType contains possible values of supported products.
 *
 * @see https://developer.paypal.com/docs/api/partner-referrals/v2/#definition-product_name
 *
 * @package Tygh\Addons\PaypalCommercePlatform\Enum
 */
class ProductType
{
    const PAYPAL_COMPLETE_PAYMENTS = 'PPCP';
    const EXPRESS_CHECKOUT = 'EXPRESS_CHECKOUT';
    const PAYPAL_PLUS = 'PPPLUS';
    const PAYPAL_PROFESSIONAL = 'WEBSITE_PAYMENT_PRO';
}
