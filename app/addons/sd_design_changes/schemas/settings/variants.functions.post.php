<?php
/****************************************************************************
 *                                                                          *
 *   © Simtech Development Ltd.                                             *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 ***************************************************************************/

use Tygh\Enum\ProfileFieldLocations;
use Tygh\Enum\ProfileFieldSections;
use Tygh\Enum\ProfileTypes;

/**
 * @return array
 */
function fn_settings_variants_addons_sd_design_changes_metro_profile_field_id(): array
{
    $variants = [
        0 => __('none')
    ];

    $profile_fields = fn_get_profile_fields(ProfileFieldLocations::ADMIN_FIELDS, [], CART_LANGUAGE, [
        'get_custom'           => true,
        'profile_type' => ProfileTypes::CODE_SELLER,
        'skip_email_field' => false,
    ]);

    if (!empty($profile_fields[ProfileFieldSections::CONTACT_INFORMATION])) {
        foreach ($profile_fields[ProfileFieldSections::CONTACT_INFORMATION] as $field_id => $field) {
            $variants[$field_id] = $field['description'] ?? "#{$field_id}";
        }
    }

    return $variants;
}

/**
 * @return array
 */
function fn_settings_variants_addons_sd_design_changes_apply_vendor_page_id(): array
{
    $variants = [
        0 => __('none')
    ];

    $params = [
        'simple' => true,
        'get_tree' => 'plain',
        'status' => ['A', 'H'],
        'page_type' => 'F',
    ];

    list($pages) = fn_get_pages($params, 0, CART_LANGUAGE);

    if (!empty($pages)) {
        foreach ($pages as $page) {
            $page_title = !empty($page['page']) ? $page['page'] : "Page #{$page['page_id']}";

            $status_info = '';
            if ($page['status'] !== 'A') {
                $status_info = " [{$page['status']}]";
            }

            $variants[$page['page_id']] = "{$page_title}{$status_info} (#{$page['page_id']})";
        }
    }

    return $variants;
}