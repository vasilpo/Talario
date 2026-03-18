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

use Tygh\Addons\VendorLocations\Dto\Region;

if (!defined('BOOTSTRAP')) { exit('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return array(CONTROLLER_STATUS_OK);
}

if ($mode === 'catalog') {
    $params = $_REQUEST;
    if (empty($params['location_filter'])) {
        return array(CONTROLLER_STATUS_OK);
    }

    /** @var \Tygh\Addons\VendorLocations\Dto\Region $vendors_search_location */
    $vendors_search_location = Region::createFromHash($params['location_filter']);

    Tygh::$app['view']->assign('vendors_search_location_place_id', $vendors_search_location->getPlaceId());
}
