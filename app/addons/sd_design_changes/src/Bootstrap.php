<?php
/***************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

namespace Tygh\Addons\SdDesignChanges;

use Tygh\Core\ApplicationInterface;
use Tygh\Core\BootstrapInterface;
use Tygh\Core\HookHandlerProviderInterface;

/**
 * Class Bootstrap provides instructions to load the sd_design_changes add-on.
 *
 * @package Tygh\Addons\SdDesignChanges
 */
class Bootstrap implements BootstrapInterface, HookHandlerProviderInterface
{
    /**
     * @inheritDoc
     */
    public function boot(ApplicationInterface $app)
    {
        $app->register(new ServiceProvider());
    }

    /** @inheritDoc */
    public function getHookHandlerMap()
    {
        return [
            'get_cart_product_data' => [
                'addons.sd_design_changes.hook_handlers.cart',
                'getCartProductData',
            ],
            'get_order_items_info_post' => [
                'addons.sd_design_changes.hook_handlers.cart',
                'getOrderItemsInfoPost',
            ],
            'pre_get_cart_product_data' => [
                'addons.sd_design_changes.hook_handlers.cart',
                'getCartProductDataPre',
            ],
            'get_products' => [
                'addons.sd_design_changes.hook_handlers.products',
                'getProducts',
            ],
        ];
    }
}
