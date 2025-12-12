<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

use Tygh\Enum\Addons\Seo\ObjectTypes;
use Tygh\Enum\StorefrontStatuses;
use Tygh\Http;
use Tygh\Registry;

/**
 * Check if mod_rewrite is active and clean up templates cache
 */
function fn_settings_actions_addons_seo(&$new_value, $old_value)
{
    if ($new_value === 'A') {
        /** @var \Tygh\Storefront\Repository $storefront_repository */
        $storefront_repository = Tygh::$app['storefront.repository'];
        $default_storefront = $storefront_repository->findDefault();
        $validation_url = fn_get_storefront_protocol() . '://' . $default_storefront->url . '/check_url_rewrite.html';
        if ($default_storefront->status === StorefrontStatuses::CLOSED) {
            $validation_url .= '?store_access_key=' . $default_storefront->access_key;
        }

        // User agent is set to crawler one to avoid regional redirect
        Http::get($validation_url, [], ['headers' => ['User-Agent: curl']]);
        $status = Http::getStatus();

        if ($status !== Http::STATUS_OK) {
            $new_value = 'D';
            fn_set_notification('W', __('warning'), __('warning_seo_urls_disabled'));
        }
    }

    fn_clear_cache();

    return true;
}

function fn_settings_actions_addons_seo_seo_product_type($new_value, $old_value)
{
    if (!empty($old_value) && $new_value != $old_value) {

        $redirect_only = false;
        $options = array('product_file', 'product_file_nohtml');
        if (in_array($new_value, $options) && in_array($old_value, $options)) {
            $redirect_only = true;
        }

        $options = array('product_category', 'product_category_nohtml');
        if (in_array($new_value, $options) && in_array($old_value, $options)) {
            $redirect_only = true;
        }

        fn_seo_settings_update('p', 'seo_product_type', $new_value, $redirect_only);
    }
}

function fn_settings_actions_addons_seo_seo_category_type($new_value, $old_value)
{
    if (!empty($old_value) && $new_value != $old_value) {

        $redirect_only = false;
        $options = array('category', 'file');
        if (in_array($new_value, $options) && in_array($old_value, $options)) {
            $redirect_only = true;
        }

        fn_seo_settings_update('c', 'seo_category_type', $new_value, $redirect_only);
    }
}

function fn_settings_actions_addons_seo_seo_page_type($new_value, $old_value)
{
    if (!empty($old_value) && $new_value != $old_value) {

        $redirect_only = false;
        $options = array('page', 'file');
        if (in_array($new_value, $options) && in_array($old_value, $options)) {
            $redirect_only = true;
        }

        fn_seo_settings_update('a', 'seo_page_type', $new_value, $redirect_only);
    }
}

/**
 * Creates redirects for product features, companies and custom SEO objects.
 *
 * @param string|null $new_value New setting value
 * @param string|null $old_value Old setting value
 *
 * @return void
 */
function fn_settings_actions_addons_seo_seo_other_type(?string $new_value, ?string $old_value)
{
    if (!empty($old_value) && $new_value !== $old_value) {
        $redirect_only = false;
        $options = ['directory', 'file'];

        if (in_array($new_value, $options) && in_array($old_value, $options)) {
            $redirect_only = true;
        }

        fn_seo_settings_update(ObjectTypes::EXTENDED, 'seo_other_type', $new_value, $redirect_only);
        fn_seo_settings_update(ObjectTypes::STATIC_DISPATCH, 'seo_other_type', $new_value, $redirect_only);

        if (fn_allowed_for('MULTIVENDOR')) {
            fn_seo_settings_update(ObjectTypes::VENDOR, 'seo_other_type', $new_value, $redirect_only);
        }
    }
}

function fn_seo_settings_update($type, $option, $new_value, $redirect_only)
{
    $old_value = Registry::get('addons.seo.' . $option);
    $schema = fn_get_schema('seo', 'objects');
    $object_htm_options = $schema[$type]['html_options'];
    $html_option_changed = (in_array($old_value, $object_htm_options) || in_array($new_value, $object_htm_options));

    fn_iterate_through_seo_names(
        static function ($seo_name) use ($option, $old_value, $new_value, $redirect_only, $html_option_changed) {
            // We shouldn't consider null value
            if (false === fn_check_seo_object_exists(
                $seo_name['object_id'], $seo_name['type'], $seo_name['company_id']
            )) {
                fn_delete_seo_name($seo_name['object_id'], $seo_name['type'], '', $seo_name['company_id']);

                return;
            }

            Registry::set('addons.seo.' . $option, $old_value);
            $is_root_parent = (!empty($seo_name['path']) || !isset($seo_name['path']));

            if ($is_root_parent || $html_option_changed) {
                $redirect_data = [
                    'type' => $seo_name['type'],
                    'object_id' => $seo_name['object_id'],
                    'lang_code' => $seo_name['lang_code']
                ];

                if ($seo_name['type'] === ObjectTypes::STATIC_DISPATCH) {
                    /** @psalm-suppress UndefinedConstant */
                    $post_fix = $old_value === 'file' ? SEO_FILENAME_EXTENSION : '';
                    $redirect_data['post_fix'] = $post_fix;
                    $redirect_data['dest'] = $seo_name['dispatch'];
                }

                $url = fn_generate_seo_url_from_schema($redirect_data, false, [], $seo_name['company_id']);

                $redirect_data['src'] = $url;
                $redirect_data['company_id'] = $seo_name['company_id'];

                if ($seo_name['type'] === ObjectTypes::STATIC_DISPATCH) {
                    /** @psalm-suppress UndefinedConstant */
                    $post_fix = empty($post_fix) ? SEO_FILENAME_EXTENSION : '';
                    $redirect_data['dest'] = '/' . $seo_name['name'] . $post_fix;
                    unset($redirect_data['post_fix']);
                }

                fn_seo_update_redirect($redirect_data, 0, false);
            }

            if (!$redirect_only) {
                Registry::set('addons.seo.' . $option, $new_value);
                fn_create_seo_name(
                    $seo_name['object_id'],
                    $seo_name['type'],
                    $seo_name['name'],
                    0,
                    '',
                    $seo_name['company_id'],
                    $seo_name['lang_code'],
                    true
                );
            }
        },
        db_quote("type = ?s ?p", $type, fn_get_seo_company_condition('?:seo_names.company_id', $type))
    );
}
