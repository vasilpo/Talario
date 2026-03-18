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

defined('BOOTSTRAP') or die('Access denied');

/**
 * Provides customer location destination ID for caching purposes.
 *
 * @return string
 *
 * @internal
 */
function fn_warehouses_blocks_get_customer_destination_id()
{
    /** @var \Tygh\Location\Manager $location */
    $location = Tygh::$app['location'];

    return $location->getDestinationId();
}

/**
 * Provides list of stores for the "Availability in stores" block.
 *
 * @return array
 *
 * @internal
 */
function fn_warehouses_blocks_get_availability_in_stores()
{
    $params = array_merge([
        'product_id' => null,
    ], $_REQUEST);

    if (!$params['product_id']) {
        return [];
    }

    /** @var \Tygh\Location\Manager $manager */
    $location_manager = Tygh::$app['location'];
    $destination_id = $location_manager->getDestinationId();
    $availability = fn_warehouses_get_availability_summary($params['product_id'], $destination_id);

    if (!$availability) {
        return $availability;
    }

    return $availability['grouped_stores'];
}
