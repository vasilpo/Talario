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
use Pimple\ServiceProviderInterface;
use Tygh\Gdpr\DataCollector\UserDataCollector;
use Tygh\Gdpr\DataModifier\UserPersonalDataAnonymizer;
use Tygh\Gdpr\DataUpdater\UserPersonalDataUpdater;
use Tygh\Gdpr\SchemaManager;

/**
 * Class ServiceProvider is intended to register services and components of the "GDPR" add-on to the application
 * container.
 *
 * @package Tygh\Gdpr
 */
class GdprServiceProvider implements ServiceProviderInterface
{
    /**
     * @return void
     */
    public function register(Container $app)
    {
        $app['gdpr.schema_manager'] = static function (Container $app) {
            return new SchemaManager();
        };

        $app['gdpr.user_data_collector'] = static function (Container $app) {
            return new UserDataCollector($app['gdpr.schema_manager']);
        };

        $app['gdpr.anonymizer'] = static function (Container $app) {
            return new UserPersonalDataAnonymizer(
                $app['gdpr.schema_manager']
            );
        };

        $app['gdpr.user_data_updater'] = static function (Container $app) {
            return new UserPersonalDataUpdater(
                $app['gdpr.schema_manager']
            );
        };
    }
}
