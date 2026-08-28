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
        'SELECT p.product_id, p.company_id, p.status, pd.product, pd.full_description, pd.short_description, '
        . 'pd.meta_keywords, pd.address FROM ?:products AS p '
        . 'LEFT JOIN ?:product_descriptions AS pd ON pd.product_id = p.product_id AND pd.lang_code = ?s '
        . 'WHERE p.product_id = ?i',
        DESCR_SL,
        $product_id
    );

    if (!$product || (int) $product['company_id'] !== $company_id) {
        return null;
    }

    $product['price'] = (float) db_get_field(
        'SELECT price FROM ?:product_prices WHERE product_id = ?i ORDER BY usergroup_id, lower_limit LIMIT 1',
        $product_id
    );
    $product['category_id'] = (int) db_get_field(
        'SELECT category_id FROM ?:products_categories WHERE product_id = ?i ORDER BY link_type DESC LIMIT 1',
        $product_id
    );
    $product['main_pair'] = fn_get_image_pairs($product_id, 'product', 'M', true, true, DESCR_SL);

    return $product;
};

$get_editor_context = static function ($product = null) use ($company_id) {
    $schedule_service = new ScheduleResourceService();
    $center = fn_talario_vendor_cabinet_get_center($company_id);
    $locations = array_values(array_filter($schedule_service->getLocations(), static function (array $location) {
        return ($location['status'] ?? 'D') === 'A';
    }));
    $categories = fn_talario_vendor_cabinet_get_allowed_categories();
    $allowed_category_ids = array_map('intval', array_column($categories, 'category_id'));

    $selected_location_id = (int) ($center['primary_location_id'] ?? 0);
    $selected_category_id = 0;

    if ($product) {
        foreach ($locations as $location) {
            if (trim((string) ($product['address'] ?? '')) !== ''
                && trim((string) $location['address']) === trim((string) $product['address'])) {
                $selected_location_id = (int) $location['location_id'];
                break;
            }
        }

        $selected_category_id = (int) ($product['category_id'] ?? 0);
        $current_category_id = $selected_category_id;
        while ($current_category_id && !in_array($current_category_id, $allowed_category_ids, true)) {
            $current_category_id = (int) db_get_field(
                'SELECT parent_id FROM ?:categories WHERE category_id = ?i',
                $current_category_id
            );
        }
        if ($current_category_id) {
            $selected_category_id = $current_category_id;
        }
    }

    return [
        'center' => $center,
        'locations' => $locations,
        'categories' => $categories,
        'selected_location_id' => $selected_location_id,
        'selected_category_id' => $selected_category_id,
    ];
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'save_class') {
        fn_trusted_vars('class_data');

        $product_id = isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0;
        $existing = $product_id ? $load_owned_product($product_id) : null;
        if ($product_id && !$existing) {
            return [CONTROLLER_STATUS_DENIED];
        }

        $class_data = isset($_REQUEST['class_data']) && is_array($_REQUEST['class_data'])
            ? $_REQUEST['class_data']
            : [];
        $name = trim((string) ($class_data['product'] ?? ''));
        $location_id = (int) ($class_data['location_id'] ?? 0);
        $category_id = (int) ($class_data['category_id'] ?? 0);
        $price_raw = str_replace(',', '.', trim((string) ($class_data['price'] ?? '0')));
        $price = is_numeric($price_raw) ? (float) $price_raw : -1;

        $context = $get_editor_context($existing);
        $allowed_category_ids = array_map('intval', array_column($context['categories'], 'category_id'));
        $locations_by_id = [];
        foreach ($context['locations'] as $location) {
            $locations_by_id[(int) $location['location_id']] = $location;
        }

        if ($name === '') {
            fn_set_notification('E', __('error'), 'Укажите название занятия.');
        } elseif (!isset($locations_by_id[$location_id])) {
            fn_set_notification('E', __('error'), 'Выберите адрес занятия.');
        } elseif (!in_array($category_id, $allowed_category_ids, true)) {
            fn_set_notification('E', __('error'), 'Выберите категорию из списка Talario.');
        } elseif ($price < 0) {
            fn_set_notification('E', __('error'), 'Укажите корректную цену от 0 ₽.');
        } else {
            $location = $locations_by_id[$location_id];
            $product_data = [
                'product' => $name,
                'company_id' => $company_id,
                'category_ids' => [$category_id],
                'main_category' => $category_id,
                'price' => $price,
                'full_description' => (string) ($class_data['full_description'] ?? ''),
                'short_description' => trim((string) ($class_data['catalog_age'] ?? '')),
                'meta_keywords' => trim((string) ($class_data['meta_keywords'] ?? '')),
                'address' => (string) $location['address'],
                'zero_price_action' => 'P',
                'status' => $existing ? (string) $existing['status'] : ObjectStatuses::ACTIVE,
            ];

            $saved_product_id = fn_update_product($product_data, $product_id, DESCR_SL);
            if ($saved_product_id) {
                fn_set_notification(
                    'N',
                    __('notice'),
                    $product_id
                        ? 'Занятие сохранено. Если изменения требуют проверки, Talario опубликует их после модерации.'
                        : 'Занятие создано и отправлено на проверку Talario.'
                );
                return [CONTROLLER_STATUS_REDIRECT, 'talario_classes.update?product_id=' . (int) $saved_product_id];
            }

            fn_set_notification('E', __('error'), 'Не удалось сохранить занятие.');
        }

        $redirect = $product_id
            ? 'talario_classes.update?product_id=' . $product_id
            : 'talario_classes.add';
        return [CONTROLLER_STATUS_REDIRECT, $redirect];
    }

    if ($mode === 'save_schedule') {
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
} elseif ($mode === 'add' || $mode === 'update') {
    $product_id = isset($_REQUEST['product_id']) ? (int) $_REQUEST['product_id'] : 0;
    $product = $product_id ? $load_owned_product($product_id) : null;
    if ($product_id && !$product) {
        return [CONTROLLER_STATUS_NO_PAGE];
    }

    $context = $get_editor_context($product);
    if (!$product_id && (empty($context['center']['name']) || empty($context['center']['address']) || empty($context['center']['primary_location_id']))) {
        fn_set_notification('W', __('warning'), 'Сначала укажите название и основной адрес центра.');
        return [CONTROLLER_STATUS_REDIRECT, 'talario_locations.manage'];
    }

    if (!$product) {
        $product = [
            'product_id' => 0,
            'product' => '',
            'full_description' => '',
            'short_description' => '',
            'meta_keywords' => '',
            'address' => '',
            'price' => 0,
            'main_pair' => [],
        ];
    }

    Tygh::$app['view']->assign([
        'talario_class' => $product,
        'talario_class_locations' => $context['locations'],
        'talario_class_categories' => $context['categories'],
        'talario_class_location_id' => $context['selected_location_id'],
        'talario_class_category_id' => $context['selected_category_id'],
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
        $schedule_data = $bridge->getFormData($product_id, $company_id);
        if (empty($schedule_data['location_id']) && !empty($product['address'])) {
            foreach ($locations as $location) {
                if (trim((string) $location['address']) === trim((string) $product['address'])) {
                    $schedule_data['location_id'] = (int) $location['location_id'];
                    break;
                }
            }
        }
        Tygh::$app['view']->assign([
            'talario_schedule_product' => $product,
            'talario_schedule_locations' => $locations,
            'talario_schedule_data' => $schedule_data,
            'talario_weekdays' => [
                1 => 'Понедельник', 2 => 'Вторник', 3 => 'Среда', 4 => 'Четверг',
                5 => 'Пятница', 6 => 'Суббота', 7 => 'Воскресенье',
            ],
        ]);
    } catch (RuntimeException $e) {
        return [CONTROLLER_STATUS_DENIED];
    }
}
