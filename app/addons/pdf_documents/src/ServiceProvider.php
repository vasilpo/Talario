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

namespace Tygh\Addons\PdfDocuments;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Addons\PdfDocuments\HookHandlers\OrdersHookHandler;
use Tygh\Addons\PdfDocuments\HookHandlers\ShipmentsHookHandler;
use Tygh\Registry;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers add-on services.
     *
     * @param Container $app Application instance
     *
     * @return void
     */
    public function register(Container $app)
    {
        $app['addons.pdf_documents.hook_handlers.orders'] = static function () {
            return new OrdersHookHandler();
        };

        $app['addons.pdf_documents.hook_handlers.shipments'] = static function () {
            return new ShipmentsHookHandler();
        };

        $app['addons.pdf_documents.service_url'] = static function () {
            $addon_service_url = Registry::get('addons.pdf_documents.service_url');

            $service_url = Registry::ifGet(
                'config.pdf_documents.service_url',
                $addon_service_url
            );

            return rtrim($service_url, '/');
        };
    }
}
