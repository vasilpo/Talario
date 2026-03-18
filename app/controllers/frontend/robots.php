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
use Tygh\Common\Robots;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'view') {
    $robots = new Robots;

    $storefront = Tygh::$app['storefront'];

    $content = $robots->getRobotsTxtContent();

    if (!isset($content)) {
        $robots_data = $robots->getRobotsDataByStorefrontId($storefront->storefront_id);
        $content = isset($robots_data['data']) ? $robots_data['data'] : '';
    }

    header('Content-type: text/plain; charset=utf-8');
    echo($content);
    exit;
}
