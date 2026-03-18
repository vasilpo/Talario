<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

namespace Tygh\Addons\OrderFulfillment;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\OrderFulfillment\HookHandlers\CartHookHandler;
use Tygh\Addons\OrderFulfillment\HookHandlers\CheckoutHookHandler;
use Tygh\Addons\OrderFulfillment\HookHandlers\CompaniesHookHandler;
use Tygh\Addons\OrderFulfillment\HookHandlers\GeoMapsHookHandler;
use Tygh\Addons\OrderFulfillment\HookHandlers\OrdersHookHandler;
use Tygh\Addons\OrderFulfillment\HookHandlers\PromotionsHookHandler;
use Tygh\Addons\OrderFulfillment\HookHandlers\ShippingsHookHandler;
use Tygh\Addons\OrderFulfillment\HookHandlers\StoreLocatorHookHandler;
use Tygh\Addons\OrderFulfillment\HookHandlers\VendorPlansHookHandler;
use Tygh\Addons\OrderFulfillment\HookHandlers\OrderManagementHookHandler;
use Tygh\Application;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     *
     * @return void
     */
    public function register(Container $app)
    {
        $app['addons.order_fulfillment.hook_handlers.companies'] = static function (Application $app) {
            return new CompaniesHookHandler();
        };

        $app['addons.order_fulfillment.hook_handlers.orders'] = static function (Application $app) {
            return new OrdersHookHandler();
        };

        $app['addons.order_fulfillment.hook_handlers.shippings'] = static function (Application $app) {
            return new ShippingsHookHandler();
        };

        $app['addons.order_fulfillment.hook_handlers.vendor_plans'] = static function (Application $app) {
            return new VendorPlansHookHandler();
        };

        $app['addons.order_fulfillment.hook_handlers.checkout'] = static function (Application $app) {
            return new CheckoutHookHandler();
        };

        $app['addons.order_fulfillment.hook_handlers.promotions'] = static function (Application $app) {
            return new PromotionsHookHandler();
        };

        $app['addons.order_fulfillment.hook_handlers.store_locator'] = static function (Application $app) {
            return new StoreLocatorHookHandler();
        };

        $app['addons.order_fulfillment.hook_handlers.cart'] = static function () {
            return new CartHookHandler();
        };

        $app['addons.order_fulfillment.hook_handlers.geo_maps'] = static function () {
            return new GeoMapsHookHandler();
        };

        $app['addons.order_fulfillment.hook_handlers.order_management'] = static function (Application $app) {
            return new OrderManagementHookHandler();
        };
    }
}
