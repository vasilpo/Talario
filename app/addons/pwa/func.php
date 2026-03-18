<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

use Tygh\Common\OperationResult;
use Tygh\Enum\ImagePairTypes;
use Tygh\Enum\NotificationSeverity;
use Tygh\Enum\SiteArea;
use Tygh\Enum\YesNo;
use Tygh\Providers\StorefrontProvider;
use Tygh\Settings;
use Tygh\Registry;
use Tygh\Storefront\Storefront;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Add-on install handler
 *
 * @return void
 */
function fn_pwa_install()
{
    $storefront_repository = StorefrontProvider::getRepository();
    [$all_storefronts,] = $storefront_repository->find(['get_total' => false]);

    $storefront_ids = [];
    foreach ($all_storefronts as $storefront) {
        $storefront_ids[] = $storefront->storefront_id;
    }

    fn_pwa_update_default_settings($storefront_ids, fn_allowed_for('MULTIVENDOR'));
}

/**
 * Sets default PWA manifest status, name and icons for storefronts.
 *
 * @param array<int> $storefront_ids            Storefront ID to set settings for
 * @param bool       $update_default_storefront Whether to update settings for default storefront (with ID = 0)
 *
 * @return void
 */
function fn_pwa_update_default_settings(array $storefront_ids = [], $update_default_storefront = false)
{
    if (empty($storefront_ids)) {
        $storefront_repository = StorefrontProvider::getRepository();
        [$storefront_ids,] = $storefront_repository->find(['get_total' => false]);
    }

    // Update default manifest name and make sure status is N
    foreach ($storefront_ids as $storefront_id) {
        $settings_manager = Settings::instance(['storefront_id' => (int) $storefront_id]);
        $pwa_settings = Settings::instance()->getValues('pwa', 'ADDON', false, null, $storefront_id);
        if (!is_array($pwa_settings)) {
            return;
        }
        $pwa_configuration = unserialize($pwa_settings['pwa_configuration']);
        $pwa_configuration['manifest_status'] = YesNo::NO;
        $pwa_configuration['manifest_app_name'] = fn_pwa_get_fallback_manifest_app_name($storefront_id);
        $pwa_settings['pwa_configuration'] = serialize($pwa_configuration);

        foreach ($pwa_settings as $setting_name => $setting_value) {
            $settings_manager->updateValue($setting_name, (string) $setting_value, '', false, null, true, $storefront_id);
        }
    }

    // Update default manifest icons
    $current_allow_external_uploads = Registry::ifGet('runtime.allow_upload_external_paths', false);
    Registry::set('runtime.allow_upload_external_paths', true, true);

    $_REQUEST['manifest_icon_image_data'] = [
        [
            'pair_id' => '',
            'type' => 'M',
            'object_id' => 0
        ]
    ];
    $_REQUEST['file_manifest_icon_image_detailed'] = [
        fn_get_theme_path('[themes]/[theme]/media/images/addons/pwa/logo_512.png')
    ];
    $_REQUEST['type_manifest_icon_image_detailed'] = ['server'];

    if ($update_default_storefront) {
        array_push($storefront_ids, 0);
    }
    foreach ($storefront_ids as $storefront_id) {
        fn_attach_image_pairs('manifest_icon', 'manifest_icon', (int) $storefront_id);
    }

    Registry::set('runtime.allow_upload_external_paths', $current_allow_external_uploads, true);
}

/**
 * Updates add-on settings.
 *
 * @param array    $settings      Add-on settings
 * @param int|null $storefront_id Storefront ID to set settings for
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_update_pwa_settings(array $settings, $storefront_id = null)
{
    if (empty($settings['pwa_configuration']['manifest_app_name'])) {
        $settings['pwa_configuration']['manifest_app_name'] = fn_pwa_get_fallback_manifest_app_name($storefront_id);
    }

    $settings_manager = Settings::instance(['storefront_id' => $storefront_id]);

    if (isset($settings['pwa_configuration'])) {
        $settings['pwa_configuration'] = serialize($settings['pwa_configuration']);
    }

    foreach ($settings as $setting_name => $setting_value) {
        $settings_manager->updateValue($setting_name, $setting_value, '', false, null, true, $storefront_id);
    }

    // Upload default icon for PWA manifest if no image passed
    $current_allow_external_uploads = Registry::ifGet('runtime.allow_upload_external_paths', false);
    Registry::set('runtime.allow_upload_external_paths', true, true);

    if (
        isset($_REQUEST['file_manifest_icon_image_detailed'][0])
        && $_REQUEST['file_manifest_icon_image_detailed'][0] === 'manifest_icon'
        && isset($_REQUEST['type_manifest_icon_image_detailed'][0])
        && $_REQUEST['type_manifest_icon_image_detailed'][0] === 'local'
        && isset($_REQUEST['manifest_icon_image_data'][0]['pair_id'])
    ) {
        if (!empty($_REQUEST['manifest_icon_image_data'][0]['pair_id'])) {
            $pair_data = db_get_row(
                'SELECT image_id, detailed_id FROM ?:images_links WHERE pair_id = ?i',
                $_REQUEST['manifest_icon_image_data'][0]['pair_id']
            );
        }

        if (empty($pair_data)) {
            $_REQUEST['file_manifest_icon_image_detailed'] = [
                fn_get_theme_path('[themes]/[theme]/media/images/addons/pwa/logo_512.png')
            ];
            $_REQUEST['type_manifest_icon_image_detailed'] = ['server'];
        }
    }

    fn_attach_image_pairs('manifest_icon', 'manifest_icon', (int) $storefront_id);

    Registry::set('runtime.allow_upload_external_paths', $current_allow_external_uploads, true);
}

/**
 * Gets add-on settings.
 *
 * @param int|null $storefront_id Storefront to get settings for
 * @param int|null $company_id    Company ID to get settings for
 *
 * @return array<string, bool|string|array|null>
 */
