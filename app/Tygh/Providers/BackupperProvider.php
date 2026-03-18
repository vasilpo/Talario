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
use Tygh\Application;
use Tygh\Languages\Languages;
use Tygh\Registry;
use Tygh\Tools\Backup\DatabaseBackupperFactory;
use Tygh\Tools\Formatter;
use Tygh\Web\Antibot;
use Tygh\Web\Antibot\NullDriver;

/**
 * The provider class that registers factories to create backuppers.
 *
 * @package Tygh\Providers
 */
class BackupperProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['backupper.database'] = function ($app) {
            return new DatabaseBackupperFactory();
        };
    }
}
