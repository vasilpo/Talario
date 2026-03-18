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

$gift_cert_code = empty($_REQUEST['gift_cert_code']) ? '' : strtoupper(trim($_REQUEST['gift_cert_code']));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($mode == 'apply_coupon') {
        $gift_cert_code = empty($_REQUEST['coupon_code']) ? '' : strtoupper(trim($_REQUEST['coupon_code']));
        $company_id = Registry::get('runtime.company_id');
        if (!empty($gift_cert_code)) {
            if (true == fn_check_gift_certificate_code($gift_cert_code, true, $company_id)) {
                Tygh::$app['session']['promotion_notices']['gift_certificates'] = array(
                    'applied' => true,
                    'messages' => array()
                );
                if (!isset(Tygh::$app['session']['cart']['use_gift_certificates'][$gift_cert_code])) {
                    Tygh::$app['session']['cart']['use_gift_certificates'][$gift_cert_code] = 'Y';
                    Tygh::$app['session']['cart']['pending_certificates'][] = $gift_cert_code;
                    Tygh::$app['session']['promotion_notices']['gift_certificates']['messages'][] = 'text_gift_cert_applied';
                    if (isset(Tygh::$app['session']['cart']['pending_coupon'])) {
                        unset(Tygh::$app['session']['cart']['pending_coupon']);
                    }
                } else {
                    Tygh::$app['session']['promotion_notices']['gift_certificates']['messages'][] = 'certificate_already_used';
                }

            } else {
                Tygh::$app['session']['promotion_notices']['gift_certificates'] = array(
                    'applied' => false,
                    'messages' => array()
                );
                $status = db_get_field(
                    "SELECT status FROM ?:gift_certificates WHERE gift_cert_code = ?s ?p",
                    $gift_cert_code, fn_get_gift_certificate_company_condition('?:gift_certificates.company_id')
                );

                $params = [
                    'active'      => true,
                    'coupon_code' => !empty(Tygh::$app['session']['cart']['pending_original_coupon'])
                        ? Tygh::$app['session']['cart']['pending_original_coupon']
                        : Tygh::$app['session']['cart']['pending_coupon'],
                ];

                list($coupon) = fn_get_promotions($params);

                if (empty($coupon) && !fn_notification_exists('extra', 'error_coupon_already_used')) {
                    Tygh::$app['session']['promotion_notices']['gift_certificates']['messages'][] = 'no_such_coupon';
                }
            }
        }

        return array(CONTROLLER_STATUS_REDIRECT, 'checkout.' . (!empty($_REQUEST['redirect_mode']) ? $_REQUEST['redirect_mode'] : 'cart'));
    }

    if ($mode == 'delete_use_certificate' && !empty($gift_cert_code)) {
        fn_delete_gift_certificate_in_use($gift_cert_code, Tygh::$app['session']['cart']);

        if (fn_cart_is_empty(Tygh::$app['session']['cart']) && defined('AJAX_REQUEST')) {
            Tygh::$app['ajax']->assign('force_redirection', fn_url('checkout.cart'));
        }

        return array(CONTROLLER_STATUS_REDIRECT, 'checkout.' . (!empty($_REQUEST['redirect_mode']) ? $_REQUEST['redirect_mode'] : 'checkout') . '.show_payment_options');
    }

    return;
}
