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

use Tygh\Addons\ProductBundles\ServiceProvider;
use Tygh\Enum\ObjectStatuses;

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'list') {
    $bundle_service = ServiceProvider::getService();
    $params = [
        'display_in_promotions' => true,
        'full_info'             => true,
        'status'                => ObjectStatuses::ACTIVE,
        'items_per_page'        => 0,
    ];
    list($bundles,) = $bundle_service->getBundles($params);
    Tygh::$app['view']->assign('bundles', $bundles);
}
