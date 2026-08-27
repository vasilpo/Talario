<?php

use InvalidArgumentException;
use RuntimeException;
use Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

$company_id = (int) fn_get_runtime_company_id();
if (!$company_id) {
    return [CONTROLLER_STATUS_DENIED];
}

$service = new ScheduleResourceService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'update') {
        $location_id = isset($_REQUEST['location_id']) ? (int) $_REQUEST['location_id'] : 0;
        $data = isset($_REQUEST['location_data']) && is_array($_REQUEST['location_data'])
            ? $_REQUEST['location_data']
            : [];

        $data['name'] = trim((string) ($data['name'] ?? ''));
        $data['address'] = trim((string) ($data['address'] ?? ''));
        $data['address_details'] = trim((string) ($data['address_details'] ?? ''));
        $data['status'] = (($data['status'] ?? 'A') === 'D') ? 'D' : 'A';

        if ($data['name'] === '' || $data['address'] === '') {
            fn_set_notification('E', __('error'), __('talario_vendor_cabinet.location_required_fields'));
            $redirect = $location_id
                ? 'talario_locations.update?location_id=' . $location_id
                : 'talario_locations.update';
            return [CONTROLLER_STATUS_REDIRECT, $redirect];
        }

        try {
            if ($location_id) {
                $service->updateLocation($location_id, $data);
            } else {
                $service->createLocation($data);
            }
            fn_set_notification('N', __('notice'), __('talario_vendor_cabinet.location_saved'));
        } catch (InvalidArgumentException $e) {
            fn_set_notification('E', __('error'), $e->getMessage());
        } catch (RuntimeException $e) {
            return [CONTROLLER_STATUS_DENIED];
        }

        return [CONTROLLER_STATUS_REDIRECT, 'talario_locations.manage'];
    }

    if ($mode === 'update_status') {
        $location_id = isset($_REQUEST['location_id']) ? (int) $_REQUEST['location_id'] : 0;
        $status = (isset($_REQUEST['status']) && $_REQUEST['status'] === 'A') ? 'A' : 'D';

        try {
            $service->updateLocation($location_id, ['status' => $status]);
        } catch (InvalidArgumentException $e) {
            fn_set_notification('E', __('error'), $e->getMessage());
        } catch (RuntimeException $e) {
            return [CONTROLLER_STATUS_DENIED];
        }

        return [CONTROLLER_STATUS_REDIRECT, 'talario_locations.manage'];
    }
}

if ($mode === 'manage') {
    try {
        Tygh::$app['view']->assign('talario_locations', $service->getLocations());
    } catch (RuntimeException $e) {
        return [CONTROLLER_STATUS_DENIED];
    }
} elseif ($mode === 'update') {
    $location_id = isset($_REQUEST['location_id']) ? (int) $_REQUEST['location_id'] : 0;
    $location = ['status' => 'A'];

    if ($location_id) {
        try {
            $location = $service->getLocation($location_id);
        } catch (InvalidArgumentException $e) {
            return [CONTROLLER_STATUS_NO_PAGE];
        } catch (RuntimeException $e) {
            return [CONTROLLER_STATUS_DENIED];
        }
    }

    Tygh::$app['view']->assign('talario_location', $location);
}
