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


/**
 * Stores only a SHA-256 hash of the dedicated Partner Sync service token.
 *
 * @param string $value Setting value.
 */
function fn_settings_actions_addons_talario_analytics_partner_sync_token(&$value)
{
    fn_settings_actions_addons_talario_analytics_api_token($value);
}


/**
 * Validates comma/space-separated Partner Sync source IP or CIDR rules.
 *
 * @param string $value Setting value.
 */
function fn_settings_actions_addons_talario_analytics_partner_sync_allowed_ips(&$value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return;
    }

    $rules = array_values(array_filter(array_map('trim', preg_split('/[\s,;]+/', $value))));
    foreach ($rules as $rule) {
        if (strpos($rule, '/') === false) {
            if (!filter_var($rule, FILTER_VALIDATE_IP)) {
                fn_set_notification('E', __('error'), 'Partner Sync source list contains an invalid IP address.');
                $value = '';
                return;
            }
            continue;
        }

        [$network, $prefix_raw] = array_pad(explode('/', $rule, 2), 2, '');
        if (!filter_var($network, FILTER_VALIDATE_IP) || !ctype_digit($prefix_raw)) {
            fn_set_notification('E', __('error'), 'Partner Sync source list contains an invalid CIDR rule.');
            $value = '';
            return;
        }

        $binary = inet_pton($network);
        $prefix = (int) $prefix_raw;
        $max_bits = $binary === false ? -1 : strlen($binary) * 8;
        if ($max_bits < 0 || $prefix < 0 || $prefix > $max_bits) {
            fn_set_notification('E', __('error'), 'Partner Sync source list contains an invalid CIDR prefix.');
            $value = '';
            return;
        }
    }

    $value = implode(',', $rules);
}
