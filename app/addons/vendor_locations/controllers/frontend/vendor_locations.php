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

use Tygh\Addons\VendorLocations\Dto\Location;

if (!defined('BOOTSTRAP')) { exit('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'set_geolocation') {
        if (defined('AJAX_REQUEST') && !empty($_REQUEST['location']) && !empty($_REQUEST['locality'])) {
            $location = Location::createFromArray($_REQUEST['location']);
            $locality = Location::createFromArray($_REQUEST['locality']);

            fn_set_session_data(VENDOR_LOCATIONS_STORAGE_KEY_GEO_LOCATION, $location->toArray());
            fn_set_session_data(VENDOR_LOCATIONS_STORAGE_KEY_LOCALITY, $locality->toArray());

            /** @var \Tygh\Ajax $ajax */
            $ajax = Tygh::$app['ajax'];
            $ajax->assign('locality', $location->getLocalityText());
        }
    }

    return array(CONTROLLER_STATUS_OK);
}
