<?php

// phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Addons\LrAgeNormalizer\Service\AgeFeatureConverter;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Syncs target age feature from source feature after product save.
 *
 * @param array  $product_data Product data
 * @param int    $product_id   Product identifier
 * @param string $lang_code    Language code
 * @param bool   $create       Creation flag
 *
 * @return void
 */
function fn_lr_age_normalizer_update_product_post(
    array $product_data,
    int $product_id,
    string $lang_code,
    bool $create
): void
{
    $source_feature_id = (int) Registry::get('addons.lr_age_normalizer.age_feature_id');
    $target_feature_id = (int) Registry::get('addons.lr_age_normalizer.age_target_feature_id');

    if ($product_id <= 0 || $source_feature_id <= 0 || $target_feature_id <= 0) {
        return;
    }

    $result = (new AgeFeatureConverter())->syncProduct($product_id, $source_feature_id, $target_feature_id);

    if ($result['status'] === 'skipped_unmapped') {
        fn_lr_age_normalizer_log_issue(
            'lr_age_normalizer.age_feature_sync_unmapped',
            $product_id,
            $source_feature_id,
            [
                '[target_feature_id]' => $target_feature_id,
                '[values]' => implode(', ', $result['unmapped_values']),
            ]
        );
    }

    if ($result['status'] === 'skipped_missing_target_variants') {
        fn_lr_age_normalizer_log_issue(
            'lr_age_normalizer.age_feature_sync_missing_target_variants',
            $product_id,
            $source_feature_id,
            [
                '[target_feature_id]' => $target_feature_id,
                '[values]' => implode(', ', $result['missing_target_ages']),
            ]
        );
    }
}

/**
 * Writes runtime log for age sync issues.
 *
 * @param string $message_key  Message key
 * @param int    $product_id   Product identifier
 * @param int    $feature_id   Source feature identifier
 * @param array  $placeholders Message placeholders
 *
 * @return void
 */
function fn_lr_age_normalizer_log_issue(
    string $message_key,
    int $product_id,
    int $feature_id,
    array $placeholders = []
): void
{
    $message = __($message_key, array_merge([
        '[product_id]' => $product_id,
        '[feature_id]' => $feature_id,
    ], $placeholders));

    fn_log_event('general', 'runtime', [
        'message' => $message,
    ]);
}
