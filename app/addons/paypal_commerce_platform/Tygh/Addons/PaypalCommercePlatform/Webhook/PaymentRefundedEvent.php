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
use Tygh\Addons\PaypalCommercePlatform\PayoutsManager;
use Tygh\Registry;

class PaymentRefundedEvent extends Event implements PaymentCaptureEventInterface
{
    /**
     * @var \Tygh\Addons\PaypalCommercePlatform\Webhook\PaymentCapture $capture
     */
    protected $capture;

    /** @inheritDoc */
    public function getCapture()
    {
        if ($this->capture === null) {
            $platform_fee = isset($this->getResource()->seller_payable_breakdown->platform_fees[0]->amount->value)
                ? $this->getResource()->seller_payable_breakdown->platform_fees[0]->amount->value
                : 0;
            $this->capture = new PaymentCapture(
                $this->getResource()->id,
                $this->getResource()->status,
                $this->getResource()->custom_id,
                $this->getResource()->amount->value,
                $platform_fee
            );
        }

        if ($this->capture->getCompanyId()) {
            $payouts_manager = new PayoutsManager((int) $this->capture->getCompanyId());
            $payouts_manager->updateRefund($this->capture->getTotal(), $this->capture->getOrderId());
        }

        return $this->capture;
    }

    /** @inheritDoc */
    public function handle(PaypalCommercePlatform $processor)
    {
        $order_status = Registry::get('addons.paypal_commerce_platform.rma_refunded_order_status');

        return [
            'reason_text'                        => $this->getSummary(),
            'order_status'                       => $order_status,
            'paypal_commerce_platform.refund_id' => $this->getResource()->id,
        ];
    }
}
