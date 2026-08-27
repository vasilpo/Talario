<?php

use RuntimeException;
use Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || $mode !== 'add' || !fn_get_runtime_company_id()) {
    return;
}

try {
    $locations = (new ScheduleResourceService())->getLocations();
} catch (RuntimeException $e) {
    return;
}

if (!$locations) {
    fn_set_notification('W', __('notice'), 'Сначала заполните информацию о центре и добавьте хотя бы один филиал.');
    return [CONTROLLER_STATUS_REDIRECT, 'talario_locations.manage'];
}
