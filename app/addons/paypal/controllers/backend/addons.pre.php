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

use Tygh\Providers\StorefrontProvider;

defined('BOOTSTRAP') or die('Access denied');

$storefront_id = empty($_REQUEST['storefront_id'])
    ? 0
    : (int) $_REQUEST['storefront_id'];

if (fn_allowed_for('ULTIMATE')) {
    $storefront_id = 0;
    if (fn_get_runtime_company_id()) {
        $storefront_id = StorefrontProvider::getStorefront()->storefront_id;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        $mode === 'update'
        && $_REQUEST['addon'] === 'paypal'
        && (
            !empty($_REQUEST['pp_settings'])
            || !empty($_REQUEST['paypal_logo_image_data'])
        )
    ) {
        $pp_settings = isset($_REQUEST['pp_settings'])
            ? $_REQUEST['pp_settings']
            : [];

        fn_update_paypal_settings($pp_settings, $storefront_id);
    }

    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'update') {
    if ($_REQUEST['addon'] === 'paypal') {
        Tygh::$app['view']->assign('pp_settings', fn_get_paypal_settings($storefront_id));
    }
}

return [CONTROLLER_STATUS_OK];
