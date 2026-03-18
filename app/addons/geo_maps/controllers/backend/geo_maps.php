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

use Tygh\Enum\YesNo;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    return [CONTROLLER_STATUS_OK];
}

if (
    $mode === 'map'
    && !empty($_REQUEST['provider'])
    && isset($_REQUEST['api_key'])
) {
    $view = Tygh::$app['view'];

    $view->assign([
        'provider'          => $_REQUEST['provider'],
        'api_key'           => $_REQUEST['api_key'],
        'yandex_commercial' => isset($_REQUEST['yandex_commercial']) ? $_REQUEST['yandex_commercial'] : YesNo::NO,
    ]);

    echo $view->fetch('addons/geo_maps/components/map.tpl');

    return [CONTROLLER_STATUS_NO_CONTENT];
}
