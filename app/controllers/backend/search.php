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

use Tygh\Registry;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    return;
}

if ($mode == 'results') {
    $params = $_REQUEST;
    $params['objects'] = array_keys(fn_search_get_customer_objects());
    $params['short_info'] = true;

    if (!empty($params['compact']) && $params['compact'] = 'Y') {
        list($objects, $search) = fn_search($params, Registry::get('settings.Appearance.admin_elements_per_page'), CART_LANGUAGE, AREA);
        Tygh::$app['view']->assign('found_objects', $objects);

        if (count($objects) == 1 && !empty($search['detailed_links'][key($objects)])) {
            $data = reset($objects);
            $object = key($objects);

            if ($data['count'] == 1) {
                return array(CONTROLLER_STATUS_REDIRECT, str_replace('%id%', $data['id'], $search['detailed_links'][$object]));
            }
        }

        $tabs = array();

        foreach ($objects as $object => $data) {
            $tabs['manage_' . $object] = array(
                'title' => $search['titles'][$object] . ' (' . $data['count'] . ')',
                'href' => $search['action_links'][$object],
                'ajax' => true
            );
        }

        Registry::set('navigation.tabs', $tabs);

    } else {
        list($data, $search) = fn_search($params, Registry::get('settings.Appearance.admin_elements_per_page'), CART_LANGUAGE, AREA);

        Tygh::$app['view']->assign('search_results', $data);
    }

    Tygh::$app['view']->assign('search', $search);
    Tygh::$app['view']->assign('params', $params);
}
