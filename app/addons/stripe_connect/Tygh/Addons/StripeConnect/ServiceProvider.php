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

namespace Tygh\Addons\StripeConnect;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\StripeConnect\Webhook\Handlers\PaymentIntentCanceled;
use Tygh\Addons\StripeConnect\Webhook\Handlers\PaymentIntentSucceeded;
use Tygh\Enum\YesNo;
use Tygh\Addons\StripeConnect\Payments\StripeConnect;
use Tygh\Addons\StripeConnect\Webhook\Handlers\AccountApplicationDeauthorized;
use Tygh\Registry;
use Tygh\Tygh;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['addons.stripe_connect.oauth_helper'] = function(Container $app) {
            return new OAuthHelper(
                StripeConnect::getProcessorParameters(),
                fn_url('companies.stripe_connect_auth')
            );
        };

        $app['addons.stripe_connect.account_helper'] = static function (Container $app) {
            return new AccountHelper(
                StripeConnect::getProcessorParameters()
            );
        };

        $app['addons.stripe_connect.price_formatter'] = function (Container $app) {
            return new PriceFormatter($app['formatter']);
        };

        $app['addons.stripe_connect.settings'] = function (Container $app) {
            return Registry::ifGet('addons.stripe_connect', []);
        };

        // Webhook handlers
        $app['addons.stripe_connect.webhook_handler.account.application.deauthorized'] = static function (Container $app) {
            return new AccountApplicationDeauthorized();
        };

        $app['addons.stripe_connect.webhook_handler.payment_intent.succeeded'] = static function (Container $app) {
            return new PaymentIntentSucceeded();
        };

        $app['addons.stripe_connect.webhook_handler.payment_intent.canceled'] = static function (Container $app) {
            return new PaymentIntentCanceled();
        };

        $app['addons.stripe_connect.processor.factory'] = static function (Container $app) {
            return new ProcessorFactory(
                $app['db'],
                $app['addons.stripe_connect.price_formatter'],
                $app['addons.stripe_connect.settings']
            );
        };
    }

    /**
     * @return \Tygh\Addons\StripeConnect\ProcessorFactory
     */
    public static function getProcessorFactory()
    {
        return Tygh::$app['addons.stripe_connect.processor.factory'];
    }

    /**
     * @return \Tygh\Addons\StripeConnect\AccountHelper
     */
    public static function getAccountHelper()
    {
        return Tygh::$app['addons.stripe_connect.account_helper'];
    }

    /**
     * @return \Tygh\Addons\StripeConnect\OAuthHelper
     */
    public static function getOAuthHelper()
    {
        return Tygh::$app['addons.stripe_connect.oauth_helper'];
    }
}
