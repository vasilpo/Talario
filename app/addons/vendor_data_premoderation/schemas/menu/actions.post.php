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

use Tygh\Enum\Addons\VendorDataPremoderation\ProductStatuses;
use Tygh\Registry;
use Tygh\Enum\UserTypes;
use Tygh\Enum\VendorStatuses;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
$addon_setting = Registry::get('addons.vendor_data_premoderation');

/**
 * @var array $request
 */
$request = $_REQUEST;

if (
    $addon_setting['products_prior_approval'] === 'none'
    && $addon_setting['products_updates_approval'] === 'none'
    && $addon_setting['vendor_profile_updates_approval'] === 'none'
) {
    return $schema;
}

// Products
if ($addon_setting['products_prior_approval'] !== 'none' || $addon_setting['products_updates_approval'] !== 'none') {
    // Show Products on moderation and Disapproved products buttons only on global products page
    if (
        empty($request['status'])
        || (
            $request['status'] !== ProductStatuses::REQUIRES_APPROVAL
            && $request['status'] !== ProductStatuses::DISAPPROVED
        )
    ) {
        $schema['products.manage']['products.manage.' . ProductStatuses::REQUIRES_APPROVAL] = [
            'href'     => 'products.manage?status=' . ProductStatuses::REQUIRES_APPROVAL,
            'text'     => __('vendor_data_premoderation.actions.require_approval'),
            'position' => 20
        ];

        $schema['products.manage']['products.manage.' . ProductStatuses::DISAPPROVED] = [
            'href'     => 'products.manage?status=' . ProductStatuses::DISAPPROVED,
            'text'     => __('vendor_data_premoderation.actions.require_vendor_action'),
            'position' => 30
        ];
    } elseif (
        !empty($request['dispatch'])
        && $request['dispatch'] === 'products.manage'
        && !empty($request['status'])
        && (
            $request['status'] === ProductStatuses::REQUIRES_APPROVAL
            || $request['status'] === ProductStatuses::DISAPPROVED
        )
    ) {
        // Hide products buttons on Products on moderation and Disapproved products pages
        unset($schema['products.manage']);
    }
}

// Vendors
if ($addon_setting['vendor_profile_updates_approval'] !== 'none' || $addon_setting['vendors_prior_approval'] !== 'none') {
    // Show Vendors on moderation button only on global products page
    if (
        Tygh::$app['session']['auth']['user_type'] === UserTypes::ADMIN
        && empty($request['status'])
        || (
            !empty($request['status'])
            && !empty($request['status'][0])
            && !empty($request['status'][1])
            && $request['status'][0] !== VendorStatuses::PENDING
            && $request['status'][1] !== VendorStatuses::NEW_ACCOUNT
        )
    ) {
        $schema['companies.manage']['vendor_data_premoderation.vendors_moderation'] = [
            'href'     => 'companies.manage?status[]=' . VendorStatuses::PENDING . '&status[]=' . VendorStatuses::NEW_ACCOUNT,
            'text'     => __('vendor_data_premoderation.actions.vendors_on_moderation'),
            'position' => 20
        ];
    } elseif (
        !empty($request['dispatch'])
        && $request['dispatch'] === 'companies.manage'
        && !empty($request['status'])
        && (
            $request['status'][0] === VendorStatuses::PENDING
            || $request['status'][1] === VendorStatuses::NEW_ACCOUNT
        )
    ) {
        // Hide products buttons on Vendors on moderation page
        unset($schema['companies.manage']);
    }
}

return $schema;
