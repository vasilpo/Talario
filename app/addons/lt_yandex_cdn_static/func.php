<?php

// phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

use Tygh\Registry;
use Tygh\Settings;
use Tygh\Cdn;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Returns stored CDN options.
 *
 * @return array<string, mixed>
 */
function fn_lt_yandex_cdn_static_get_stored_cdn_options(): array
{
    $raw_options = Settings::instance()->getValue('cdn', '');

    if (empty($raw_options)) {
        return [];
    }

    $options = unserialize($raw_options);

    return is_array($options) ? $options : [];
}

/**
 * Returns selected CDN service.
 *
 * @param array<string, mixed>|null $cdn_data CDN options
 *
 * @return string
 */
function fn_lt_yandex_cdn_static_get_selected_service(array $cdn_data = null): string
{
    $cdn_data = $cdn_data ?: fn_lt_yandex_cdn_static_get_stored_cdn_options();

    return !empty($cdn_data['service']) && $cdn_data['service'] === 'yandex'
        ? 'yandex'
        : 'amazon';
}

/**
 * Returns backend name for the selected service.
 *
 * @param string $service Selected CDN service
 *
 * @return string
 */
function fn_lt_yandex_cdn_static_get_backend_by_service(string $service): string
{
    return $service === 'yandex' ? 'yandex' : 'cloudfront';
}

/**
 * Normalizes CDN form payload before the core controller persists it.
 *
 * @param array<string, mixed> $cdn_data CDN form data
 *
 * @return array<string, mixed>
 */
function fn_lt_yandex_cdn_static_prepare_cdn_data(array $cdn_data): array
{
    $service = fn_lt_yandex_cdn_static_get_selected_service($cdn_data);

    $cdn_data['service'] = $service;
    $cdn_data['is_enabled'] = !empty($cdn_data['is_enabled']) ? 1 : 0;

    if ($service === 'yandex') {
        $cdn_data['cname'] = trim((string) ($cdn_data['cname'] ?? ''));
        $cdn_data['key'] = '';
        $cdn_data['secret'] = '';
        $cdn_data['id'] = '';
        $cdn_data['host'] = '';
        $cdn_data['is_active'] = !empty($cdn_data['cname']) ? 1 : 0;

        return $cdn_data;
    }

    $cdn_data['key'] = trim((string) ($cdn_data['key'] ?? ''));
    $cdn_data['secret'] = trim((string) ($cdn_data['secret'] ?? ''));
    $cdn_data['cname'] = '';

    return $cdn_data;
}

/**
 * Returns CDN base URL for storefront output rewriting.
 *
 * @return string
 */
function fn_lt_yandex_cdn_static_get_storefront_cdn_base_url(): string
{
    if (!defined('AREA') || AREA !== 'C' || fn_lt_yandex_cdn_static_get_selected_service() !== 'yandex') {
        return '';
    }

    try {
        $cdn = Cdn::instance();
    } catch (\Exception $exception) {
        return '';
    }

    if (!$cdn->getOption('is_enabled')) {
        return '';
    }

    $cdn_host = trim((string) $cdn->getHost(), '/');

    if ($cdn_host === '') {
        return '';
    }

    $scheme = parse_url((string) Registry::get('config.current_location'), PHP_URL_SCHEME) ?: 'https';

    return $scheme . '://' . $cdn_host;
}

/**
 * The `init_storage` hook handler.
 *
 * @return void
 */
function fn_lt_yandex_cdn_static_init_storage(): void
{
    Registry::set(
        'config.cdn_backend',
        fn_lt_yandex_cdn_static_get_backend_by_service(fn_lt_yandex_cdn_static_get_selected_service())
    );
}

/**
 * The `init_templater_post` hook handler.
 *
 * @param \Tygh\SmartyEngine\Core $view Smarty view instance
 *
 * @return void
 */
function fn_lt_yandex_cdn_static_init_templater_post($view): void
{
    if (fn_lt_yandex_cdn_static_get_storefront_cdn_base_url() === '') {
        return;
    }

    $view->addExtension(new \Tygh\Addons\LtYandexCdnStatic\SmartyEngine\Extensions\LtYandexCdnStatic());
}

/**
 * Resets Yandex CDN settings to a safe default when the add-on is disabled.
 *
 * @return void
 */
function fn_lt_yandex_cdn_static_reset_service_configuration(): void
{
    $cdn_options = fn_lt_yandex_cdn_static_get_stored_cdn_options();

    if (fn_lt_yandex_cdn_static_get_selected_service($cdn_options) !== 'yandex') {
        return;
    }

    $cdn_options['service'] = 'amazon';
    $cdn_options['is_enabled'] = 0;
    $cdn_options['cname'] = '';
    $cdn_options['host'] = '';
    $cdn_options['id'] = '';
    $cdn_options['is_active'] = 0;

    Settings::instance()->updateValue('cdn', serialize($cdn_options));
}

/**
 * The `update_addon_status_post` hook handler.
 *
 * @param string                  $addon             Add-on name
 * @param string                  $status            New add-on status
 * @param bool                    $show_notification Display notification flag
 * @param bool                    $on_install        Whether add-on is being installed
 * @param bool                    $allow_unmanaged   Whether unmanaged add-ons are allowed
 * @param string                  $old_status        Previous add-on status
 * @param \Tygh\Addons\AXmlScheme $scheme            Add-on scheme
 *
 * @return void
 */
function fn_lt_yandex_cdn_static_update_addon_status_post(
    string $addon,
    string $status,
    bool $show_notification,
    bool $on_install,
    bool $allow_unmanaged,
    string $old_status,
    $scheme
): void {
    if ($addon !== 'lt_yandex_cdn_static' || $status === 'A') {
        return;
    }

    fn_lt_yandex_cdn_static_reset_service_configuration();
}

/**
 * Performs add-on uninstall cleanup.
 *
 * @return void
 */
function fn_lt_yandex_cdn_static_uninstall(): void
{
    fn_lt_yandex_cdn_static_reset_service_configuration();
}
