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

if ($mode == 'view') {

    $page_data = Tygh::$app['view']->getTemplateVars('page');
    $is_pagination = !empty($_REQUEST['page']);

    if ($page_data['page_type'] == PAGE_TYPE_BLOG) {

        list($subpages, $search) = fn_get_pages([
            'parent_id' => $page_data['page_id'],
            'page' => !empty($_REQUEST['page']) ? $_REQUEST['page'] : 0,
            'page_type' => PAGE_TYPE_BLOG,
            'get_image' => true,
            'status' => 'A',
            'sort_by' => 'timestamp',
            'sort_order' => 'desc'
        ], Registry::get('settings.Appearance.elements_per_page'));

        if (
            empty($subpages)
            && $is_pagination
        ) {
            return [CONTROLLER_STATUS_NO_PAGE];
        }

        Tygh::$app['view']->assign('subpages', $subpages);
        Tygh::$app['view']->assign('search', $search);
    }
}
