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

namespace Tygh\Addons\Robokassa;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh;
use Tygh\Addons\Robokassa\HookHandlers\CompaniesHookHandler;
use Tygh\Addons\Robokassa\HookHandlers\PaymentsHookHandler;
use Tygh\Addons\Robokassa\HookHandlers\OrdersHookHandler;
use Tygh\Addons\Robokassa\Factories\ProcessorFactory;

/**
 * Class ServiceProvider is intended to register services and components of the robokassa add-on to the application
 * container.
 *
 * @package Tygh\Addons\Robokassa
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     *
     * @return void
     */
    public function register(Container $app)
    {
        $app['addons.robokassa.hook_handlers.companies'] = static function (Container $app) {
            return new CompaniesHookHandler($app);
        };

        $app['addons.robokassa.hook_handlers.payments'] = static function (Container $app) {
            return new PaymentsHookHandler($app);
        };

        $app['addons.robokassa.hook_handlers.orders'] = static function (Container $app) {
            return new OrdersHookHandler($app);
        };

        $app['addons.robokassa.service'] = static function (Container $app) {
            return new Service();
        };

        $app['addons.robokassa.processor.factory'] = static function (Container $app) {
            return new ProcessorFactory($app['db']);
        };
    }

    /**
     * @return \Tygh\Addons\Robokassa\Service
     */
    public static function getService()
    {
        return Tygh::$app['addons.robokassa.service'];
    }

    /**
     * @return \Tygh\Addons\Robokassa\Factories\ProcessorFactory
     */
    public static function getProcessorFactory()
    {
        return Tygh::$app['addons.robokassa.processor.factory'];
    }
}
