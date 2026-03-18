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

    if ($mode == 'update'
        && $_REQUEST['addon'] == 'paypal_adaptive'
        && (!empty($_REQUEST['ppa_settings'])
            || !empty($_REQUEST['paypal_adaptive_logo_image_data'])
            || !empty($_REQUEST['paypal_ver_image'])
        )
    ) {
        $ppa_settings = isset($_REQUEST['ppa_settings']) ? $_REQUEST['ppa_settings'] : array();
        fn_update_paypal_adaptive_settings($ppa_settings);
    }
}

if ($mode == 'update') {
    if ($_REQUEST['addon'] == 'paypal_adaptive') {
        $pp_adaptive_settings['main_pair'] = fn_get_image_pairs(0, 'paypal_ver_image', 'M', false, true, DESCR_SL);
        Tygh::$app['view']->assign('ppa_settings', fn_get_paypal_adaptive_settings());
        Tygh::$app['view']->assign('pp_adaptive_settings', $pp_adaptive_settings);
    }
}
