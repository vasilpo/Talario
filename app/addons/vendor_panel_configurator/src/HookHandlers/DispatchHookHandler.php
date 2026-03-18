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

namespace Tygh\Addons\VendorPanelConfigurator\HookHandlers;

use Tygh\Addons\VendorPanelConfigurator\ServiceProvider;
use Tygh\Less;
use Tygh\Registry;
use Tygh\Tygh;

class DispatchHookHandler
{
    /**
     * The "dispatch_assign_template" hook handler.
     *
     * Actions performed:
     *  - Adds information about vendor panel style settings
     *
     * @return void
     *
     * @see \fn_dispatch()
     */
    public function onDispatchAssignTemplate()
    {
        $settings_service = ServiceProvider::getSettingsService();
        $style = $settings_service->getVendorPanelStyle();

        // phpcs:ignore
        if (defined('ACCOUNT_TYPE') && ACCOUNT_TYPE === 'vendor') {
            Registry::set('runtime.vendor_panel_style', $style);
        }

        if (empty($style['element_color'])) {
            return;
        }

        $less = new Less();
        /** @var array<array-key, string> $style */
        $main_color_saturation = $less->getColorSaturation(['raw_color', $style['element_color']]);
        Tygh::$app['view']->assign('is_gray_main_color', $main_color_saturation < 10);
    }
}
