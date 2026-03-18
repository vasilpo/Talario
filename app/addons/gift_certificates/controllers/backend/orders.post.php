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
    return [CONTROLLER_STATUS_OK];
}

if ($mode == 'details') {
    $downloads_exist = Tygh::$app['view']->getTemplateVars('downloads_exist');
    if (!$downloads_exist) {
        return [CONTROLLER_STATUS_OK];
    }

    $order_info = Tygh::$app['view']->getTemplateVars('order_info');
    $downloads_exist = fn_gift_certificate_has_downloadable_products_in_order($order_info);


    if (!$downloads_exist) {
        Registry::del('navigation.tabs.downloads');
    }

    return [CONTROLLER_STATUS_OK];
}
