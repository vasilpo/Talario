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

$products_deprecated_href = Registry::get('navigation.dynamic.sections.products.href');
if ($products_deprecated_href && ($mode === 'import')) {
    Registry::set('navigation.dynamic.sections.products.href', 'import_presets.manage&object_type=products');
    Registry::set('navigation.dynamic.sections.products_deprecated', [
        'href' => $products_deprecated_href,
        'title' => __('products_deprecated'),
    ]);

    if ($_REQUEST['section'] === 'products') {
        Registry::set('navigation.dynamic.active_section', 'products_deprecated');
    }
}
