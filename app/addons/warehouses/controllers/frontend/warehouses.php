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

use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\YesNo;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'stock_availability') {
    $show_stock_availability_with_shipping_estimation = Registry::get('addons.geo_maps.status') === ObjectStatuses::ACTIVE
        && Registry::get('addons.geo_maps.show_shippings_on_product') === YesNo::YES;

    if ($show_stock_availability_with_shipping_estimation) {
        return [CONTROLLER_STATUS_OK];
    }

    $params = array_merge([
        'product_id'          => null,
    ], $_REQUEST);

    /** @var \Tygh\Location\Manager $location_manager */
    $location_manager = \Tygh::$app['location'];
    $destination_id = $location_manager->getDestinationId();
    $availability = fn_warehouses_get_availability_summary($params['product_id'], $destination_id);

    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];
    $view->assign($availability);
    $view->assign('location', $location_manager->getLocation()->toArray());
}

return [CONTROLLER_STATUS_OK];
