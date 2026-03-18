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

namespace Tygh\Addons\PaypalCheckout\Enum;

class WebhookEventType
{
    const PAYMENT_CAPTURE_COMPLETED = 'PAYMENT.CAPTURE.COMPLETED';
    const PAYMENT_CAPTURE_PENDING = 'PAYMENT.CAPTURE.PENDING';
    const PAYMENT_CAPTURE_DENIED = 'PAYMENT.CAPTURE.DENIED';
    const PAYMENT_CAPTURE_REFUNDED = 'PAYMENT.CAPTURE.REFUNDED';

    /**
     * Gets webhook event types that relate to the payment capture.
     *
     * @return array<string>
     */
    public static function getCaptureTypes()
    {
        return [
            self::PAYMENT_CAPTURE_COMPLETED,
            self::PAYMENT_CAPTURE_PENDING,
            self::PAYMENT_CAPTURE_DENIED,
            self::PAYMENT_CAPTURE_REFUNDED
        ];
    }
}
