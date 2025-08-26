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

use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\YesNo;
use Tygh\Registry;
use Tygh\Enum\Addons\VendorDataPremoderation\ProductStatuses;
use Tygh\Enum\UserTypes;
use Tygh\Tygh;

defined('BOOTSTRAP') or die('Access denied');

$auth = Tygh::$app['session']['auth'];

/**
 * @var array $request
 */
$request = $_REQUEST;

if (
    !empty($auth)
    && $auth['user_type'] === UserTypes::ADMIN
    && !empty($request['dispatch'])
    && $request['dispatch'] === 'products.manage'
    && empty($request['status'])
    || !empty($request['status'])
        && (
            Registry::get('addons.vendor_data_premoderation.status') === ObjectStatuses::ACTIVE
            && $request['status'] !== ProductStatuses::REQUIRES_APPROVAL
            && $request['status'] !== ProductStatuses::DISAPPROVED
        )
) {
    /** @var array $schema */
    $schema['products.manage']['products.master_products'] = [
        'href'     => 'products.master_products',
        'text'     => __('master_products.actions.master_products'),
        'position' => 50
    ];
}

if (
    !empty($auth)
    && $auth['user_type'] === UserTypes::VENDOR
    && YesNo::toBool(Registry::get('addons.master_products.allow_vendors_to_create_products'))
    && !empty($request['dispatch'])
    && $request['dispatch'] === 'products.manage'
    && empty($request['status'])
    || !empty($request['status'])
        && (
            Registry::get('addons.vendor_data_premoderation.status') === ObjectStatuses::ACTIVE
            && $request['status'] !== ProductStatuses::REQUIRES_APPROVAL
            && $request['status'] !== ProductStatuses::DISAPPROVED
        )
) {
    /** @var array $schema */
    $schema['products.manage']['products.add'] = [
        'href'     => 'products.add',
        'text'     => __('master_products.actions.products_add'),
        'position' => 20
    ];
}

if (!Registry::get('runtime.company_id')) {
    /** @var array $schema */
    $schema['products.master_products']['bulk_product_addition'] = [
        'href'     => 'products.m_add',
        'text'     => __('actions.bulk_product_addition'),
        'position' => 400
    ];
}

/** @var array $schema */
$schema['products.master_products']['export_found_products'] = [
    'href'     => 'products.export_found.master',
    'text'     => __('actions.export_found'),
    'position' => 400
];

return $schema;
