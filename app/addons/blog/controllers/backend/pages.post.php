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

if ($mode == 'add') {

    $page_type = Tygh::$app['view']->getTemplateVars('page_type');
    if ($page_type == PAGE_TYPE_BLOG) {
        $parent_pages = Tygh::$app['view']->getTemplateVars('parent_pages');
        if ($parent_pages) {
            $top_parent = reset($parent_pages);
            $page_data['parent_id'] = $top_parent['page_id'];
            Tygh::$app['view']->assign('page_data', $page_data);
        }
        if (Registry::get('addons.discussion.status') == 'A') {
            Tygh::$app['view']->assign('discussion', array(
                'type' => 'C'
            ));
        }
    }
} elseif ($mode == 'manage') {
    Tygh::$app['view']->assign(
        'is_managing_blog',
        (isset($_REQUEST['page_type']) && $_REQUEST['page_type'] == PAGE_TYPE_BLOG)
    );
}
