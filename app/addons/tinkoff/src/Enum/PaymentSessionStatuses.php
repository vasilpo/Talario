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

namespace Tygh\Addons\Tinkoff\Enum;

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
}
