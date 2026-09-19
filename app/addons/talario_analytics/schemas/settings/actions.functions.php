<?php

defined('BOOTSTRAP') or die('Access denied');

use Tygh\Registry;

function fn_talario_analytics_token_is_distinct(string $hash, string $other_setting): bool
{
    $other_hash = trim((string) Registry::get('addons.talario_analytics.' . $other_setting));
    if ($other_hash !== '' && !preg_match('/^sha256:[a-f0-9]{64}$/', $other_hash)) {
        $other_hash = 'sha256:' . hash('sha256', $other_hash);
    }
    return $other_hash === '' || hash_equals($other_hash, $hash);
}

/**
 * Stores only a SHA-256 hash of the bearer token in CS-Cart settings.
 * Existing hashed values are preserved on subsequent saves.
 *
 * @param string $value Setting value.
 */
function fn_settings_actions_addons_talario_analytics_api_token(&$value)
{
    $value = trim((string) $value);

    if ($value === '') {
        return;
    }

    if (preg_match('/^sha256:[a-f0-9]{64}$/', $value)) {
        $hash = $value;
    } else {
        if (strlen($value) < 32) {
            fn_set_notification('E', __('error'), __('talario_analytics.token_too_short'));
            $value = '';
            return;
        }
        $hash = 'sha256:' . hash('sha256', $value);
    }

    if (!fn_talario_analytics_token_is_distinct($hash, 'partner_sync_token')) {
        fn_set_notification('E', __('error'), __('talario_analytics.tokens_must_differ'));
        $value = '';
        return;
    }

    $value = $hash;
}

/** Stores only the SHA-256 hash of the Partner Sync token. */
function fn_settings_actions_addons_talario_analytics_partner_sync_token(&$value)
{
    $value = trim((string) $value);

    if ($value === '') {
        return;
    }

    if (preg_match('/^sha256:[a-f0-9]{64}$/', $value)) {
        $hash = $value;
    } else {
        if (strlen($value) < 32) {
            fn_set_notification('E', __('error'), __('talario_analytics.token_too_short'));
            $value = '';
            return;
        }
        $hash = 'sha256:' . hash('sha256', $value);
    }

    if (!fn_talario_analytics_token_is_distinct($hash, 'api_token')) {
        fn_set_notification('E', __('error'), __('talario_analytics.tokens_must_differ'));
        $value = '';
        return;
    }

    $value = $hash;
}
