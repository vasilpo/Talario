<?php

use InvalidArgumentException;
use RuntimeException;
use Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService;
use Tygh\Addons\TalarioVendorCabinet\Service\BookingBridgeService;
use Tygh\Enum\Addons\VendorDataPremoderation\ProductStatuses;
use Tygh\Enum\ObjectStatuses;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

$company_id = (int) fn_get_runtime_company_id();
if (!$company_id) {
    return [CONTROLLER_STATUS_DENIED];
}

$load_owned_product = static function ($product_id) use ($company_id) {
    $product_id = (int) $product_id;
    if (!$product_id) {
        return null;
    }
    $product = db_get_row(
        'SELECT p.product_id, p.company_id, p.status, pd.product FROM ?:products AS p '
        . 'LEFT JOIN ?:product_descriptions AS pd ON pd.product_id = p.product_id AND pd.lang_code = ?s '
        . 'WHERE p.product_id = ?i',
        DESCR_SL,
        $product_id
    );
    return ($product && (int) $product['company_id'] === $company_id) ? $product : null;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mode === 'save_schedule') {
    $product_id = isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0;
    $product = $load_owned_product($product_id);
    if (!$product) {
        return [CONTROLLER_STATUS_DENIED];
    }
    $schedule_data = isset($_REQUEST['schedule_data']) && is_array($_REQUEST['schedule_data']) ? $_REQUEST['schedule_data'] : [];
    try {
        $bridge = new BookingBridgeService();
        $bridge->syncProductSchedule($product_id, $company_id, $schedule_data);
        fn_set_notification('N', __('notice'), 'Расписание сохранено. Календарь занятия обновлён.');
    } catch (InvalidArgumentException $e) {
        fn_set_notification('E', __('error'), $e->getMessage());
    } catch (RuntimeException $e) {
        return [CONTROLLER_STATUS_DENIED];
    }
    return [CONTROLLER_STATUS_REDIRECT, 'talario_classes.schedule?product_id=' . $product_id];
}

if ($mode === 'manage') {
    $filter = isset($_REQUEST['talario_status']) ? (string) $_REQUEST['talario_status'] : 'all';
    $params = [
        'company_id' => $company_id,
        'include_child_variations' => false,
        'page' => isset($_REQUEST['page']) ? max(1, (int) $_REQUEST['page']) : 1,
        'extend' => ['description'],
    ];
    if ($filter === 'active') {
        $params['status'] = ObjectStatuses::ACTIVE;
    } elseif ($filter === 'pending') {
        $params['status'] = ProductStatuses::REQUIRES_APPROVAL;
    } elseif ($filter === 'disabled') {
        $params['status'] = [ObjectStatuses::DISABLED, ObjectStatuses::HIDDEN, ProductStatuses::DISAPPROVED];
    } else {
        $filter = 'all';
    }

    [$products, $search] = fn_get_products($params, 24, DESCR_SL);
    $products = array_filter($products, static function (array $product) use ($company_id) {
        return (int) $product['company_id'] === $company_id;
    });
    fn_gather_additional_products_data($products, [
        'get_icon' => true,
        'get_detailed' => true,
        'get_features' => true,
        'get_options' => false,
        'get_discounts' => false,
    ], DESCR_SL);
    foreach ($products as &$product) {
        $product['talario_age'] = $product['product_features'][552]['value'] ?? $product['product_features'][552]['variant'] ?? '';
    }
    unset($product);
    Tygh::$app['view']->assign([
        'talario_products' => $products,
        'talario_search' => $search,
        'talario_filter' => $filter,
    ]);
} elseif ($mode === 'schedule') {
    $product_id = isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0;
    $product = $load_owned_product($product_id);
    if (!$product) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }
    try {
        $schedule_service = new ScheduleResourceService();
        $bridge = new BookingBridgeService($schedule_service);
        $locations = array_values(array_filter($schedule_service->getLocations(), static function (array $location) {
            return ($location['status'] ?? 'D') === 'A';
        }));
        Tygh::$app['view']->assign([
            'talario_schedule_product' => $product,
            'talario_schedule_locations' => $locations,
            'talario_schedule_data' => $bridge->getFormData($product_id, $company_id),
            'talario_weekdays' => [
                1 => 'Понедельник', 2 => 'Вторник', 3 => 'Среда', 4 => 'Четверг',
                5 => 'Пятница', 6 => 'Суббота', 7 => 'Воскресенье',
            ],
        ]);
    } catch (RuntimeException $e) {
        return [CONTROLLER_STATUS_DENIED];
    }
}
