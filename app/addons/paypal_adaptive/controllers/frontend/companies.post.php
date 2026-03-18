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

if ($mode == 'catalog') {
    $params = $_REQUEST;
    $params['status'] = 'A';
    $params['get_description'] = 'Y';

    $companies = Registry::get('view')->getTemplateVars('companies');

    foreach ($companies as $company_id => $company) {
        $show_vendor_verification = Registry::get('addons.paypal_adaptive.display_verification_status_on_storefront');
        if ($show_vendor_verification == 'Y' && $company['paypal_verification'] == 'verified') {
            $companies[$company_id]['paypal_verification_status']['main_pair'] = fn_get_image_pairs(0, 'paypal_ver_image', 'M', false, true, DESCR_SL);
            if (!empty($companies[$company_id]['paypal_verification_status']['main_pair'])) {
                $width = Registry::get('addons.paypal_adaptive.paypal_verified_image_width');
                $height = Registry::get('addons.paypal_adaptive.paypal_verified_image_height');
                $companies[$company_id]['paypal_verification_status']['width'] = $width ?: 60;
                $companies[$company_id]['paypal_verification_status']['height'] = $height ?: 60;
            } else {
                $companies[$company_id]['paypal_verification_status']['verified'] = 'verified';
            }
        }
    }
    Tygh::$app['view']->assign('companies', $companies);
}