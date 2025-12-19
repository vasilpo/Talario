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

namespace Tygh\Addons\EcTableBookingSystem\Documents\Order;


use Tygh\Template\IVariable;
use Tygh\Template\Snippet\Table\ItemContext;
use Tygh\Tools\Formatter;

/**
 * Class RewardPointProductVariable
 * @package Tygh\Addons\RewarPoints\Documents\Order
 */
class TableBookingProductVariable implements IVariable
{
    public $booking_info;

    /**
     * TableBookingProductVariable Variable constructor.
     *
     * @param ItemContext   $context    Instance of table column context.
     * @param Formatter     $formatter  Instance of
     */
    public function __construct(ItemContext $context, Formatter $formatter)
    {
        $product = $context->getItem();
        if (!empty($product['extra']['booking_info'])) {
            $booking_info = [
                '<b>' . __('ec_table_booking_system.booking_info') . ':</b>',
            ];
            if (!empty($product['extra']['booking_info']['address'])) {
                $booking_info[] = __('ec_table_booking_system.booking_address') . ': ' . $product['extra']['booking_info']['address'];
            }
            if ($product['extra']['booking_info']['booking_type'] == 'T') {
                $booking_info[] = __('ec_table_booking_system.booking_date') . ': ' . date('Y-m-d',$product['extra']['booking_info']['booking_date']);
                $booking_info[] = __('ec_table_booking_system.booking_slot') . ': ' . $product['extra']['booking_info']['booking_slot'];
                $booking_info[] = __('ec_table_booking_system.booking_table_amount') . ': ' . $product['extra']['booking_info']['booking_slot_amount'];
            } else {
                $booking_info[] = __('ec_table_booking_system.booking_date') . ': ' . $product['extra']['booking_info']['from'] . '-' . $product['extra']['booking_info']['to'];
            }

            $this->booking_info = implode('<br/>', $booking_info);
        }
    }
}
