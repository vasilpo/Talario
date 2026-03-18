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

namespace Tygh\Addons\VendorPrivileges;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider is intended to register services and components of the "Vendor Privileges" add-on to the application
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
        $app['addons.vendor_privileges.privileges'] = function(Container $app) {
            return self::createPrivileges();
        };
    }

    /**
     * @return \Tygh\Addons\VendorPrivileges\Privileges
     */
    public static function createPrivileges()
    {
        $vendor_schema = fn_get_permissions_schema('vendor');
        $admin_schema = fn_get_permissions_schema('admin');

        return new Privileges($admin_schema, $vendor_schema);
    }
}
