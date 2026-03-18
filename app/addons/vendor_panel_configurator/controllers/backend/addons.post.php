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

use Tygh\Addons\VendorPanelConfigurator\ServiceProvider;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        $mode === 'update'
        && isset($_REQUEST['addon'])
        && $_REQUEST['addon'] === 'vendor_panel_configurator'
    ) {
        $settings_service = ServiceProvider::getSettingsService();
        if (isset($_REQUEST['product_fields_configuration'])) {
            $settings_service->updateProductFieldsConfiguration($_REQUEST['product_fields_configuration']);
        }
        if (isset($_REQUEST['product_tabs_configuration'])) {
            $settings_service->updateProductTabsConfiguration($_REQUEST['product_tabs_configuration']);
        }
        if (isset($_REQUEST['vendor_panel'])) {
            $settings_service->updateVendorPanelStyleConfiguration($_REQUEST['vendor_panel']);
        }
    }

    return [CONTROLLER_STATUS_OK];
}

if (
    $mode === 'update'
    && isset($_REQUEST['addon'])
    && $_REQUEST['addon'] === 'vendor_panel_configurator'
) {
    $settings_service = ServiceProvider::getSettingsService();
    $vendor_panel = $settings_service->getVendorPanelStyle();
    $color_schemas = $settings_service->getColorSchemas();

    if (
        isset($_REQUEST['vendor_panel'])
        && isset($color_schemas[$_REQUEST['vendor_panel']['color_schema']])
    ) {
        $vendor_panel = array_merge($vendor_panel, $color_schemas[$_REQUEST['vendor_panel']['color_schema']], $_REQUEST['vendor_panel']);
    }

    /** @var \Tygh\SmartyEngine\Core $view */
    $view = Tygh::$app['view'];
    $view->assign([
        'product_page_configuration' => $settings_service->getProductPageConfiguration(),
        'vendor_panel'               => $vendor_panel,
        'color_schemas'              => $color_schemas,
    ]);

    return [CONTROLLER_STATUS_OK];
}
