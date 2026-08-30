<?php

use Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService;
use Tygh\Addons\TalarioVendorCabinet\Service\BookingBridgeService;
use Tygh\Addons\ProductVariations\Product\FeaturePurposes;
use Tygh\Addons\ProductVariations\Product\Group\GroupFeatureCollection;
use Tygh\Addons\ProductVariations\Request\GenerateProductsAndAttachToGroupRequest;
use Tygh\Addons\ProductVariations\Request\GenerateProductsAndCreateGroupRequest;
use Tygh\Addons\ProductVariations\ServiceProvider as VariationsServiceProvider;
use Tygh\Enum\Addons\VendorDataPremoderation\ProductStatuses;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\UserTypes;
use Tygh\Registry;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

$company_id = (int) fn_get_runtime_company_id();
if (!$company_id) {
    return [CONTROLLER_STATUS_DENIED];
}
fn_talario_vendor_cabinet_ensure_moderation_storage();

$load_owned_product = static function ($product_id) use ($company_id) {
    $product_id = (int) $product_id;
    if (!$product_id) {
        return null;
    }

    $product = db_get_row(
        'SELECT p.product_id, p.company_id, p.status, pd.product, pd.full_description, pd.short_description, '
        . 'pd.meta_keywords, pd.search_words, pd.address FROM ?:products AS p '
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
    $product['image_pairs'] = fn_get_image_pairs($product_id, 'product', 'A', true, true, DESCR_SL);
    $product['variations'] = db_get_array(
        'SELECT p.product_id, pd.product, pp.price FROM ?:products AS p '
        . 'INNER JOIN ?:product_variation_group_products AS v ON v.product_id = p.product_id '
        . 'INNER JOIN ?:product_variation_group_products AS parent ON parent.group_id = v.group_id '
        . 'AND parent.product_id = ?i '
        . 'LEFT JOIN ?:product_descriptions AS pd ON pd.product_id = p.product_id AND pd.lang_code = ?s '
        . 'LEFT JOIN ?:product_prices AS pp ON pp.product_id = p.product_id AND pp.lower_limit = 1 '
        . 'AND pp.usergroup_id = 0 WHERE p.product_id <> ?i AND p.company_id = ?i '
        . 'GROUP BY p.product_id ORDER BY p.product_id',
        $product_id,
        DESCR_SL,
        $product_id,
        $company_id
    );
    if (function_exists('fn_vendor_data_premoderation_get_premoderation')) {
        $premoderation = fn_vendor_data_premoderation_get_premoderation([$product_id]);
        $product['talario_revision_comment'] = trim((string) ($premoderation[$product_id]['reason'] ?? ''));
    }

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

$get_variation_axes = static function ($category_id, $product_id = 0) {
    $feature_ids = [];
    if ($product_id) {
        $group_id = VariationsServiceProvider::getGroupRepository()->findGroupIdByProductId($product_id);
        if ($group_id) {
            $feature_ids = VariationsServiceProvider::getGroupRepository()
                ->findGroupFeatureCollectionByGroupId($group_id)
                ->getFeatureIds();
        }
    }

    $params = [
        'exclude_group' => true,
        'variants' => true,
        'plain' => true,
        'statuses' => [ObjectStatuses::ACTIVE, ObjectStatuses::HIDDEN],
        'purpose' => FeaturePurposes::getAll(),
    ];
    if ($feature_ids) {
        $params['feature_id'] = $feature_ids;
    } elseif ($category_id) {
        $params['category_ids'] = [(int) $category_id];
    } else {
        return [];
    }
    [$features] = fn_get_product_features($params, 0, DESCR_SL);
    return array_values(array_filter($features, static function (array $feature) {
        return !empty($feature['variants']) && in_array($feature['purpose'], FeaturePurposes::getAll(), true);
    }));
};

$sync_variations = static function (
    $product_id,
    array $rows,
    array $delete_ids,
    $validate_only = false,
    $category_id = 0
) use ($company_id, $get_variation_axes) {
    $service = VariationsServiceProvider::getService();
    $group_repository = VariationsServiceProvider::getGroupRepository();
    $product_repository = VariationsServiceProvider::getProductRepository();
    $group_id = $group_repository->findGroupIdByProductId($product_id);

    $validated_delete_ids = [];
    foreach (array_unique(array_map('intval', $delete_ids)) as $delete_id) {
        if (!$delete_id || !$group_id || $delete_id === (int) $product_id) {
            throw new InvalidArgumentException('Нельзя удалить родительское занятие или неизвестный вариант.');
        }
        $owned = (int) db_get_field(
            'SELECT p.company_id FROM ?:products p INNER JOIN ?:product_variation_group_products gp '
            . 'ON gp.product_id = p.product_id WHERE gp.group_id = ?i AND p.product_id = ?i',
            $group_id,
            $delete_id
        );
        if ($owned !== $company_id) {
            throw new RuntimeException('Cross-company variation operation is forbidden');
        }
        $validated_delete_ids[] = $delete_id;
    }

    $category_id = $category_id ?: (int) db_get_field(
        'SELECT category_id FROM ?:products_categories WHERE product_id = ?i ORDER BY link_type DESC LIMIT 1', $product_id
    );
    $axes = $get_variation_axes($category_id, $product_id);
    $allowed = [];
    foreach ($axes as $axis) {
        foreach ((array) $axis['variants'] as $variant) {
            $allowed[(int) $axis['feature_id']][(int) $variant['variant_id']] = true;
        }
    }

    $feature_ids = [];
    $combination_feature_ids = null;
    $combination_prices = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $price_raw = str_replace(',', '.', trim((string) ($row['price'] ?? '')));
        $variants = array_filter(array_map('intval', (array) ($row['variants'] ?? [])));
        if (!$variants || !is_numeric($price_raw) || (float) $price_raw < 0) {
            throw new InvalidArgumentException('Заполните все особенности и цену варианта.');
        }
        foreach ($variants as $feature_id => $variant_id) {
            $feature_id = (int) $feature_id;
            if (empty($allowed[$feature_id][$variant_id])) {
                throw new InvalidArgumentException('Выбрана недоступная особенность варианта.');
            }
            $feature_ids[$feature_id] = $feature_id;
        }
        $row_feature_ids = array_map('intval', array_keys($variants));
        sort($row_feature_ids);
        if ($combination_feature_ids === null) {
            $combination_feature_ids = $row_feature_ids;
        } elseif ($row_feature_ids !== $combination_feature_ids) {
            throw new InvalidArgumentException('Во всех сочетаниях нужно заполнить одинаковые особенности.');
        }
        $combination_id = $product_repository->generateCombinationId(array_values($variants));
        if (array_key_exists($combination_id, $combination_prices)) {
            throw new InvalidArgumentException('Такое сочетание добавлено больше одного раза.');
        }
        $combination_prices[$combination_id] = (float) $price_raw;
    }
    if ($validate_only) {
        return;
    }

    foreach ($validated_delete_ids as $delete_id) {
        $result = $service->detachProductFromGroup($group_id, $delete_id);
        if ($result->isFailure()) {
            throw new InvalidArgumentException(implode(' ', $result->getErrors()));
        }
        fn_delete_product($delete_id);
    }
    if (!$combination_prices) {
        return;
    }

    if ($group_id) {
        $request = GenerateProductsAndAttachToGroupRequest::create(
            $group_id,
            $product_id,
            array_keys($combination_prices)
        );
        $result = $service->generateProductsAndAttachToGroup($request);
    } else {
        $features = $product_repository->findFeatures(array_values($feature_ids));
        $request = GenerateProductsAndCreateGroupRequest::create(
            $product_id,
            array_keys($combination_prices),
            GroupFeatureCollection::createFromFeatureList($features)
        );
        $result = $service->generateProductsAndCreateGroup($request);
    }
    if ($result->isFailure()) {
        throw new InvalidArgumentException(implode(' ', $result->getErrors()));
    }

    $group_id = $group_repository->findGroupIdByProductId($product_id);
    $group_product_ids = $group_repository->findGroupById($group_id)->getProductIds();
    foreach ($group_product_ids as $variation_id) {
        if ((int) $variation_id === (int) $product_id) {
            continue;
        }
        $group_feature_ids = $group_repository->findGroupFeatureCollectionByGroupId($group_id)->getFeatureIds();
        sort($group_feature_ids);
        $variant_ids = db_get_fields(
            'SELECT variant_id FROM ?:product_features_values WHERE product_id = ?i '
            . 'AND feature_id IN (?n) AND variant_id > 0 ORDER BY feature_id',
            $variation_id,
            $group_feature_ids
        );
        $combination_id = $product_repository->generateCombinationId(array_map('intval', $variant_ids));
        if (array_key_exists($combination_id, $combination_prices)) {
            fn_update_product(['price' => $combination_prices[$combination_id]], $variation_id, DESCR_SL);
            unset($combination_prices[$combination_id]);
        }
    }
    if ($combination_prices) {
        throw new InvalidArgumentException('Не удалось сопоставить созданный вариант с выбранным сочетанием.');
    }
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
        $action = (string) ($_REQUEST['save_action'] ?? 'draft');
        if (!in_array($action, ['draft', 'preview', 'submit'], true)) {
            return [CONTROLLER_STATUS_DENIED];
        }
        if ($existing && $existing['status'] === ObjectStatuses::ACTIVE && $action === 'draft') {
            fn_set_notification(
                'W',
                __('warning'),
                'Штатная модерация CS-Cart не хранит отдельную черновую версию опубликованного занятия. '
                . 'Чтобы не снять текущую карточку с витрины, изменения не сохранены. '
                . 'Их можно передать только кнопкой «Отправить на проверку».'
            );
            return [CONTROLLER_STATUS_REDIRECT, 'talario_classes.update?product_id=' . $product_id];
        }
        if ($existing && $existing['status'] === ObjectStatuses::ACTIVE && $action === 'preview') {
            $image_request = [];
            foreach ($_REQUEST as $request_key => $request_value) {
                if (preg_match('/^(?:(?:file_|type_).+_(?:detailed|data)|.+_image_data)$/D', (string) $request_key)) {
                    $image_request[$request_key] = $request_value;
                }
            }
            $preview_token = fn_talario_vendor_cabinet_store_preview_revision(
                $product_id,
                $company_id,
                (int) Tygh::$app['session']['auth']['user_id'],
                [
                    'class_data' => $class_data,
                    'product_data' => (array) ($_REQUEST['product_data'] ?? []),
                    'image_request' => $image_request,
                ]
            );
            fn_set_notification('N', __('notice'), 'Предварительный просмотр не изменяет опубликованную карточку.');
            return [
                CONTROLLER_STATUS_REDIRECT,
                'talario_classes.update?product_id=' . $product_id . '&open_preview=1&talario_preview_token=' . $preview_token
            ];
        }
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
            $variation_prices = isset($class_data['variation_prices']) && is_array($class_data['variation_prices'])
                ? $class_data['variation_prices']
                : [];
            $validated_variation_prices = [];
            foreach ($variation_prices as $variation_id => $variation_price_raw) {
                $variation_id = (int) $variation_id;
                $variation_price_raw = str_replace(',', '.', trim((string) $variation_price_raw));
                $variation_company_id = (int) db_get_field(
                    'SELECT child.company_id FROM ?:products AS child '
                    . 'INNER JOIN ?:product_variation_group_products AS child_group '
                    . 'ON child_group.product_id = child.product_id '
                    . 'INNER JOIN ?:product_variation_group_products AS parent_group '
                    . 'ON parent_group.group_id = child_group.group_id AND parent_group.product_id = ?i '
                    . 'WHERE child.product_id = ?i',
                    $product_id,
                    $variation_id
                );
                if (!$variation_id || $variation_company_id !== $company_id || !is_numeric($variation_price_raw)
                    || (float) $variation_price_raw < 0) {
                    return [CONTROLLER_STATUS_DENIED];
                }
                $validated_variation_prices[$variation_id] = (float) $variation_price_raw;
            }
            $location = $locations_by_id[$location_id];
            $removed_pair_ids = array_values(array_unique(array_filter(array_map(
                'intval',
                (array) ($_REQUEST['product_data']['removed_image_pair_ids'] ?? [])
            ))));
            if ($removed_pair_ids) {
                if (!$product_id) {
                    return [CONTROLLER_STATUS_DENIED];
                }
                $owned_pair_ids = db_get_fields(
                    'SELECT pair_id FROM ?:images_links WHERE pair_id IN (?n) AND object_id = ?i AND object_type = ?s',
                    $removed_pair_ids,
                    $product_id,
                    'product'
                );
                if (array_diff($removed_pair_ids, array_map('intval', $owned_pair_ids))) {
                    return [CONTROLLER_STATUS_DENIED];
                }
            }
            $product_data = [
                'product' => $name,
                'company_id' => $company_id,
                'category_ids' => [$category_id],
                'main_category' => $category_id,
                'price' => $price,
                'full_description' => (string) ($class_data['full_description'] ?? ''),
                'short_description' => trim((string) ($class_data['catalog_age'] ?? '')),
                'meta_keywords' => trim((string) ($class_data['meta_keywords'] ?? '')),
                'search_words' => trim((string) ($class_data['meta_keywords'] ?? '')),
                'address' => (string) $location['address'],
                'zero_price_action' => 'P',
                'removed_image_pair_ids' => $removed_pair_ids,
            ];
            // New activities start privately. Existing product status is deliberately
            // omitted: vendor_data_premoderation owns all published-product transitions.
            if (!$existing) {
                $product_data['status'] = ObjectStatuses::HIDDEN;
            }

            $new_variations = isset($class_data['new_variations']) && is_array($class_data['new_variations'])
                ? $class_data['new_variations']
                : [];
            $delete_variations = isset($class_data['delete_variations']) && is_array($class_data['delete_variations'])
                ? $class_data['delete_variations']
                : [];
            try {
                // Validate every variation and deletion before fn_update_product can touch images.
                $sync_variations($product_id, $new_variations, $delete_variations, true, $category_id);
            } catch (InvalidArgumentException $e) {
                fn_set_notification('E', __('error'), $e->getMessage());
                return [CONTROLLER_STATUS_REDIRECT, $product_id
                    ? 'talario_classes.update?product_id=' . $product_id
                    : 'talario_classes.add'];
            } catch (RuntimeException $e) {
                return [CONTROLLER_STATUS_DENIED];
            }

            $transaction_open = false;
            $saved_product_id = 0;
            $save_succeeded = false;
            $original_user_type = (string) Tygh::$app['session']['auth']['user_type'];
            Registry::set('talario_vendor_cabinet.draft_guard', [
                'enabled' => true,
                'product_id' => $product_id,
                'company_id' => $company_id,
            ], true);
            // vendor_data_premoderation has no "skip" flag before its request function.
            // Run only this internal update cycle as an administrator, then restore the
            // vendor identity and issue exactly one explicit request for submit below.
            Tygh::$app['session']['auth']['user_type'] = UserTypes::ADMIN;
            try {
                db_query('START TRANSACTION');
                $transaction_open = true;
                $saved_product_id = fn_update_product($product_data, $product_id, DESCR_SL);
                if (!$saved_product_id) {
                    throw new RuntimeException('Не удалось сохранить занятие.');
                }
                if (!$product_id) {
                    Registry::set('talario_vendor_cabinet.draft_guard.product_id', (int) $saved_product_id, true);
                }

                foreach ($validated_variation_prices as $variation_id => $variation_price) {
                    fn_update_product(['price' => $variation_price], $variation_id, DESCR_SL);
                }
                $sync_variations(
                    (int) $saved_product_id,
                    $new_variations,
                    $delete_variations
                );

                $variation_min_price = db_get_field(
                    'SELECT MIN(pp.price) FROM ?:product_variation_group_products parent '
                    . 'INNER JOIN ?:product_variation_group_products child ON child.group_id = parent.group_id '
                    . 'INNER JOIN ?:product_prices pp ON pp.product_id = child.product_id '
                    . 'AND pp.lower_limit = 1 AND pp.usergroup_id = 0 '
                    . 'WHERE parent.product_id = ?i AND child.product_id <> ?i',
                    $saved_product_id,
                    $saved_product_id
                );
                if ($variation_min_price !== null) {
                    fn_update_product(['price' => (float) $variation_min_price], $saved_product_id, DESCR_SL);
                }

                $affected_ids = [(int) $saved_product_id];
                $group_id = VariationsServiceProvider::getGroupRepository()
                    ->findGroupIdByProductId((int) $saved_product_id);
                if ($group_id) {
                    $affected_ids = VariationsServiceProvider::getGroupRepository()
                        ->findGroupById($group_id)
                        ->getProductIds();
                }
                db_query(
                    'UPDATE ?:products SET status = ?s WHERE product_id IN (?n)',
                    ObjectStatuses::HIDDEN,
                    $affected_ids
                );
                if (function_exists('fn_vendor_data_premoderation_delete_premoderation')) {
                    fn_vendor_data_premoderation_delete_premoderation(array_map('intval', $affected_ids));
                }
                if ($action === 'submit') {
                    if (!function_exists('fn_vendor_data_premoderation_request_approval_for_products')) {
                        throw new RuntimeException('Модуль проверки занятий недоступен.');
                    }
                    // The moderation record must restore the parent to Active after
                    // approval. Variations stay private and are activated by the
                    // approval hook registered by this add-on.
                    db_query(
                        'UPDATE ?:products SET status = ?s WHERE product_id = ?i',
                        ObjectStatuses::ACTIVE,
                        $saved_product_id
                    );
                    fn_talario_vendor_cabinet_mark_class_moderation(
                        (int) $saved_product_id,
                        $company_id,
                        (int) $group_id,
                        array_values(array_diff(array_map('intval', $affected_ids), [(int) $saved_product_id]))
                    );
                    Tygh::$app['session']['auth']['user_type'] = $original_user_type;
                    Registry::del('talario_vendor_cabinet.draft_guard');

                    // The only approval request made by this user action.
                    fn_vendor_data_premoderation_request_approval_for_products([(int) $saved_product_id], true);
                    $moderation = db_get_row(
                        'SELECT p.status, pm.original_status FROM ?:products p '
                        . 'LEFT JOIN ?:premoderation_products pm ON pm.product_id = p.product_id '
                        . 'WHERE p.product_id = ?i',
                        $saved_product_id
                    );
                    if (($moderation['status'] ?? '') !== ProductStatuses::REQUIRES_APPROVAL
                        || ($moderation['original_status'] ?? '') !== ObjectStatuses::ACTIVE) {
                        throw new RuntimeException('Занятие не удалось корректно отправить на проверку.');
                    }
                } else {
                    fn_talario_vendor_cabinet_clear_class_moderation([(int) $saved_product_id], true);
                }
                db_query('COMMIT');
                $transaction_open = false;
                $save_succeeded = true;
            } catch (Throwable $e) {
                if ($transaction_open) {
                    db_query('ROLLBACK');
                    $transaction_open = false;
                }
                fn_set_notification('E', __('error'), $e->getMessage());
            } finally {
                Tygh::$app['session']['auth']['user_type'] = $original_user_type;
                Registry::del('talario_vendor_cabinet.draft_guard');
                if ($transaction_open) {
                    db_query('ROLLBACK');
                }
            }

            if ($saved_product_id && $save_succeeded) {
                // Ecarter tables may be MyISAM. Update them only after the
                // transactional product/variation changes have committed.
                foreach (array_unique(array_map('intval', $delete_variations)) as $deleted_variation_id) {
                    db_query('DELETE FROM ?:ec_table_booking_system WHERE product_id = ?i', $deleted_variation_id);
                }
                (new BookingBridgeService())->syncProductVariants((int) $saved_product_id, $company_id);
                fn_set_notification(
                    'N',
                    __('notice'),
                    $action === 'submit'
                        ? 'Занятие отправлено на проверку Talario.'
                        : 'Черновик занятия сохранён.'
                );
                if ($action === 'preview') {
                    return [
                        CONTROLLER_STATUS_REDIRECT,
                        'talario_classes.update?product_id=' . (int) $saved_product_id . '&open_preview=1'
                    ];
                }
                return [CONTROLLER_STATUS_REDIRECT, 'talario_classes.update?product_id=' . (int) $saved_product_id];
            }
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
    if ($products && function_exists('fn_vendor_data_premoderation_get_premoderation')) {
        $premoderation = fn_vendor_data_premoderation_get_premoderation(array_map('intval', array_column($products, 'product_id')));
        foreach ($products as &$product) {
            $product['premoderation_reason'] = trim((string) ($premoderation[$product['product_id']]['reason'] ?? ''));
        }
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
    if (!$context['categories']) {
        fn_set_notification(
            'E',
            __('error'),
            'В Talario не настроен список разрешённых категорий. Обратитесь к администратору Talario.'
        );
        return [CONTROLLER_STATUS_REDIRECT, 'talario_classes.manage'];
    }
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
            'image_pairs' => [],
        ];
    }

    Tygh::$app['view']->assign([
        'talario_class' => $product,
        'talario_class_locations' => $context['locations'],
        'talario_class_categories' => $context['categories'],
        'talario_class_location_id' => $context['selected_location_id'],
        'talario_class_category_id' => $context['selected_category_id'],
        'talario_variation_axes' => $get_variation_axes(
            (int) $context['selected_category_id'],
            (int) $product['product_id']
        ),
    ]);
    if ($product_id && !empty($_REQUEST['open_preview'])) {
        $preview_token = isset($_REQUEST['talario_preview_token'])
            ? (string) $_REQUEST['talario_preview_token']
            : '';
        $preview_product = fn_get_product_data(
            $product_id,
            Tygh::$app['session']['auth'],
            DESCR_SL,
            '',
            false,
            false,
            false,
            false,
            false,
            false,
            true
        );
        if ($preview_product && (int) $preview_product['company_id'] === $company_id) {
            $storefront_repository = Tygh::$app['storefront.repository'];
            $storefront = $storefront_repository->findByCompanyId($company_id);
            $storefront = empty($storefront) ? $storefront_repository->findDefault() : $storefront;
            $language = Registry::get('settings.Appearance.frontend_default_language');
            Tygh::$app['view']->assign('talario_preview_url', fn_get_preview_url(
                'products.view?product_id=' . $product_id
                    . '&storefront_id=' . (int) $storefront->storefront_id
                    . ($preview_token !== '' ? '&talario_preview_token=' . rawurlencode($preview_token) : ''),
                $preview_product,
                (int) Tygh::$app['session']['auth']['user_id'],
                $language
            ));
        }
    }
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
