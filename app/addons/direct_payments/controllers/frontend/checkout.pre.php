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

use Tygh\Enum\NotificationSeverity;

defined('BOOTSTRAP') or die('Access denied');

/** @var \Tygh\Addons\DirectPayments\Cart\Service $cart_service */
$cart_service = Tygh::$app['addons.direct_payments.cart.service'];

if ($mode === 'place_order') {
    if (!isset($_REQUEST['vendor_id'])) {
        // If direct_payments addon is on, vendor_id should always be passed with 'place_order' request.
        fn_set_notification(NotificationSeverity::ERROR, __('error'), __('direct_payments.no_vendor_id_in_request'));

        return [CONTROLLER_STATUS_NO_PAGE];
    }
}

/**
 * Override cart and data associated to it in session by same data for current vendor.
 */
$cart_service->overrideSessionDataByVendorData();

return [CONTROLLER_STATUS_OK];
