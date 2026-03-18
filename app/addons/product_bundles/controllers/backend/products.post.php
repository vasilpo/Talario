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
use Tygh\Http;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'update') {
    $is_allowed_to_view_product_bundles = fn_check_view_permissions('product_bundles.manage', Http::GET);
    if (!$is_allowed_to_view_product_bundles) {
        return [CONTROLLER_STATUS_OK];
    }

    Registry::set('navigation.tabs.product_bundles', [
        'title' => __('product_bundles.product_bundles'),
        'js'    => true,
    ]);

    $params = [
        'product_id' => $_REQUEST['product_id'],
        'lang_code'  => DESCR_SL,
    ];

    $service = ServiceProvider::getService();
    list($bundles,) = $service->getBundles($params);

    Tygh::$app['view']->assign([
        'bundles' => $bundles,
        'is_allowed_to_create_product_bundles' => fn_check_view_permissions('product_bundles.update', Http::POST)
    ]);
}