function fn_get_pwa_settings($storefront_id = null, $company_id = null)
{
    if (!$storefront_id && SiteArea::isStorefront(AREA)) {
        $storefront_id = StorefrontProvider::getStorefront()->storefront_id;
    }

    if (!isset($storefront_id)) {
        return [];
    }

    $settings = [];
    $pwa_settings = Settings::instance(['company_id' => $company_id])->getValues('pwa', 'ADDON', false, null, $storefront_id);
    if (!empty($pwa_settings['pwa_configuration']) && is_string($pwa_settings['pwa_configuration'])) {
        $settings['config'] = unserialize($pwa_settings['pwa_configuration']);
    } else {
        $settings['config'] = [];
    }

    if (empty($settings['config']['manifest_status'])) {
        $settings['config']['manifest_status'] = YesNo::NO;
    }

    // Get start_url from storefront URL
    $storefront_repository = StorefrontProvider::getRepository();
    if (empty($storefront_id)) {
        $storefront = $storefront_repository->findDefault();
    } else {
        $storefront = $storefront_repository->findById($storefront_id);
    }

    if (!empty($storefront)) {
        $storefront_url_parts = explode('/', $storefront->url, 2);
        $settings['config']['manifest_start_url'] = isset($storefront_url_parts[1]) && $storefront_url_parts[1] !== ''
            ? '/' . $storefront_url_parts[1]
            : '/';
    }

    $settings['manifest_icon'] = fn_get_image_pairs($storefront_id, 'manifest_icon', ImagePairTypes::MAIN, false, true);
    if (!$settings['manifest_icon']) {
        $settings['manifest_icon'] = fn_pwa_get_fallback_manifest_icon($storefront_id);
    }

    $settings['is_valid'] = $settings['config']['manifest_status'] === YesNo::YES
        && isset($settings['manifest_icon'])
        && isset($settings['config']['manifest_app_name']);

    return $settings;
}

/**
 * Gets fallback manifest app name.
 *
 * @param int|null $storefront_id Storefront to get settings for
 * @param int      $threshold     Max symbol amount for app name
 * @param string   $suffix        Suffix if max amount exceeded
 *
 * @return string
 */
function fn_pwa_get_fallback_manifest_app_name($storefront_id = null, $threshold = 12, $suffix = '...')
{
    if ($storefront_id === null && SiteArea::isStorefront(AREA)) {
        $storefront_id = StorefrontProvider::getStorefront()->storefront_id;
    }

    if ($storefront_id === null) {
        return '';
    }

    $storefront_repository = StorefrontProvider::getRepository();
    $storefront = $storefront_repository->findById($storefront_id);

    if (empty($storefront)) {
        return '';
    }

    $fallback_name = $storefront->name;

    $name_len = mb_strlen($fallback_name, 'UTF-8');
    $suffix_len = mb_strlen($suffix, 'UTF-8');

    return $name_len > $threshold
        ? mb_substr($fallback_name, 0, $threshold - $suffix_len, 'UTF-8') . $suffix
        : $fallback_name;
}

/**
 * Gets fallback manifest icon.
 *
 * @param int|null $storefront_id Storefront to get settings for
 *
 * @return array<array<string|int|array<string|int>>>|array<array<array<string|int|array<string|int>>>>|null
 */
function fn_pwa_get_fallback_manifest_icon($storefront_id = null)
{
    $fallback_logo = fn_get_image_pairs(0, 'manifest_icon', 'M', false, true);
    if (!$fallback_logo) {
        return null;
    }

    $fallback_logo['pair_id'] = 0;
    $fallback_logo['object_id'] = $storefront_id;
    $fallback_logo['detailed']['object_id'] = $storefront_id;

    return $fallback_logo;
}

/**
 * Generates manifest content.
 *
 * @param int|null $storefront_id Storefront identifier
 *
 * @return string
 */
