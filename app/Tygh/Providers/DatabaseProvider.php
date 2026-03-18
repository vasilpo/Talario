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
use Tygh\Exceptions\DatabaseException;
use Tygh\Database\Connection;
use Tygh\Registry;

/**
 * Class DatabaseProvider is used to register database components at Application container.
 */
class DatabaseProvider implements \Pimple\ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        // Database component
        $app['db'] = function ($app) {
            $database = new Connection($app['db.driver']);

            $connected = $database->connect(
                Registry::get('config.db_user'),
                Registry::get('config.db_password'),
                Registry::get('config.db_host'),
                Registry::get('config.db_name'),
                array(
                    'table_prefix' => Registry::get('config.table_prefix')
                )
            );

            if ($connected) {
                Registry::set('runtime.database.skip_errors', false);
            } else {
                throw new DatabaseException('Cannot connect to the database server');
            }

            return $database;
        };

        // Database driver instance
        $app['db.driver'] = function ($app) {
            return new $app['db.driver.class'];
        };

        $app['db.driver.class'] = function ($app) {
            $driver_class = Registry::ifGet('config.database_backend', 'mysqli');
            $driver_class = '\\Tygh\\Backend\\Database\\' . ucfirst($driver_class);

            return $driver_class;
        };
    }
}
