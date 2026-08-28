<?php

use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

if (!fn_get_runtime_company_id()) {
    return [CONTROLLER_STATUS_DENIED];
}

if ($mode === 'manage') {
    $notifications_center = Tygh::$app['notifications_center'];
    $notifications = $notifications_center->get([
        'sort_by' => 'pinned_timestamp',
    ], 50);

    $items = [];
    foreach ($notifications as $notification) {
        $item = $notification->toArray();
        $item['action_url'] = $notifications_center->getActionUrl($notification->action_url, $notification->area);
        $items[] = $item;
    }

    Tygh::$app['view']->assign([
        'talario_notifications' => $items,
        'talario_notifications_count' => count($items),
    ]);
}
