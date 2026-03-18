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

use Tygh\Tygh;

/** @var string $mode */

if ($mode === 'cart' || $mode === 'shipping_estimation') {
    if ($action === 'get_rates') {
        $customer_location = !empty($_REQUEST['customer_location'])
            ? array_map('trim', $_REQUEST['customer_location'])
            : [];
        Tygh::$app['session']['stored_location'] = $customer_location;
    }

    $location = fn_cities_get_location_from_session($mode === 'shipping_estimation');

    if ($location) {
        list($cities,) = fn_cities_get_cities([
            'country_code' => $location['s_country'],
            'state_code'   => $location['s_state'],
            'status'       => 'A',
        ], 0, DESCR_SL);

        Tygh::$app['view']->assign([
            'cities'       => $cities,
            'customer_loc' => $location,
        ]);
    }
}

Tygh::$app['view']->assign(
    'city_autocomplete',
    [
        'url'                  => fn_url('city.autocomplete_city'),
        'city_param'           => 'q',
        'country_param'        => 'check_country',
        'items_per_page_param' => 'items_per_page',
        'items_per_page'       => 50,
    ]
);

return [CONTROLLER_STATUS_OK];
