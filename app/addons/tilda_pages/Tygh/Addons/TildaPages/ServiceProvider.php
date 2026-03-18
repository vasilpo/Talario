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

namespace Tygh\Addons\TildaPages;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\TildaApi\TildaClient;
use Tygh\Registry;
use Tygh\Tygh;

/**
 * Class ServiceProvider is intended to register services and components of the "Landing pages from Tilda" add-on to the application container.
 *
 * @package Tygh\Addons\TildaPages
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
        $app['addons.tilda_pages.tilda_client'] = static function (Container $app) {
            return new TildaClient(
                Registry::get('addons.tilda_pages.tilda_public_api_key'),
                Registry::get('addons.tilda_pages.tilda_private_api_key'),
                Registry::get('addons.tilda_pages.tilda_project_id')
            );
        };
    }

    /**
     * @return \Tygh\Addons\TildaApi\TildaClient
     */
    public static function getTildaClient()
    {
        return Tygh::$app['addons.tilda_pages.tilda_client'];
    }
}
