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

namespace Tygh\Addons\PaypalCommercePlatform\Webhook;

use Tygh\Addons\PaypalCommercePlatform\Payments\PaypalCommercePlatform;

interface PaymentCaptureEventInterface
{
    /**
     * @return \Tygh\Addons\PaypalCommercePlatform\Webhook\PaymentCapture
     */
    public function getCapture();

    /**
     * @param \Tygh\Addons\PaypalCommercePlatform\Payments\PaypalCommercePlatform $processor Processor to perform
     *                                                                                       payment transactions
     *
     * @return array<string, string>
     */
    public function handle(PaypalCommercePlatform $processor);
}
