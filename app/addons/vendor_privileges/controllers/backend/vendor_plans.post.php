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
use Tygh\Models\VendorPlan;
use Tygh\Registry;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'update' || $mode === 'add') {
    $id = 0;
    if ($mode === 'update') {
        $plan = Tygh::$app['view']->getTemplateVars('plan');

        if ($plan instanceof VendorPlan) {
            $id = $plan->plan_id;
        }
    }

    $vendor_usergroups = fn_get_usergroups(
        [
            'type'   => USERGROUP_TYPE_VENDOR,
            'status' => [
                ObjectStatuses::ACTIVE,
                ObjectStatuses::HIDDEN,
            ],
        ],
        DESCR_SL
    );

    if ($vendor_usergroups) {
        $tabs = Registry::get('navigation.tabs');

        $new_tab = [
            'plan_privileges_' . $id => [
                'title' => __('privileges'),
                'js'    => true,
            ]
        ];

        // insert new tab after Restrictions
        $tabs_keys = array_keys($tabs);
        $index = array_search('plan_restrictions_' . $id, $tabs_keys, true);
        $pos = !$index ? 0 : $index + 1;
        $tabs = array_slice($tabs, 0, $pos) + $new_tab + array_slice($tabs, $pos);

        Tygh::$app['view']->assign('vendor_usergroups', $vendor_usergroups);

        Registry::set('navigation.tabs', $tabs);
    }
}
