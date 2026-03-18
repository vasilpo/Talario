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


namespace Tygh\Addons\GiftCertificates;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\GiftCertificates\Documents\GiftCertificate\Type;

/**
 * Class ServiceProvider is intended to register services and components of the "gift_certificates" add-on to the application
 * container.
 *
 * @package Tygh\Addons\GiftCertificates
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['template.document.gift_certificate.type'] = function ($app) {
            return new Type($app['template.document.repository'], $app['db'], $app['template.renderer'], $app['template.variable_collection_factory']);
        };
    }
}