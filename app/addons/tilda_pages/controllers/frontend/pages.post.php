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

/**
 * @var string $mode
 */

if ($mode === 'view' && !empty($_REQUEST['page_id'])) {
    $page = fn_get_page_data($_REQUEST['page_id'], CART_LANGUAGE);

    if (!empty($page['dispatch'])) {
        return [CONTROLLER_STATUS_REDIRECT, $page['dispatch']];
    }

    if ($page['page_type'] === PAGE_TYPE_TILDA_PAGE && !empty($page['tilda_page_id'])) {
        /** @var \Tygh\SmartyEngine\Core $view */
        $view = Tygh::$app['view'];

        $view->assign([
            'tilda_page_upload_settings' => fn_tilda_pages_get_upload_settings($page['tilda_page_id'])
        ]);
    }
}
