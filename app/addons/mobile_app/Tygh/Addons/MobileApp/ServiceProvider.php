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

namespace Tygh\Addons\MobileApp;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\MobileApp\Notifications\Factory;
use Tygh\Languages\Values;
use Tygh\Tygh;

class ServiceProvider implements ServiceProviderInterface
{
    /** @inheritdoc */
    public function register(Container $app)
    {
        $app['addons.mobile_app.notifications.factory'] = function (Container $app) {
            return new Factory();
        };

        $app['addons.mobile_app.translation_manager'] = function (Container $app) {
            return new TranslationManager(new Values(), DEFAULT_LANGUAGE);
        };
    }

    /**
     * @return \Tygh\Addons\MobileApp\TranslationManager
     */
    public static function getTranslationManager()
    {
        return Tygh::$app['addons.mobile_app.translation_manager'];
    }
}