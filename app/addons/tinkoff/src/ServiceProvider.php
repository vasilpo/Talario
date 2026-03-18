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

namespace Tygh\Addons\Tinkoff;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\Tinkoff\HookHandlers\OrdersHookHandler;
use Tygh\Addons\Tinkoff\HookHandlers\PaymentsHookHandler;
use Tygh\Application;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['addons.tinkoff.hook_handlers.orders'] = static function (Application $application) {
            return new OrdersHookHandler();
        };

        $app['addons.tinkoff.hook_handlers.payments'] = static function (Application $application) {
            return new PaymentsHookHandler();
        };
    }
}
