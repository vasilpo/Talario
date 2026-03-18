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

use Tygh\Models\VendorPlan;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'update' || $mode === 'add') {
    $id = 0;
    if ($mode === 'update') {
        $plan = VendorPlan::model()->find($_REQUEST['plan_id'], ['get_companies_count' => true]);
        if (!$plan) {
            return [CONTROLLER_STATUS_NO_PAGE];
        }
        Tygh::$app['view']->assign('plan', $plan);
        if ($plan instanceof VendorPlan) {
            $id = $plan->plan_id;
        }
    }
    $new_tabs = [];
    $navigation_tabs = Registry::get('navigation.tabs');
    foreach ($navigation_tabs as $key => $tab) {
        $new_tabs[$key] = $tab;
        if ($key === 'plan_general_' . $id) {
            $new_tabs['plan_shippings_' . $id] = [
                'title' => __('shipping'),
                'js' => true,
            ];
        }
    }
    Registry::set('navigation.tabs', $new_tabs);
}
