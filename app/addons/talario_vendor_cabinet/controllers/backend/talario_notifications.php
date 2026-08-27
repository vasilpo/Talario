<?php

use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

if (!fn_get_runtime_company_id()) {
    return [CONTROLLER_STATUS_DENIED];
}

if ($mode === 'manage') {
    $notifications_center = Tygh::$app['notifications_center'];
    Tygh::$app['view']->assign('talario_notifications_count', (int) $notifications_center->getCount());
}
