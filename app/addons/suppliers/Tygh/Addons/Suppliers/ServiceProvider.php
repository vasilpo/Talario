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


namespace Tygh\Addons\Suppliers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\Suppliers\Documents\SupplierOrder\Type;

/**
 * Class ServiceProvider is intended to register services and components of the "Suppliers" add-on to the application
 * container.
 *
 * @package Tygh\Addons\Suppliers
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['template.document.supplier_order.type'] = function ($app) {
            return new Type($app['template.document.repository'], $app['db'], $app['template.renderer'], $app['template.variable_collection_factory']);
        };
    }
}