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


namespace Tygh\Addons\Rma;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\Rma\Documents\PackingSlip\Type;

/**
 * Class ServiceProvider is intended to register services and components of the "Rma" add-on to the application
 * container.
 *
 * @package Tygh\Addons\Rma
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['template.document.rma_packing_slip.type'] = function ($app) {
            return new Type($app['template.document.repository'], $app['db'], $app['template.renderer'], $app['template.variable_collection_factory']);
        };
    }
}