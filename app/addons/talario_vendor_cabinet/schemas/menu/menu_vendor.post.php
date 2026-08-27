<?php

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if (!Registry::get('runtime.company_id')) {
    return $schema;
}

$schema['central'] = [
    'talario_home' => ['href' => 'talario_dashboard.manage', 'position' => 100, 'title' => __('talario_vendor_cabinet.home'), 'icon' => 'home'],
    'talario_classes' => ['href' => 'talario_classes.manage', 'alt' => 'products.add,products.update', 'position' => 200, 'title' => __('talario_vendor_cabinet.classes'), 'icon' => 'tag'],
    'talario_bookings' => ['href' => 'ec_table_booking_system.booked_orders', 'position' => 300, 'title' => __('talario_vendor_cabinet.bookings'), 'icon' => 'calendar'],
    'talario_centers' => ['href' => 'talario_locations.manage', 'alt' => 'talario_locations.update', 'position' => 400, 'title' => __('talario_vendor_cabinet.centers'), 'icon' => 'map-marker'],
    'talario_profile' => ['href' => 'profiles.update', 'position' => 500, 'title' => __('talario_vendor_cabinet.profile'), 'icon' => 'user'],
    'talario_support' => ['href' => 'vendor_communication.threads', 'position' => 600, 'title' => __('talario_vendor_cabinet.support'), 'icon' => 'comments'],
];

return $schema;
