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

//
// View page details
//
if ($mode == 'add' && Registry::get('addons.tags.tags_for_pages') == 'Y') {
    if (Registry::get('runtime.company_id') && fn_allowed_for('ULTIMATE') || fn_allowed_for('MULTIVENDOR')) {
        Registry::set('navigation.tabs.tags', array(
            'title' => __('tags'),
            'js' => true
        ));
    }

} elseif ($mode == 'update' && Registry::get('addons.tags.tags_for_pages') == 'Y') {
    if (Registry::get('runtime.company_id') && fn_allowed_for('ULTIMATE') || fn_allowed_for('MULTIVENDOR')) {
        Registry::set('navigation.tabs.tags', array(
            'title' => __('tags'),
            'js' => true
        ));
    }

    $page = Tygh::$app['view']->getTemplateVars('page_data');

    list($tags) = fn_get_tags(array(
        'object_type' => 'A',
        'object_id' => $page['page_id']
    ));

    $page['tags'] = $tags;

    Tygh::$app['view']->assign('page_data', $page);
}
