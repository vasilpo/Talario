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
use Tygh\Enum\ObjectStatuses;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

/**
 * The `fn_settings_actions_addons_` handler.
 *
 * Perform shipping assignment actualization when add-on changing it's status.
 *
 * @param string $new_status New add-on status.
 * @param string $old_status Old add-on status.
 * @param bool   $on_install True if current status change is part of installation process, false otherwise.
 *
 * @return void
 */
function fn_settings_actions_addons_order_fulfillment($new_status, $old_status, $on_install)
{
    if ($on_install) {
        return;
    }
    fn_settings_actions_addons_order_fulfillment_actualize_shipping_methods($new_status === ObjectStatuses::ACTIVE);
}

/**
 * Perform shipping assignment actualization when add-on changing it's status.
 *
 * @param bool $turning_on True if add-on is activating, false otherwise.
 *
 * @return void
 */
function fn_settings_actions_addons_order_fulfillment_actualize_shipping_methods($turning_on)
{
    $auth = Tygh::$app['session']['auth'];
    list($all_companies,) = fn_get_companies([], $auth);
    foreach ($all_companies as $company) {
        /** @var array<string, string|array<string>>|false $company_data */
        $company_data = fn_get_company_data($company['company_id']);
        if (
            !$company_data
            || (empty($company_data['shippings_ids']) && empty($company_data['saved_shippings_state']))
        ) {
            continue;
        }
        $old_shippings = explode(',', $company_data['saved_shippings_state']);
        $company_data['saved_shippings_state'] = implode(', ', $company_data['shippings_ids']);
        $company_data['shippings'] = $old_shippings;
        fn_update_company($company_data, $company['company_id']);
        if ($turning_on) {
            fn_set_notification(NotificationSeverity::WARNING, __('notice'), __('order_fulfillment.marketplace_shippings_available'), 'S');
        } else {
            fn_set_notification(NotificationSeverity::WARNING, __('notice'), __('order_fulfillment.vendor_shipping_settings_active'), 'S');
        }
    }
}
