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

if ($mode === 'shipping_estimation') {
    $params = array_merge([
        'product_id'          => null,
    ], $_REQUEST);

    /** @var \Tygh\Location\Manager $manager */
    $location_manager = Tygh::$app['location'];
    $destination_id = $location_manager->getDestinationId();
    $availability = fn_warehouses_get_availability_summary($params['product_id'], $destination_id);

    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];
    $view->assign($availability);
}

return [CONTROLLER_STATUS_OK];