function fn_pwa_generate_manifest_content($storefront_id = null)
{
    if (empty($storefront_id)) {
        /** @var \Tygh\Storefront\Repository $repository */
        $repository = Tygh::$app['storefront.repository'];
        $storefront = $repository->findDefault();

        if ($storefront) {
            $storefront_id = $storefront->storefront_id;
        }
    }

    $pwa_settings = fn_get_pwa_settings($storefront_id);

    $config = $pwa_settings['config'] ?? [];
    $icon = $pwa_settings['manifest_icon']['detailed'] ?? [];

    if (!YesNo::toBool($config['manifest_status'] ?? YesNo::NO)) {
        return '';
    }

    $icons = [];
    if (!empty($icon['image_path'])) {
        $icons[] = [
            'src' => $icon['image_path'],
            'sizes' => "{$icon['image_x']}x{$icon['image_y']}",
            'type' => fn_get_mime_content_type($icon['image_path']),
            'purpose' => 'maskable'
        ];
        $icons[] = [
            'src' => $icon['image_path'],
            'sizes' => "{$icon['image_x']}x{$icon['image_y']}",
            'type' => fn_get_mime_content_type($icon['image_path'])
        ];
    }

    $manifest = [
        'name' => $config['manifest_app_name'] ?? '',
        'short_name' => $config['manifest_app_name'] ?? '',
        'start_url' => $config['manifest_start_url'] ?? '/',
        'display' => 'standalone',
    ];

    if (!empty($icons)) {
        $manifest['icons'] = $icons;
    }

    $manifest = array_filter($manifest, static function ($value) {
        return $value !== null && $value !== '';
    });

    return json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

/**
 * Generates array map for ids relative storefronts (storefronts on same domain).
 *
 * @param Tygh\Storefront\Storefront[] $storefronts List of storefront objects
 *
 * @return array
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
 */
function fn_pwa_map_relative_storefront_ids(array $storefronts)
{
    $by_host = [];

    foreach ($storefronts as $storefront) {
        [$host, $path] = array_pad(explode('/', $storefront->url, 2), 2, '');
        $by_host[$host][] = [
            'id'   => $storefront->storefront_id,
            'path' => $path
        ];
    }

    $result = [];

    foreach ($by_host as $items) {
        $parent = null;
        $children = [];

        foreach ($items as $item) {
            if ($item['path'] === '') {
                $parent = $item['id'];
            } else {
                $children[] = $item['id'];
            }
        }

        if ($parent) {
            $result[$parent] = [
                'is_parent' => true,
                'relatives'  => $children,
            ];
        }

        foreach ($children as $child) {
            $rel = $children;
            if ($parent) {
                array_unshift($rel, $parent);
            }
            $rel = array_values(array_diff($rel, [$child]));

            $result[$child] = [
                'is_parent' => false,
                'relatives'  => $rel,
            ];
        }
    }

    return $result;
}

/**
 * Hook handler: filter PWA manifest icon.
 *
 * @param string $name          Name of uploaded data
 * @param array  $filter_by_ext Allow file extensions
 * @param array  $filtered      Filtered file data
 * @param array  $udata_local   List of uploaded files
 * @param array  $udata_other   List of files object types
 * @param array  $utype         List of files sources
 *
 * @return void
 *
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint
 */
function fn_pwa_filter_uploaded_data_post($name, array $filter_by_ext, array &$filtered, array $udata_local, array $udata_other, array $utype)
{
    if (
        !isset($_REQUEST['dispatch']) || $_REQUEST['dispatch'] !== 'addons.update'
        || !isset($_REQUEST['addon']) || $_REQUEST['addon'] !== 'pwa'
        || $name !== 'manifest_icon_image_detailed' || empty($filtered)
    ) {
        return;
    }

    foreach (array_keys($utype) as $id) {
        $icon_data = $filtered[$id];
        $image_size = fn_get_image_size($icon_data['path']);

        if (!$image_size) {
            continue;
        }

        [$width, $height, , ] = $image_size;
        if ($width === 512 && $width === $height) {
            continue;
        }

        unset($filtered[$id]);
        fn_set_notification(NotificationSeverity::WARNING, __('error'), __('pwa.manifest_icon_cannot_be_uploaded'));
    }
}

/**
 * Hook handler: Adds format restriction for manifest icon.
 *
 * @param string        $object_type       Type of image
 * @param array<string> $supported_formats Support image formats for specified image type
 *
 * @return void
 */
function fn_pwa_image_helper_get_supported_formats_post($object_type, array &$supported_formats)
{
    if ($object_type !== 'manifest_icon') {
        return;
    }

    $supported_formats = ['png'];
}

/**
 * Hook handler: Updates default PWA settings for a new storefront.
 *
 * @param \Tygh\Storefront\Storefront  $storefront  Storefront
 * @param \Tygh\Common\OperationResult $save_result Result of the save process
 *
 * @return void
 */
function fn_pwa_storefront_repository_save_post(Storefront $storefront, OperationResult $save_result)
{
    if ($storefront->storefront_id) {
        // If this is a storefront UPDATE
        return;
    }

    $storefront_id = $save_result->getData();

    fn_pwa_update_default_settings([$storefront_id]);
}
