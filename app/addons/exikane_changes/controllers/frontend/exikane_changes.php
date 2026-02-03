<?php

defined('BOOTSTRAP') or die('Access denied');

if ($mode === 'partner_site_click') {
    $product_id = isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0;
    if ($product_id <= 0) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $site = fn_exikane_changes_get_partner_site($product_id);
    if ($site === '') {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $site = fn_exikane_changes_normalize_site_url($site);
    if ($site === '') {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $site = fn_exikane_changes_attach_partner_utm($site);

    $auth = isset(Tygh::$app['session']['auth']) ? (array) Tygh::$app['session']['auth'] : [];
    fn_exikane_changes_log_partner_click($product_id, $auth);

    fn_redirect($site, true);
}
