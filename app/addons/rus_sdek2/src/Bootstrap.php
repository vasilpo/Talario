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

namespace Tygh\Addons\RusSdek2;

use Tygh\Core\ApplicationInterface;
use Tygh\Core\BootstrapInterface;
use Tygh\Core\HookHandlerProviderInterface;

/**
 * This class describes instructions for loading the rus_sdek_2 add-on
 *
 * @package Tygh\Addons\RusSdek2
 */
class Bootstrap implements BootstrapInterface, HookHandlerProviderInterface
{
    /**
     * @inheritDoc
     */
    public function boot(ApplicationInterface $app)
    {
        // FIXME
        require_once 'app/addons/rus_sdek2/lib/vendor/autoload.php';

        fn_define('SDEK2_DEFAULT_DIMENSIONS', 20);
        fn_define('SDEK2_DEFAULT_WEIGHT', 0.1);

        $app->register(new ServiceProvider());
    }

    /**
     * @inheritDoc
     */
    public function getHookHandlerMap()
    {
        return [
            'get_cities_post' => [
                'addons.rus_sdek2.hook_handlers.cities',
                /** @see \Tygh\Addons\RusSdek2\HookHandlers\CitiesHookHandler::onGetCitiesPost() */
                'onGetCitiesPost',
            ],
            'update_city_post' => [
                'addons.rus_sdek2.hook_handlers.cities',
                /** @see \Tygh\Addons\RusSdek2\HookHandlers\CitiesHookHandler::onUpdateCityPost() */
                'onUpdateCityPost',
            ],
            'delete_city_post' => [
                'addons.rus_sdek2.hook_handlers.cities',
                /** @see \Tygh\Addons\RusSdek2\HookHandlers\CitiesHookHandler::onDeleteCityPost() */
                'onDeleteCityPost',
            ],
            'update_cart_by_data_post' => [
                'addons.rus_sdek2.hook_handlers.carts',
                /** @see \Tygh\Addons\RusSdek2\HookHandlers\CartsHookHandler::onUpdateCartByDataPost() */
                'onUpdateCartByDataPost',
            ],
            'calculate_cart_taxes_pre' => [
                'addons.rus_sdek2.hook_handlers.carts',
                /** @see \Tygh\Addons\RusSdek2\HookHandlers\CartsHookHandler::onCalculateCartTaxesPre() */
                'onCalculateCartTaxesPre',
            ],
            'place_suborders_pre' => [
                'addons.rus_sdek2.hook_handlers.carts',
                /** @see \Tygh\Addons\RusSdek2\HookHandlers\CartsHookHandler::onPlaceSubordersPre() */
                'onPlaceSubordersPre',
            ],
            'pre_update_order' => [
                'addons.rus_sdek2.hook_handlers.carts',
                /** @see \Tygh\Addons\RusSdek2\HookHandlers\CartsHookHandler::onPreUpdateOrder() */
                'onPreUpdateOrder',
            ],
            'pickup_point_variable_init' => [
                'addons.rus_sdek2.hook_handlers.pickup_points',
                /** @see \Tygh\Addons\RusSdek2\HookHandlers\PickupPointsHookHandler::onPickupPointVariableInit() */
                'onPickupPointVariableInit',
            ],
            'order_fulfillment_set_marketplace_shipping_to_product_group' => [
                'addons.rus_sdek2.hook_handlers.order_fulfillment',
                /** @see \Tygh\Addons\RusSdek2\HookHandlers\OrderFulfillmentHookHandler::onOrderFulfillmentSetMarketplaceShippingToProductGroup() */
                'onOrderFulfillmentSetMarketplaceShippingToProductGroup',
            ],
            'commerceml_order_storage_update_order_cart_calculation_post' => [
                'addons.rus_sdek2.hook_handlers.commerceml',
                /** @see \Tygh\Addons\RusSdek2\HookHandlers\CommercemlHookHandler::onCommercemlOrderStorageUpdateOrderCartCalculationPost() */
                'onCommercemlOrderStorageUpdateOrderCartCalculationPost',
            ],
        ];
    }
}
