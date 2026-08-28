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

$normalize_center_description = static function ($value) {
    $value = html_entity_decode(strip_tags((string) $value), ENT_QUOTES, 'UTF-8');
    $value = preg_replace('/\s+/u', ' ', $value);
    $value = trim((string) $value);

    if ($value === '') {
        return '';
    }

    if (preg_match('/^(.+?[.!?])(?:\s|$)/u', $value, $matches)) {
        $value = trim($matches[1]);
    }

    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, 180, 'UTF-8');
    }

    return substr($value, 0, 180);
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'update_center') {
        $center_data = isset($_REQUEST['center_data']) && is_array($_REQUEST['center_data']) ? $_REQUEST['center_data'] : [];
        $name = trim((string) ($center_data['name'] ?? ''));
        $description = $normalize_center_description($center_data['description'] ?? '');

        if ($name === '') {
            fn_set_notification('E', __('error'), 'Укажите название центра.');
            return [CONTROLLER_STATUS_REDIRECT, 'talario_locations.manage'];
        }

        fn_talario_vendor_cabinet_update_center($company_id, $name, $description);

        fn_set_notification('N', __('notice'), 'Информация о центре сохранена.');
        return [CONTROLLER_STATUS_REDIRECT, 'talario_locations.manage'];
    }

    if ($mode === 'update') {
        $location_id = isset($_REQUEST['location_id']) ? (int) $_REQUEST['location_id'] : 0;
        $data = isset($_REQUEST['location_data']) && is_array($_REQUEST['location_data']) ? $_REQUEST['location_data'] : [];

        $data['name'] = trim((string) ($data['name'] ?? ''));
        $data['address'] = trim((string) ($data['address'] ?? ''));
        $data['address_details'] = trim((string) ($data['address_details'] ?? ''));
        $data['status'] = 'A';

        if ($data['name'] === '' || $data['address'] === '') {
            fn_set_notification('E', __('error'), __('talario_vendor_cabinet.location_required_fields'));
            $redirect = $location_id ? 'talario_locations.update?location_id=' . $location_id : 'talario_locations.update';
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
        $center = fn_talario_vendor_cabinet_get_center($company_id);
        $company_data = fn_get_company_data($company_id, DESCR_SL, ['skip_cache' => true]);

        if (empty($center['name']) && !empty($company_data['company'])) {
            $center['name'] = (string) $company_data['company'];
        }
        $center['description'] = $normalize_center_description($center['description'] ?? '');

        Tygh::$app['view']->assign([
            'talario_locations' => $service->getLocations(),
            'talario_center' => $center,
        ]);
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
