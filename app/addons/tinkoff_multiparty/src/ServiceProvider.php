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

namespace Tygh\Addons\TinkoffMultiparty;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\TinkoffMultiparty\HookHandlers\OrdersHookHandler;
use Tygh\Addons\TinkoffMultiparty\HookHandlers\PaymentsHookHandler;
use Tygh\Addons\TinkoffMultiparty\Services\PayoutsManagerService;
use Tygh\Application;
use Tygh\Enum\ObjectStatuses;
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
        $app['addons.tinkoff_multiparty.hook_handlers.orders'] = static function (Application $application) {
            return new OrdersHookHandler();
        };

        $app['addons.tinkoff_multiparty.hook_handlers.payments'] = static function (Application $application) {
            return new PaymentsHookHandler();
        };

        $app['addons.tinkoff_multiparty.payouts_manager_service'] = static function (Application $application) {
            $can_collect_commission = Registry::get('addons.vendor_plans.status') === ObjectStatuses::ACTIVE;
            return new PayoutsManagerService($can_collect_commission);
        };
    }
}
