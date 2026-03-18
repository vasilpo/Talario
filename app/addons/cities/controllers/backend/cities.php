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

use Tygh\Enum\NotificationSeverity;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if (empty($_REQUEST['country_code'])) {
    $_REQUEST['country_code'] = Registry::get('settings.Checkout.default_country');
}

$params = $_REQUEST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'update') {
        if (empty($params['city_data']['state_code'])) {
            fn_set_notification(NotificationSeverity::ERROR, __('error'), __('cities.not_selected_state'));

            return [CONTROLLER_STATUS_REDIRECT, 'cities.manage?state_id=&country_code=' . $params['city_data']['country_code']];
        }

        fn_cities_update_city($params['city_data'], $params['city_id'], DESCR_SL);

        return [
            CONTROLLER_STATUS_OK,
            'cities.manage?state_code=' . $params['city_data']['state_code'] . '&country_code=' . $params['city_data']['country_code']
        ];
    }

    if ($mode === 'm_delete') {
        if (!empty($params['city_ids'])) {
            foreach ($params['city_ids'] as $v) {
                fn_cities_delete_city($v);
            }
        }
    }

    if ($mode === 'delete') {
        if (!empty($_REQUEST['city_id'])) {
            fn_cities_delete_city($params['city_id']);
        }
    }

    $redirect_url = 'cities.manage?country_code=' . $params['country_code'];

    if (!empty($params['state_code'])) {
        $redirect_url .= '&state_code=' . $params['state_code'];
    }

    if (!empty($params['redirect_url'])) {
        $redirect_url = $params['redirect_url'];
    }

    return [CONTROLLER_STATUS_OK, $redirect_url];
}

if ($mode === 'manage') {
    if (empty($_REQUEST['country_code'])) {
        $params['country_code'] = Registry::get('settings.Checkout.default_country');
    }

    list($cities, $search) = fn_cities_get_cities($params, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

    $states = fn_get_all_states();
    $countries = fn_get_simple_countries(false, DESCR_SL);
    if (!empty($cities)) {
        foreach ($cities as $key => $city) {
            $cities[$key]['state_name'] = fn_get_state_name($city['state_code'], $city['country_code'], DESCR_SL);
            $cities[$key]['country_name'] = $countries[$city['country_code']];
        }
    }

    Tygh::$app['view']->assign('countries', $countries);
    Tygh::$app['view']->assign('states', $states);
    Tygh::$app['view']->assign('cities', $cities);
    Tygh::$app['view']->assign('search', $search);
}
