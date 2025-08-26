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
            if($product['extra']['booking_info']['booking_type'] == 'T') {
                $this->booking_info =  __("ec_table_booking.booking_date") .':'.date('Y-m-d',$product['extra']['booking_info']['booking_date']).'<br />'.__('ec_table_booking.booking_slot').':'.$product['extra']['booking_info']['booking_slot'].'<br />'.__('ec_table_booking.booking_slot_amount').':'.$product['extra']['booking_info']['booking_slot_amount'];
            }
            else {
                $this->booking_info =  __("ec_table_booking.booking_date") .':'.$product['extra']['booking_info']['from'] .'-'.$product['extra']['booking_info']['to'];

            }
        }
    }
}