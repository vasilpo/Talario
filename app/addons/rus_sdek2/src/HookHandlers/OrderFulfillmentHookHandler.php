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

namespace Tygh\Addons\RusSdek2\HookHandlers;

use Tygh\Application;

/**
 * This class describes the hook handlers related to order fulfillment add-on
 *
 * @package Tygh\Addons\RusSdek2\HookHandlers
 */
class OrderFulfillmentHookHandler
{
    /** @var Application */
    protected $application;

    /**
     * @param Application $application Application object
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * The "order_fulfillment_set_marketplace_shipping_to_product_group" hook handler.
     *
     * Actions performed:
     * - Adds selected office data to all product groups which are using marketplace shipping
     *
     * @param array<string> $cart                  Cart data
     * @param int           $group_key             Group key
     * @param int           $marketplace_group_key Marketplace group key
     * @param array<string> $chosen_shipping_data  Chosen shipping method data
     *
     * @see \onPrePlaceOrder()
     *
     * @return void
     */
    public function onOrderFulfillmentSetMarketplaceShippingToProductGroup(array &$cart, $group_key, $marketplace_group_key, array $chosen_shipping_data)
    {
        if (empty($cart['select_office'][$marketplace_group_key])) {
            return;
        }
        $marketplace_group_selected_office_data = $cart['select_office'][$marketplace_group_key][$chosen_shipping_data['shipping_id']];
        $cart['select_office'][$group_key][$chosen_shipping_data['shipping_id']] = $marketplace_group_selected_office_data;
    }
}
