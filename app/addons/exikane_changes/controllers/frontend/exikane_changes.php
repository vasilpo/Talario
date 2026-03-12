<?php

use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

if ($mode === 'calendar_event') {
    $order_id = isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : 0;
    if ($order_id <= 0) {
        /** @phpstan-ignore-next-line Runtime CS-Cart controller status constant. */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $order_info = fn_get_order_info($order_id);
    if (empty($order_info)) {
        /** @phpstan-ignore-next-line Runtime CS-Cart controller status constant. */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $event_data = fn_exikane_changes_get_calendar_event_data($order_info);
    if ($event_data === null) {
        /** @phpstan-ignore-next-line Runtime CS-Cart controller status constant. */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $ics_content = fn_exikane_changes_build_ics_content($event_data);
    $filename = 'booking-' . $order_id . '.ics';

    header('Content-Type: text/calendar; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($ics_content));

    echo $ics_content;

    exit;
}

if ($mode === 'partner_site_click') {
    $product_id = isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0;
    if ($product_id <= 0) {
        /** @phpstan-ignore-next-line Runtime CS-Cart controller status constant. */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $site = fn_exikane_changes_get_partner_site($product_id);
    if ($site === '') {
        /** @phpstan-ignore-next-line Runtime CS-Cart controller status constant. */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $site = fn_exikane_changes_normalize_site_url($site);
    if ($site === '') {
        /** @phpstan-ignore-next-line Runtime CS-Cart controller status constant. */
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $site = fn_exikane_changes_attach_partner_utm($site);

    $auth = isset(Tygh::$app['session']['auth']) ? (array) Tygh::$app['session']['auth'] : [];
    fn_exikane_changes_log_partner_click($product_id, $auth);

    fn_redirect($site, true);
}
