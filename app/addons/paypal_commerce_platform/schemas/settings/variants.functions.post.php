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

defined('BOOTSTRAP') or die('Access denied');

/**
 * Provides list of variants for Order status on refund add-on setting.
 *
 * @return array<string, string>
 */
function fn_settings_variants_addons_paypal_commerce_platform_rma_refunded_order_status()
{
    $order_statuses = fn_get_simple_statuses(STATUSES_ORDER);

    return array_merge(
        ['' => __('paypal_commerce_platform.do_not_change')],
        $order_statuses
    );
}
