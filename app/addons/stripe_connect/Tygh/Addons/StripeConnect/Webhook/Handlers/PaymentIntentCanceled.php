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
use Tygh\Addons\StripeConnect\Webhook\Handler;
use Stripe\Event;
use Tygh\Enum\OrderStatuses;
use Tygh\Tygh;

class PaymentIntentCanceled implements Handler
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

            $order_id = (int) $payment_intent->metadata['order_id'];

            /** @var \Tygh\Lock\Factory $lock_factory */
            $lock_factory = Tygh::$app['lock.factory'];
            $lock = $lock_factory->createLock('stripe_webhook_handle_order_status_' . $order_id, 60.0, false);
            if (!$lock->acquire()) {
                do {
                    $lock->wait();
                } while (!$lock->acquire());
            }

            if (fn_check_payment_script('stripe_connect.php', $order_id)) {
                fn_change_order_status($order_id, OrderStatuses::FAILED);
            }

            $lock->release();
        } catch (Exception $e) {
            fn_log_event('general', 'runtime', [
                'message' => __('stripe.webhook_handle_error', [
                    '[error]' => $e->getMessage(),
                ]),
            ]);
        }
    }
}
