<?php

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if (!Registry::get('runtime.company_id')) {
    return $schema;
}

$schema['central'] = [
    'products' => [
        'position'  => 100,
        'title'     => __('talario_vendor_cabinet.classes'),
        'href'      => 'talario_classes.manage',
        'alt'       => 'products.add,products.update',
        'icon'      => 'tag',
        'items'     => [],
        'is_custom' => true,
    ],
    'orders' => [
        'position'  => 200,
        'title'     => __('talario_vendor_cabinet.bookings'),
        'href'      => 'ec_table_booking_system.booked_orders',
        'icon'      => 'calendar',
        'items'     => [],
        'is_custom' => true,
    ],
    'seller_tools' => [
        'position'  => 300,
        'title'     => __('talario_vendor_cabinet.centers'),
        'href'      => 'talario_locations.manage',
        'alt'       => 'talario_locations.update',
        'icon'      => 'map_marker',
        'items'     => [],
        'is_custom' => true,
    ],
    'settings' => [
        'position'  => 400,
        'title'     => __('talario_vendor_cabinet.profile'),
        'href'      => 'profiles.update',
        'icon'      => 'user',
        'items'     => [],
        'is_custom' => true,
    ],
    'content' => [
        'position'  => 500,
        'title'     => __('talario_vendor_cabinet.support'),
        'href'      => 'talario_support.manage',
        'icon'      => 'comments',
        'items'     => [],
        'is_custom' => true,
    ],
];

return $schema;
