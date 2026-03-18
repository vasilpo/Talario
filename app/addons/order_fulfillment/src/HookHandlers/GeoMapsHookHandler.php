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

namespace Tygh\Addons\OrderFulfillment\HookHandlers;

class GeoMapsHookHandler
{
    /**
     * The `geo_maps_get_product_shipping_methods_before_estimation` hook handler.
     *
     * Action performed:
     *    - Marks product that will be delivered by marketplace for geo maps shipping estimations.
     *
     * @param array{company_id: int} $product Information about product.
     *
     * @see ShippingEstimator::getShippingEstimation()
     *
     * @return void
     */
    public function onGetProductShippingMethodsBeforeEstimation(array &$product)
    {
        $product['shipping_by_marketplace'] = fn_are_company_orders_fulfilled_by_marketplace($product['company_id']);
    }
}
