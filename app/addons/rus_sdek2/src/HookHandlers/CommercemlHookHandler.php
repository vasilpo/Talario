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
 * This class describes the hook handlers related to CommerceML addon
 *
 * @package Tygh\Addons\RusSdek2\HookHandlers
 */
class CommercemlHookHandler
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
     * FIXME: Remove hook handler when pickup points logic moved to core.
     * The "commerceml_order_storage_update_order_cart_calculation_post" hook handler.
     *
     * Actions performed:
     *     - Adds selected pickup point data from old order to cart contents
     *
     * @param array $cart       Cart contents
     * @param array $order_data Order data
     *
     * @see \Tygh\Addons\CommerceML\Storages\OrderStorage::updateOrder()
     *
     * @return void
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
     */
    public function onCommercemlOrderStorageUpdateOrderCartCalculationPost(array &$cart, array $order_data)
    {
        if (empty($cart['product_groups'])) {
            return;
        }

        foreach ($cart['product_groups'] as &$product_group) {
            foreach ($product_group['chosen_shippings'] as &$shipping) {
                if ($shipping['module'] !== 'sdek2') {
                    continue;
                }

                foreach ($order_data['shipping'] as $old_order_shipping) {
                    if ($shipping['shipping_id'] !== $old_order_shipping['shipping_id']) {
                        continue;
                    }

                    $shipping['office_id'] = $old_order_shipping['office_id'] ?? null;
                    $shipping['office_data'] = $old_order_shipping['office_data'] ?? null;
                }
            }
            unset($shipping);
        }
        unset($product_group);
    }
}
