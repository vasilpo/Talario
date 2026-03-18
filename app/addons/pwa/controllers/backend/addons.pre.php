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

use Tygh\Enum\YesNo;
use Tygh\Providers\StorefrontProvider;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

$storefront_id = !empty($_REQUEST['storefront_id']) && (int) $_REQUEST['storefront_id']
    ? (int) $_REQUEST['storefront_id']
    : StorefrontProvider::getStorefront()->storefront_id;

if (fn_allowed_for('ULTIMATE')) {
    $storefront_id = 0;
    if (fn_get_runtime_company_id()) {
        $storefront_id = StorefrontProvider::getStorefront()->storefront_id;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        $mode === 'update'
        && $_REQUEST['addon'] === 'pwa'
        && (
            !empty($_REQUEST['pwa'])
            || !empty($_REQUEST['manifest_icon_image_data'])
        )
    ) {
        $pwa_settings = [
            'pwa_configuration' => $_REQUEST['pwa'] ?? []
        ];

        if (Registry::get('runtime.is_multiple_storefronts') && empty($storefront_id)) {
            return [CONTROLLER_STATUS_NO_PAGE];
        }

        fn_update_pwa_settings($pwa_settings, $storefront_id);
    }

    return [CONTROLLER_STATUS_OK];
}

if ($mode === 'update') {
    if ($_REQUEST['addon'] === 'pwa') {
        if (
            fn_allowed_for('MULTIVENDOR') && Registry::get('runtime.is_multiple_storefronts')
            && empty($_REQUEST['storefront_id'])
            || fn_allowed_for('ULTIMATE') && empty($storefront_id)
        ) {
            $view = Tygh::$app['view'];

            if (fn_allowed_for('MULTIVENDOR')) {
                $view->assign('switcher_param_name', 'storefront_id');
                $view->assign('storefront_switcher_param_name', 'storefront_id');
            }

            $view->assign('content_tpl', 'common/select_company.tpl');

            return [CONTROLLER_STATUS_OK];
        }

        /** @var \Tygh\Storefront\Repository $repository */
        $repository = Tygh::$app['storefront.repository'];

        $is_change_storefront_allowed = fn_check_change_storefront_permission();
        if ($is_change_storefront_allowed) {
            [$all_storefronts,] = $repository->find();
        } else {
            $single_storefront = $repository->findById($storefront_id);

            if (!empty($single_storefront)) {
                $all_storefronts = [$single_storefront];
            }
        }

        $pwa_settings_for_current_storefront = fn_get_pwa_settings($storefront_id);

        if (!empty($all_storefronts)) {
            $storefront_ids_map = fn_pwa_map_relative_storefront_ids($all_storefronts);
            // Relative storefront = storefront on same domain
            $rel_storefront_ids = $storefront_ids_map[$storefront_id]['relatives'] ?? [];

            $relative_storefront_list_html = '';
            $main_domain = null;
            $subfolder = null;
            $current_name = '';
            $current_url = '';
            $active_name = null;
            $active_url = null;
            $has_active_app_on_rel_storefront = false;

            foreach ($all_storefronts as $_storefront) {
                $id = $_storefront->storefront_id;

                if ($id !== $storefront_id && !in_array($id, $rel_storefront_ids)) {
                    continue;
                }

                $name = $_storefront->name;
                $url = $_storefront->url;

                if ($id === $storefront_id) {
                    $current_name = $name;
                    $current_url = $url;
                }

                if (!empty($storefront_ids_map[$id]['is_parent'])) {
                    $main_domain = $url;
                } elseif (!isset($subfolder) || $id === $storefront_id) {
                    $pos = strpos($url, '/');
                    // Subfolder is a part of storefront url between 1st and 2nd slash (first one storefront taken)
                    $subfolder = ($pos = strpos($url, '/')) !== false ? explode('/', substr($url, $pos + 1))[0] ?? null : null;
                }

                $relative_storefront_list_html .= '<li><i>' . htmlspecialchars($name) . '</i>: <u>' . htmlspecialchars($url) . '</u></li>';
            }

            foreach ($rel_storefront_ids as $rel_id) {
                // Getting company_id is workaround for ULTIMATE to get other storefronts settings
                $rel_company_ids = $all_storefronts[$rel_id]->getCompanyIds();
                $rel_company_id = !empty($rel_company_ids) ? reset($rel_company_ids) : null;

                $_pwa_settings = fn_get_pwa_settings($rel_id, fn_allowed_for('ULTIMATE') ? $rel_company_id : null);

                if (empty($_pwa_settings['config']['manifest_status']) || !YesNo::toBool($_pwa_settings['config']['manifest_status'])) {
                    continue;
                }

                $active_name = $active_name ?? ($all_storefronts[$rel_id]->name ?? null);
                $active_url = $active_url ?? ($all_storefronts[$rel_id]->url ?? null);
                $has_active_app_on_rel_storefront = true;
            }

            if (empty($main_domain)) {
                $url = reset($all_storefronts)->url;
                $main_domain = ($pos = strpos($url, '/')) !== false ? substr($url, 0, $pos) : $url;
            }

            Tygh::$app['view']->assign('has_relatives_but_no_active_apps', !empty($rel_storefront_ids) && !$has_active_app_on_rel_storefront);
            Tygh::$app['view']->assign('has_active_app_on_rel_storefront', $has_active_app_on_rel_storefront);
            Tygh::$app['view']->assign('current_name', $current_name);
            Tygh::$app['view']->assign('current_url', $current_url);
            Tygh::$app['view']->assign('active_name', $active_name);
            Tygh::$app['view']->assign('active_url', $active_url);
            Tygh::$app['view']->assign('main_domain', $main_domain);
            Tygh::$app['view']->assign('subfolder', $subfolder);
            Tygh::$app['view']->assign('relative_storefront_list_html', $relative_storefront_list_html);
        }

        Tygh::$app['view']->assign('pwa', $pwa_settings_for_current_storefront);
        Tygh::$app['view']->assign('current_storefront_id', $storefront_id);
    }
}
