<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

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
