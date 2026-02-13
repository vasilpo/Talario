<?php

use Tygh\Registry;
use Tygh\Enum\YesNo;

function fn_get_provider_settings()
{
    $addon_settings = Registry::get('settings.extended_social_buttons.general');

    $provider_settings = [];
    foreach ($addon_settings['social_settings'] as $provider => $setting_value) {
        if ($setting_value === YesNo::NO) {
            continue;
        }
        $provider_settings[$provider]['enabled'] = $setting_value;
        $func_name = 'fn_' . $provider . '_settings_prepare';

        if (is_callable($func_name)) {
            $provider_settings[$provider]['data'] = call_user_func($func_name, $addon_settings);
        }
    }

    return $provider_settings;
}

function fn_telegram_settings_prepare($settings)
{
    $params = [
        'text' => $settings['telegram_comment_text']
    ];
    return $params;
}

function fn_viber_settings_prepare($settings)
{
    $params = [
        'text' => $settings['viber_text']
    ];

    return $params;
}

function fn_twitter_settings_prepare($settings)
{
    $params = [
        'hashtags' => $settings['twitter_hashtags'],
        'text' => $settings['twitter_text'],
        'via' => $settings['twitter_via']
    ];
    $twitter_params = '';
    foreach ($params as $field_name => $value) {
        if (!empty($value)) {
            $twitter_params .= '&' . $field_name . '=' . $value;
        }
    }

    return $twitter_params;
}

function fn_odnoklassniki_settings_prepare($settings)
{
    $params = [
        'text' => $settings['odnoklassniki_text']
    ];

    return $params;
}
