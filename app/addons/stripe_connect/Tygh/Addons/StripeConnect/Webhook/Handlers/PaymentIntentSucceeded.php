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

namespace Tygh\Addons\StripeConnect\Webhook\Handlers;

use Exception;
//phpcs:ignore
use Stripe\PaymentIntent;
use Tygh\Addons\StripeConnect\ServiceProvider;
use Tygh\Addons\StripeConnect\Webhook\Handler;
use Stripe\Event;
use Tygh\Enum\OrderStatuses;
use Tygh\Tygh;

class PaymentIntentSucceeded implements Handler
{
    /**
     * Handles the payment_intent.succeeded event
     *
     * @param Event $event Stripe event
     *
     * @return void
     */
    public function handle(Event $event)
    {
        /** @var PaymentIntent $payment_intent */
        $payment_intent = $event->data->object;

        try {
            if (
                empty($payment_intent->metadata['order_id'])
                || empty($payment_intent->metadata['payment_type'])
                || $payment_intent->metadata['payment_type'] !== 'stripe_connect'
            ) {
                return;
            }

            /** @var array $order_info */
            $order_info = fn_get_order_info((int) $payment_intent->metadata['order_id']);
            $orders = [$order_info];

            if ($order_info['status'] === OrderStatuses::PARENT) {
                /** @var array $orders */
                $orders = fn_get_suborders_info((int) $payment_intent->metadata['order_id']);
            }

            foreach ($orders as $order) {
                $order_id = (int) $order['order_id'];
                /** @var array $order */
                $order = fn_get_order_info($order_id);

                /** @var \Tygh\Lock\Factory $lock_factory */
                $lock_factory = Tygh::$app['lock.factory'];
                $lock = $lock_factory->createLock('stripe_webhook_handle_order_status_' . $order_id, 60.0, false);
                if (!$lock->acquire()) {
                    do {
                        $lock->wait();
                    } while (!$lock->acquire());
                }

                if (
                    !in_array($order['status'], fn_get_settled_order_statuses())
                    && !$this->isOrderProcessed($order)
                ) {
                    fn_change_order_status($order_id, OrderStatuses::PAID);

                    $processor = ServiceProvider::getProcessorFactory()->getByPaymentId($order['payment_id']);
                    $processor->chargeWith3DSecure($order);

                    $this->setOrderProcessed($event, $order_id);
                }

                $lock->release();
            }

            if (isset($processor)) {
                $processor->addOrderTransactionsStripeCommissionInfo($order_info);
            }
        } catch (Exception $e) {
            fn_log_event('general', 'runtime', [
                'message' => __('stripe.webhook_handle_error', [
                    '[error]' => $e->getMessage(),
                ]),
            ]);
        }
    }

    /**
     * @param Event $event    Stripe event
     * @param int   $order_id Order id
     */
    protected function setOrderProcessed(Event $event, int $order_id): void
    {
        fn_update_order_payment_info(
            $order_id,
            ['stripe_connect.webhook_event_id' => $event->id]
        );
    }

    /**
     * phpcs:disable Squiz.Commenting.FunctionComment.MissingParamName
     *
     * @param array{payment_info: array{"stripe_connect.webhook_event_id"?: string}} $order_info Order info
     */
    protected function isOrderProcessed(array $order_info): bool
    {
        return !empty($order_info['payment_info']['stripe_connect.webhook_event_id']);
    }
}
