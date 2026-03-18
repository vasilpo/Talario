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

if ($mode == 'add' || $mode == 'update') {
    $page_type = isset($_REQUEST['page_type']) ? $_REQUEST['page_type'] : '';
    if (empty($page_type) && !empty($_REQUEST['page_id'])) {
        $page_data = fn_get_page_data($_REQUEST['page_id']);
        $page_type = $page_data['page_type'];
    }

    if ($page_type == PAGE_TYPE_FORM) {
        // [Page sections]
        Registry::set('navigation.tabs.build_form', array (
            'title' => __('form_builder'),
            'js' => true
        ));
        // [/Page sections]
    }

    Tygh::$app['view']->assign('selectable_elements', implode('', fn_form_builder_selectable_elements()));
}

if ($mode == 'update') {
    list($elements, $form) = fn_get_form_elements($_REQUEST['page_id'], false, DESCR_SL);
    Tygh::$app['view']->assign('form', $form);
    Tygh::$app['view']->assign('elements', $elements);
}
