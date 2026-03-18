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

if (in_array($mode, array('cart', 'checkout', 'place_order')) && isset(Tygh::$app['session']['cart']['use_gift_certificates'])) {
    $company_id = Registry::get('runtime.company_id');
    $codes = fn_check_gift_certificate_code(array_keys(Tygh::$app['session']['cart']['use_gift_certificates']), true, $company_id);

    $remove_codes = array_diff_key(Tygh::$app['session']['cart']['use_gift_certificates'], !empty($codes) ? $codes : array());
    $removed_codes = false;

    if (!empty($remove_codes)) {
        foreach ($remove_codes as $code => $value) {
            unset(Tygh::$app['session']['cart']['use_gift_certificates'][$code]);
        }
        $removed_codes = true;
    }

    if ($removed_codes) {
        fn_set_notification('W', __('warning'), __('warning_gift_cert_deny', array(
            '[codes]' => implode(', ', array_keys($remove_codes))
        )), 'K');
    }

    if ($mode == 'place_order') {
        fn_calculate_cart_content(Tygh::$app['session']['cart'], $auth, 'A', true, 'F');
    }

    return;
}
