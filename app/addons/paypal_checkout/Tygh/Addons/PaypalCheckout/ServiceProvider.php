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

namespace Tygh\Addons\PaypalCheckout;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Registry;
use Tygh\Tygh;

/**
 * Class OAuthHelperProvider
 *
 * @package Tygh\Addons\PaypalCheckout\Providers
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @return \Tygh\Addons\PaypalCheckout\ProcessorFactory
     */
    public static function getProcessorFactory()
    {
        return Tygh::$app['addons.paypal_checkout.processor.factory'];
    }

    /**
     * @inheritDoc
     *
     * @return void
     */
    public function register(Container $app)
    {
        $app['addons.paypal_checkout.processor.factory'] = static function (Container $app) {
            return new ProcessorFactory(
                $app['db'],
                fn_get_schema('paypal_checkout', 'status_conversion'),
                Registry::get('settings.Checkout.tax_calculation')
            );
        };
    }
}
