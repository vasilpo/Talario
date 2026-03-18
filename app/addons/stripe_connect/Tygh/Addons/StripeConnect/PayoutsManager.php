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

namespace Tygh\Addons\StripeConnect;

use Tygh\Enum\VendorPayoutApprovalStatuses;
use Tygh\Enum\VendorPayoutTypes;
use Tygh\Enum\YesNo;
use Tygh\VendorPayouts;

class PayoutsManager
{
    /** @var int $company_id */
    protected $company_id;

    /** @var \Tygh\VendorPayouts $manager */
    protected $manager;

    /**
     * PayoutsManager constructor.
     *
     * @param int $company_id Vendor ID to instantate payouts manager for
     */
    public function __construct($company_id)
    {
        $this->company_id = $company_id;
        $this->manager = VendorPayouts::instance(array('vendor' => $company_id));
    }

    /**
     * Creates withdrawal when an order is paid.
     *
     * @param float  $amount   Withdrawal amount
     * @param int    $order_id Order ID
     * @param string $comment  Comment
     *
     * @return int Withdrawal ID
     */
    public function createWithdrawal($amount, $order_id, $comment = '')
    {
        $params = [
            'company_id'      => $this->company_id,
            'payout_type'     => VendorPayoutTypes::WITHDRAWAL,
            'approval_status' => VendorPayoutApprovalStatuses::COMPLETED,
            'payout_amount'   => $amount,
            'comments'        => $comment ? $comment : __('stripe_connect.withdrawal_for_the_order', [
                '[order_id]' => $order_id,
            ]),
        ];

        return $this->manager->update($params);
    }

    /**
     * Gets order commission value.
     *
     * @param int $order_id Order ID
     *
     * @return float Commission value
     */
    public function getOrderFee($order_id)
    {
        $commission = $this->manager->getSimple(array(
            'order_id'    => $order_id,
            'payout_type' => VendorPayoutTypes::ORDER_PLACED,
        ));

        if (!$commission) {
            return 0;
        }

        $commission = reset($commission);

        $fee = $commission['commission_type'] == 'P' ? $commission['commission_amount'] : $commission['commission'];

        return $fee;
    }

    /**
     * Updates refund info when an order is refunded.
     *
     * @param int|float $amount   Refund amount
     * @param int       $order_id Order ID
     *
     * @return int|void Refund ID
     */
    public function updateRefund($amount, $order_id)
    {
        $params = [
            'exclude_from_balance' => YesNo::YES,
            'comments'             => __('stripe_connect.refunded_via_stripe')
        ];

        $refund_payout_id = 0;
        $amount = -$amount;

        $refund_payouts = $this->manager->getSimple([
            'payout_type'  => VendorPayoutTypes::ORDER_REFUNDED,
            'order_id'     => $order_id
        ]);

        foreach ($refund_payouts as $refund_payout) {
            if (empty($refund_payout['details']) || empty($refund_payout['order_amount'])) {
                continue;
            }
            $details = unserialize($refund_payout['details']);

            if (!isset($details['order_products_discount'])) {
                $details['order_products_discount'] = 0;
            }

            if ((float) ($refund_payout['order_amount'] - $details['order_products_discount']) !== (float) $amount) {
                continue;
            }

            $refund_payout_id = !empty($refund_payout['payout_id']) ? $refund_payout['payout_id'] : 0;
            break;
        }

        if (empty($refund_payout_id)) {
            return;
        }

        return $this->manager->update($params, $refund_payout_id);
    }

    /**
     * Adds stripe fee to order placed payout extra.
     *
     * @param int       $order_id             Order ID
     * @param int|float $stripe_fee           Stripe fee
     * @param string    $stripe_currency_code Stripe fee currency code
     *
     * @return int Refund ID
     */
    public function addOrderPayoutStripeFee($order_id, $stripe_fee, $stripe_currency_code): int
    {
        $payout = $this->manager->getSimple([
            'order_id'    => $order_id,
            'payout_type' => VendorPayoutTypes::ORDER_PLACED
        ]);

        if (!$payout || !$stripe_fee) {
            return 0;
        }

        $payout = reset($payout);
        $payout['extra'] = json_decode($payout['extra'], true);
        $payout['extra']['application_stripe_fee'] = $stripe_fee;
        $payout['extra']['stripe_currency_code'] = $stripe_currency_code;
        $payout['extra'] = json_encode($payout['extra']);

        return $this->manager->update(['extra' => $payout['extra']], $payout['payout_id']);
    }
}
