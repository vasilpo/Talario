<?php

defined('BOOTSTRAP') or die('Access denied');

/**
 * Stores only a SHA-256 hash of the bearer token in CS-Cart settings.
 * Existing hashed values are preserved on subsequent saves.
 *
 * @param string $value Setting value.
 */
function fn_settings_actions_addons_talario_analytics_api_token(&$value)
{
    $value = trim((string) $value);

    if ($value === '' || preg_match('/^sha256:[a-f0-9]{64}$/', $value)) {
        return;
    }

    if (strlen($value) < 32) {
        fn_set_notification('E', __('error'), __('talario_analytics.token_too_short'));
        $value = '';
        return;
    }

    $value = 'sha256:' . hash('sha256', $value);
}

/** Stores only the SHA-256 hash of the Partner Sync token. */
function fn_settings_actions_addons_talario_analytics_partner_sync_token(&$value)
{
    $value = trim((string) $value);

    if ($value === '' || preg_match('/^sha256:[a-f0-9]{64}$/', $value)) {
        return;
    }

    if (strlen($value) < 32) {
        fn_set_notification('E', __('error'), __('talario_analytics.token_too_short'));
        $value = '';
        return;
    }

    $value = 'sha256:' . hash('sha256', $value);
}
