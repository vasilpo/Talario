<?php

use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

if (!fn_get_runtime_company_id()) {
    return [CONTROLLER_STATUS_DENIED];
}

if ($mode === 'manage') {
    $notifications_center = Tygh::$app['notifications_center'];
    $notifications = $notifications_center->get([
        'items_per_page' => 50,
        'sort_by'        => 'pinned_timestamp',
    ], 50);

    Tygh::$app['view']->assign('talario_notifications', $notifications_center->buildViewData($notifications));
}
