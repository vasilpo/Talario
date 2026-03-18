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
use Tygh\SoftwareProductEnvironment;

/**
 * Class EnvironmentProvider is intended to register environment-related services into the Application container.
 *
 * @package Tygh\Providers
 */
class EnvironmentProvider implements \Pimple\ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['product.env'] = function (Container $app) {
            $store_mode = fn_get_storage_data('store_mode');

            $licensing_environment = new SoftwareProductEnvironment(
                PRODUCT_NAME,
                PRODUCT_VERSION,
                $store_mode,
                PRODUCT_STATUS,
                PRODUCT_BUILD,
                PRODUCT_EDITION,
                PRODUCT_RELEASE_TIMESTAMP
            );

            return $licensing_environment;
        };
    }
}
