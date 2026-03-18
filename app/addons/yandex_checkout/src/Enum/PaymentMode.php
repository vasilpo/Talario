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

namespace Tygh\Addons\YandexCheckout\Enum;

/**
 * Class ItemPaymentStatus contains possible values for the `payment_mode` API request field.
 *
 * @package Tygh\Addons\YandexCheckout\Enum
 */
class PaymentMode
{
    const FULL_PREPAYMENT = 'full_prepayment';
    const PARTIAL_PREPAYMENT = 'partial_prepayment';
    const ADVANCE = 'advance';
    const FULL_PAYMENT = 'full_payment';
    const PARTIAL_PAYMENT = 'partial_payment';
    const CREDIT = 'credit';
    const CREDIT_PAYMENT = 'credit_payment';
}