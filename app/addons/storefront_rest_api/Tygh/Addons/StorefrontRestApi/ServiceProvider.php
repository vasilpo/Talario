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

namespace Tygh\Addons\StorefrontRestApi;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Enum\ProfileTypes;
use Tygh\Addons\StorefrontRestApi\ProfileFields\Hydrator;
use Tygh\Addons\StorefrontRestApi\ProfileFields\Manager;
use Tygh\Addons\StorefrontRestApi\ProfileFields\Validator;
use Tygh\Enum\YesNo;
use Tygh\Registry;

/**
 * Class ServiceProvider is intended to register services and components of the "Storefront REST API" add-on to the application
 * container.
 *
 * @package Tygh\Addons\ProductVariations
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['addons.storefront_rest_api.profile_fields.customer_manager'] = static function (Container $app) {
            return new Manager(
                YesNo::toBool(Registry::get('settings.General.quick_registration')),
                Registry::get('settings.Checkout.address_position'),
                $app['addons.storefront_rest_api.profile_fields.validator'],
                $app['addons.storefront_rest_api.profile_fields.hydrator'],
                ProfileTypes::CODE_USER
            );
        };

        $app['addons.storefront_rest_api.profile_fields.vendor_manager'] = static function (Container $app) {
            return new Manager(
                YesNo::toBool(Registry::get('settings.General.quick_registration')),
                Registry::get('settings.Checkout.address_position'),
                $app['addons.storefront_rest_api.profile_fields.validator'],
                $app['addons.storefront_rest_api.profile_fields.hydrator'],
                ProfileTypes::CODE_SELLER
            );
        };

        $app['addons.storefront_rest_api.profile_fields.hydrator'] = static function (Container $app) {
            return new Hydrator();
        };

        $app['addons.storefront_rest_api.profile_fields.validator'] = static function (Container $app) {
            return new Validator();
        };
    }
}
