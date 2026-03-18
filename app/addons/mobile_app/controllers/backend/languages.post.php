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

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'translations') {

    $params = array_merge(
        [
            'name' => null,
        ],
        $_REQUEST
    );

    $sections = Registry::ifGet('navigation.dynamic.sections', []);
    $sections['mobile_app_translations'] = [
        'title' => __('mobile_app.app_translations'),
        'href'  => fn_url('languages.translations?name=mobile_app.mobile_'),
    ];

    Registry::set('navigation.dynamic.sections', $sections);
    if ($params['name'] === 'mobile_app.mobile_') {
        Registry::set('navigation.dynamic.active_section', 'mobile_app_translations');
    }
}