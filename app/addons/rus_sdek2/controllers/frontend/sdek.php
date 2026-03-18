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

if ($mode === 'sdek_offices') {
    $group_key = $_REQUEST['group_key'];
    $shipping_id = $_REQUEST['shipping_id'];
    $select_office = $_REQUEST['old_office_id'];

    $sdek_offices = Tygh::$app['session']['cart']['shippings_extra']['data'][$group_key][$shipping_id]['offices'];

    Tygh::$app['view']->assign('group_key', $group_key);
    Tygh::$app['view']->assign('shipping_id', $shipping_id);
    Tygh::$app['view']->assign('old_office_id', $select_office);
    Tygh::$app['view']->assign('sdek_offices', $sdek_offices);
    Tygh::$app['view']->display('addons/rus_sdek2/views/sdek/sdek_offices.tpl');

    exit;
}
