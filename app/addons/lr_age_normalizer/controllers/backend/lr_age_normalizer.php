<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Addons\LrAgeNormalizer\Service\AgeFeatureConverter;
use Tygh\Enum\NotificationSeverity;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/** @var string $mode */

$source_feature_id = isset($_REQUEST['source_feature_id'])
    ? (int) $_REQUEST['source_feature_id']
    : (int) Registry::get('addons.lr_age_normalizer.age_feature_id');

$target_feature_id = isset($_REQUEST['target_feature_id'])
    ? (int) $_REQUEST['target_feature_id']
    : (int) Registry::get('addons.lr_age_normalizer.age_target_feature_id');

if ($mode === 'preview') {
    if ($source_feature_id <= 0 || $target_feature_id <= 0) {
        fn_set_notification(
            NotificationSeverity::ERROR,
            __('error'),
            __('lr_age_normalizer.age_feature_converter_feature_ids_are_not_configured')
        );

        return [CONTROLLER_STATUS_REDIRECT, 'addons.update?addon=lr_age_normalizer&selected_section=settings'];
    }

    $result = (new AgeFeatureConverter())->preview($source_feature_id, $target_feature_id);

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($mode === 'run') {
    if ($source_feature_id <= 0 || $target_feature_id <= 0) {
        fn_set_notification(
            NotificationSeverity::ERROR,
            __('error'),
            __('lr_age_normalizer.age_feature_converter_feature_ids_are_not_configured')
        );

        return [CONTROLLER_STATUS_REDIRECT, 'addons.update?addon=lr_age_normalizer&selected_section=settings'];
    }

    $result = (new AgeFeatureConverter())->convert($source_feature_id, $target_feature_id);
    $skipped_product_ids = [];

    foreach ($result['skipped_products'] as $skipped_product) {
        if (empty($skipped_product['product_id'])) {
            continue;
        }

        $skipped_product_ids[] = (int) $skipped_product['product_id'];
    }

    $skipped_product_ids = array_values(array_unique($skipped_product_ids));

    if ($result['summary']['products_skipped_missing_target_variants'] > 0) {
        fn_set_notification(
            NotificationSeverity::ERROR,
            __('error'),
            __('lr_age_normalizer.age_feature_converter_target_variants_are_missing', [
                '[target_feature_id]' => $target_feature_id,
            ])
        );
    } else {
        fn_set_notification(
            NotificationSeverity::NOTICE,
            __('notice'),
            __('lr_age_normalizer.age_feature_converter_completed', [
                '[updated]' => $result['summary']['products_updated'],
                '[unchanged]' => $result['summary']['products_unchanged'],
                '[skipped]' => $skipped_product_ids ? implode(', ', $skipped_product_ids) : '-',
            ])
        );
    }
}
