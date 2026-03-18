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

namespace Tygh\Addons\Barcode;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Registry;
use Tygh\Tools\Barcode\Generator;

/**
 * Class ServiceProvider is intended to register services and components of the "Barcode" add-on to the application
 * container.
 *
 * @package Tygh\Addons\Barcode
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['addons.barcode.generator'] = function(Container $app) {
            return new Generator(
                $app['image'],
                Registry::get('config.dir.lib') . 'other/fonts/verdana.ttf'
            );
        };
    }
}
