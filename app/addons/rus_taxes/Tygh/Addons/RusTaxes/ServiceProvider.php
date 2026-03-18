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

namespace Tygh\Addons\RusTaxes;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Tygh\Registry;
use Tygh\Tygh;

/**
 * Class ServiceProvider is intended to register services and components of the "rus_taxes" add-on to the application
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
        $app['addons.rus_taxes.receipt_factory'] = function () {
            return new ReceiptFactory(
                CART_PRIMARY_CURRENCY,
                TaxType::getMap(),
                Registry::get('settings.Appearance.cart_prices_w_taxes') === 'Y'
            );
        };

        $app['addons.rus_taxes.digital_marking_service'] = static function () {
            return new DigitalMarkingService();
        };
    }

    /**
     * @return DigitalMarkingService
     */
    public static function getDigitalMarkingService()
    {
        return Tygh::$app['addons.rus_taxes.digital_marking_service'];
    }
}
