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
use Tygh\Marketplace\Client;
use Tygh\Registry;
use Tygh\Tygh;

class MarketplaceProvider implements ServiceProviderInterface
{
    /**
     * @inheritdoc
     *
     * @return void
     */
    public function register(Container $app)
    {
        $app['marketplace.client'] = static function (Container $app) {
            return new Client(
                Registry::get('config.resources.marketplace_url'),
                Registry::get('settings.Upgrade_center.license_number'),
                HelpdeskProvider::getAuthService()->getExternalUserId((int) $app['session']['auth']['user_id'])
            );
        };
    }

    /**
     * Gets CS-Cart Marketplace API Client.
     *
     * @return Client
     */
    public static function getClient()
    {
        return Tygh::$app['marketplace.client'];
    }
}
