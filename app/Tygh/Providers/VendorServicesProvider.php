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

use Tygh\Tygh;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Vendors\Invitations\Repository;
use Tygh\Vendors\Invitations\Sender;

/**
 * The provider class that registers the vendor invites repository in the Tygh::$app container.
 *
 * @package Tygh\Providers
 */
class VendorServicesProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['vendors.invitations.repository'] = function ($app) {
            return new Repository($app['db']);
        };

        $app['vendors.invitations.sender'] = function ($app) {
            return new Sender($app['db'], $app['vendors.invitations.repository'], $app['mailer']);
        };
    }

    /**
     * @return \Tygh\Vendors\Invitations\Repository
     */
    public static function getInvitationsRepository()
    {
        return Tygh::$app['vendors.invitations.repository'];
    }

    /**
     * @return \Tygh\Vendors\Invitations\Sender
     */
    public static function getInvitationsSender()
    {
        return Tygh::$app['vendors.invitations.sender'];
    }
}
