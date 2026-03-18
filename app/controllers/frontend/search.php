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

if ($mode == 'results') {
    $params = $_REQUEST;
    $params['objects'] = array_keys(fn_search_get_customer_objects());

    unset($params['compact']);

    list($search_results, $search) = fn_search($params, Registry::get('settings.Appearance.products_per_page'));

    Tygh::$app['view']->assign('search_results', $search_results);
    Tygh::$app['view']->assign('search', $search);

    fn_add_breadcrumb(__('search_results'));
}
