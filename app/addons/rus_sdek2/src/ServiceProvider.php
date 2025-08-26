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

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\RusSdek2\HookHandlers\CartsHookHandler;
use Tygh\Addons\RusSdek2\HookHandlers\CitiesHookHandler;
use Tygh\Addons\RusSdek2\HookHandlers\CommercemlHookHandler;
use Tygh\Addons\RusSdek2\HookHandlers\OrderFulfillmentHookHandler;
use Tygh\Addons\RusSdek2\HookHandlers\PickupPointsHookHandler;
use Tygh\Addons\RusSdek2\Services\CitiesService;
use Tygh\Addons\RusSdek2\Services\SdekApiDataBuilder;
use Tygh\Addons\RusSdek2\Services\SdekService;
use Tygh\Application;
use Tygh\Registry;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     *
     * @return void
     */
    public function register(Container $app)
    {
        $app['addons.rus_sdek2.hook_handlers.cities'] = static function () {
            return new CitiesHookHandler();
        };

        $app['addons.rus_sdek2.hook_handlers.carts'] = static function (Application $app) {
            return new CartsHookHandler($app);
        };

        $app['addons.rus_sdek2.hook_handlers.pickup_points'] = static function (Application $app) {
            return new PickupPointsHookHandler($app);
        };

        $app['addons.rus_sdek2.hook_handlers.order_fulfillment'] = static function (Application $app) {
            return new OrderFulfillmentHookHandler($app);
        };

        $app['addons.rus_sdek2.hook_handlers.commerceml'] = static function (Application $app) {
            return new CommercemlHookHandler($app);
        };

        $app['addons.rus_sdek2.cities_service'] = static function (Application $app) {
            /** @var \Tygh\Database\Connection $db */
            $db = $app['db'];

            return new CitiesService($db);
        };

        $app['addons.rus_sdek2.sdek_service'] = static function (Application $app) {
            /** @var \Tygh\Database\Connection $db */
            $db = $app['db'];

            return new SdekService($db);
        };

        $app['addons.rus_sdek2.sdek_api_data_builder'] = static function (Application $app) {
            /** @var \Tygh\Database\Connection $db */
            $db = $app['db'];

            /** @var \Tygh\Addons\RusSdek2\Services\SdekService $sdek_service */
            $sdek_service = $app['addons.rus_sdek2.sdek_service'];

            $currencies = Registry::get('currencies');
            $default_currency = CART_PRIMARY_CURRENCY;
            $symbol_grams = (float) Registry::get('settings.General.weight_symbol_grams');
            $company_city = Registry::get('runtime.company_data.city');
            $company_address = Registry::get('runtime.company_data.address');
            $company_name = Registry::get('runtime.company_data.company');
            $currency_sdek = fn_get_schema('sdek', 'currency_sdek', 'php', true);
            $sdek_tariffs = fn_get_schema('sdek', 'sdek_delivery', 'php', true);

            return new SdekApiDataBuilder(
                $db,
                $sdek_service,
                $currencies,
                $default_currency,
                $company_city,
                $company_address,
                $company_name,
                $symbol_grams,
                $currency_sdek,
                $sdek_tariffs
            );
        };
    }
}
