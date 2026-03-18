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
use Tygh\Registry;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return;
}

if (
    $mode === 'update'
    && $_REQUEST['addon'] === 'searchanise'
) {
    $tabs = Registry::get('navigation.tabs');
    unset($tabs['subscription']);
    Registry::set('navigation.tabs', $tabs);
    Tygh::$app['view']->assign('personal_review', true);
}

if ($mode === 'manage') {
    /** @var array<string, array<string>> $addon_list */
    $addon_list = Tygh::$app['view']->getTemplateVars('addons_list');
    if (isset($addon_list['searchanise'])) {
        $addon_list['searchanise']['hide_post_review'] = true;
    }
    Tygh::$app['view']->assign('addons_list', $addon_list);
}
