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

namespace Tygh\Addons\DirectPayments;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\DirectPayments\Cart\Service;
use Tygh\Tygh;

class ServiceProvider implements ServiceProviderInterface
{
    /** @inheritdoc */
    public function register(Container $app)
    {
        $app['addons.direct_payments.cart.service'] = function ($app) {

            return new Service($app['session']);
        };
    }

    /**
     * @return \Tygh\Addons\DirectPayments\Cart\Service
     */
    public static function getCartService()
    {
        return Tygh::$app['addons.direct_payments.cart.service'];
    }
}