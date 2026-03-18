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

namespace Tygh\Providers;

use Pimple\Container;
use Tygh\ServerEnvironment;

/**
 * Class ServerEnvironmentProvider is used to register server environment components at Application container.
 */
class ServerEnvironmentProvider implements \Pimple\ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        // Environment component
        $app['server.env'] = function ($app) {

            $env = new ServerEnvironment(
                PHP_VERSION,
                PHP_SAPI,
                $app['server.env.ini_vars']
            );

            return $env;
        };

        $app['server.env.ini_vars'] = array();
    }
}
