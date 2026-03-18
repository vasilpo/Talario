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

use Tygh\Enum\YesNo;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'update') {
    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];
    /** @var array $shipping */
    $shipping = $view->getTemplateVars('shipping');
    /** @var array $services */
    $services = $view->getTemplateVars('services');
    $module = null;
    if (!empty($shipping['service_id']) && !empty($services[$shipping['service_id']]['module'])) {
        $module = $services[$shipping['service_id']]['module'];
    }

    if ($module === 'store_locator') {
        $show_store_locator_configure_tab = isset($_REQUEST['show_all_settings']);
        if (!$show_store_locator_configure_tab) {
            $tabs = Registry::ifGet('navigation.tabs', []);
            unset($tabs['configure']['href']);
            $tabs['configure']['hidden'] = YesNo::YES;
            Registry::set('navigation.tabs', $tabs);
        }

        $view->assign('show_store_locator_configure_tab', $show_store_locator_configure_tab);
        $view->assign('store_locator_shipping', true);
    }
}

if ($mode == 'configure') {

    if (!empty($_REQUEST['shipping_id'])) {

        $module = !empty($_REQUEST['module']) ? $_REQUEST['module'] : '';
        if ($module == 'store_locator') {

            $shipping = Tygh::$app['view']->getTemplateVars('shipping');

            $params = [];
            if (fn_allowed_for('MULTIVENDOR') && !empty($shipping['company_id'])) {
                $params['company_id'] = $shipping['company_id'] ?: Registry::get('runtime.company_id');
            }
            list($locations, $params) = fn_get_store_locations($params);

            $active_stores = array();
            if (!empty($shipping['service_params']['active_stores']) && is_array($shipping['service_params']['active_stores'])) {
                $_active_stores = $shipping['service_params']['active_stores'];

                foreach($_active_stores as $store_location_id) {
                    $active_stores[$store_location_id] = $locations[$store_location_id]['city'] . ' (' . $locations[$store_location_id]['name'] .')';
                }
            }

            if (!empty($locations)) {
                $stores = $all_stores = $select_stores = array();

                foreach ($locations as $location) {
                    $available_for_pickup = $location['main_destination_id'] !== null;
                    if ($available_for_pickup) {
                        $result = array_search($location['store_location_id'], $active_stores);
                        if ($result === false) {
                            $select_stores[$location['store_location_id']] = $location['city'] . ' (' . $location['name'] .')';
                        }
                        $all_stores[$location['store_location_id']] = $location['city'] . ' (' . $location['name'] .')';
                    }
                }

                asort($select_stores);
                asort($active_stores);

                Tygh::$app['view']->assign('all_stores', $all_stores);
                Tygh::$app['view']->assign('select_stores', $select_stores);
                Tygh::$app['view']->assign('active_stores', $active_stores);
            }
        }
    }
}
