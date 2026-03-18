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
