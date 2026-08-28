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

    return function_exists('mb_substr')
        ? mb_substr($value, 0, 180, 'UTF-8')
        : substr($value, 0, 180);
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'update_center') {
        $center_data = isset($_REQUEST['center_data']) && is_array($_REQUEST['center_data'])
            ? $_REQUEST['center_data']
            : [];
        $center = fn_talario_vendor_cabinet_get_center($company_id);

        $name = trim((string) ($center_data['name'] ?? ''));
        $description = $normalize_center_description($center_data['description'] ?? '');
        $address = trim((string) ($center_data['address'] ?? ''));
        $address_details = trim((string) ($center_data['address_details'] ?? ''));

        if ($name === '' || $address === '') {
            fn_set_notification('E', __('error'), 'Укажите название центра и основной адрес.');
            return [CONTROLLER_STATUS_REDIRECT, 'talario_locations.manage'];
        }

        try {
            $primary_location_id = (int) ($center['primary_location_id'] ?? 0);
            $location_data = [
                'name' => $name,
                'address' => $address,
                'address_details' => $address_details,
                'status' => 'A',
            ];

            if ($primary_location_id && $service->locationBelongsToCompany($primary_location_id, $company_id)) {
                $service->updateLocation($primary_location_id, $location_data);
            } else {
                $primary_location_id = (int) $service->createLocation($location_data);
            }

            fn_talario_vendor_cabinet_update_center($company_id, [
                'name' => $name,
                'description' => $description,
                'address' => $address,
                'address_details' => $address_details,
                'primary_location_id' => $primary_location_id,
            ]);

            fn_set_notification('N', __('notice'), 'Информация о центре сохранена.');
        } catch (InvalidArgumentException $e) {
            fn_set_notification('E', __('error'), $e->getMessage());
        } catch (RuntimeException $e) {
            return [CONTROLLER_STATUS_DENIED];
        }

        return [CONTROLLER_STATUS_REDIRECT, 'talario_locations.manage'];
    }

    if ($mode === 'update') {
        $location_id = isset($_REQUEST['location_id']) ? (int) $_REQUEST['location_id'] : 0;
        $data = isset($_REQUEST['location_data']) && is_array($_REQUEST['location_data']) ? $_REQUEST['location_data'] : [];

        $data['name'] = trim((string) ($data['name'] ?? ''));
        $data['address'] = trim((string) ($data['address'] ?? ''));
        $data['address_details'] = trim((string) ($data['address_details'] ?? ''));
        $data['status'] = 'A';

        if ($data['address'] === '') {
            fn_set_notification('E', __('error'), 'Укажите адрес филиала.');
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
        $center = fn_talario_vendor_cabinet_get_center($company_id);
        if ($location_id === (int) ($center['primary_location_id'] ?? 0)) {
            return [CONTROLLER_STATUS_DENIED];
        }

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

        $locations = $service->getLocations();
        $primary_location_id = (int) ($center['primary_location_id'] ?? 0);
        if ($primary_location_id) {
            foreach ($locations as $location) {
                if ((int) $location['location_id'] === $primary_location_id) {
                    if (empty($center['address'])) {
                        $center['address'] = (string) $location['address'];
                    }
                    if (empty($center['address_details'])) {
                        $center['address_details'] = (string) $location['address_details'];
                    }
                    break;
                }
            }
        }

        $branches = array_values(array_filter($locations, static function (array $location) use ($primary_location_id) {
            return (int) $location['location_id'] !== $primary_location_id;
        }));

        Tygh::$app['view']->assign([
            'talario_locations' => $branches,
            'talario_center' => $center,
        ]);
    } catch (RuntimeException $e) {
        return [CONTROLLER_STATUS_DENIED];
    }
} elseif ($mode === 'update') {
    $location_id = isset($_REQUEST['location_id']) ? (int) $_REQUEST['location_id'] : 0;
    $center = fn_talario_vendor_cabinet_get_center($company_id);
    if ($location_id && $location_id === (int) ($center['primary_location_id'] ?? 0)) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

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
