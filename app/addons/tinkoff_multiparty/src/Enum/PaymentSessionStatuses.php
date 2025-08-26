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

namespace Tygh\Addons\TinkoffMultiparty\Enum;

defined('BOOTSTRAP') or die('Access denied');

class PaymentSessionStatuses
{
    const AUTHORIZED       = 'AUTHORIZED';
    const CANCELED         = 'CANCELED';
    const CONFIRMED        = 'CONFIRMED';
    const NEW              = 'NEW';
    const PARTIAL_REFUNDED = 'PARTIAL_REFUNDED';
    const PARTIAL_REVERSED = 'PARTIAL_REVERSED';
    const REFUNDED         = 'REFUNDED';
    const REJECTED         = 'REJECTED';
    const REVERSED         = 'REVERSED';

    /**
     * Gets an array of payment session statuses that do not support refunds.
     *
     * @return array<array-key, string>
     */
    public static function getStatusesNotRefund()
    {
        return [self::REJECTED, self::CANCELED, self::REVERSED, self::REFUNDED];
    }

    /**
     * Gets an array of refunded payment session statuses.
     *
     * @return array<array-key, string>
     */
    public static function getStatusesWereRefunded()
    {
        return [self::REVERSED, self::CANCELED, self::REFUNDED, self::PARTIAL_REVERSED, self::PARTIAL_REFUNDED];
    }

    /**
     * Gets an array of payment session statuses for order status paid.
     *
     * @return array<array-key, string>
     */
    public static function getStatusesForOrderPaid()
    {
        return [self::AUTHORIZED, self::CONFIRMED];
    }

    /**
     * Gets an array of payment session statuses for order status canceled.
     *
     * @return array<array-key, string>
     */
    public static function getStatusesForOrderCanceled()
    {
        return [self::REVERSED, self::CANCELED, self::REJECTED, self::REFUNDED];
    }

    /**
     * Gets an array of payment session statuses for send closing receipts.
     *
     * @return array<array-key, string>
     */
    public static function getStatusesForSendClosingReceipts()
    {
        return [self::CONFIRMED];
    }
}
