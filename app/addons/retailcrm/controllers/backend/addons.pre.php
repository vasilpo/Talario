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

/**
 * @var string $mode
 */

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && $mode === 'update'
    && $_REQUEST['addon'] === 'retailcrm'
) {
    if (isset($_REQUEST['retailcrm_mapping'])) {
        /** @var \Tygh\Addons\Retailcrm\Settings $settings */
        $settings = Tygh::$app['addons.retailcrm.settings'];

        if (isset($_REQUEST['retailcrm_mapping']['order_statuses'])) {
            $settings->setMapOrderStatuses((array) $_REQUEST['retailcrm_mapping']['order_statuses']);
        }

        if (isset($_REQUEST['retailcrm_mapping']['payment_types'])) {
            $settings->setMapPaymentTypes((array) $_REQUEST['retailcrm_mapping']['payment_types']);
        }

        if (isset($_REQUEST['retailcrm_mapping']['shipping_types'])) {
            $settings->setMapShippingTypes((array) $_REQUEST['retailcrm_mapping']['shipping_types']);
        }

        if (isset($_REQUEST['retailcrm_mapping']['sites'])) {
            $sites = array_filter((array) $_REQUEST['retailcrm_mapping']['sites']);


            $result = fn_retailcrm_validate_retailcrm_map_sites($sites);
            $result->showNotifications();

            $settings->setMapSites($sites);
        }

        if (isset($_REQUEST['retailcrm_settings']['order_type'])) {
            $settings->setOrderType($_REQUEST['retailcrm_settings']['order_type']);
        }

        if (isset($_REQUEST['retailcrm_settings']['order_method'])) {
            $settings->setOrderMethod($_REQUEST['retailcrm_settings']['order_method']);
        }
    }
}