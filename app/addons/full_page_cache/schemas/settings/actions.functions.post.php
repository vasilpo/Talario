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

use Tygh\Enum\ObjectStatuses;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Hook is used to check whether an add-on can be enabled and execute enable/disable-related actions.
 *
 * @param string $new_status Status addon updated to
 * @param string $old_status Status addon updated from
 *
 * @return void
 */
function fn_settings_actions_addons_full_page_cache(&$new_status, $old_status)
{
    if ($new_status === ObjectStatuses::ACTIVE) {
        /** @var \Tygh\Addons\FullPageCache\Addon $addon */
        $addon = Tygh::$app['addons.full_page_cache'];
        if (!$addon->canBeEnabled()) {
            $new_status = ObjectStatuses::DISABLED;
            return;
        }
    }
}

/**
 * Hook is used to change status of the unmanaged addon together with main addon.
 *
 * @param string $new_status Status addon updated to
 *
 * @return void
 */
function fn_settings_actions_addons_post_full_page_cache($new_status)
{
    /** @var \Tygh\Addons\FullPageCache\Addon $addon */
    $addon = Tygh::$app['addons.full_page_cache'];

    // Add-on have been enabled
    if ($new_status === ObjectStatuses::ACTIVE) {
        $addon->onAddonEnable();
    } // Add-on have been disabled

    fn_update_addon_status('full_page_cache_unmanaged', $new_status, false, false, true);
}
