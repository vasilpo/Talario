<?php

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

$company_id = (int) Registry::get('runtime.company_id');
if (!$company_id) {
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
        'title'     => __('talario_vendor_cabinet.center'),
        'href'      => 'talario_locations.manage',
        'alt'       => 'talario_locations.update',
        'icon'      => 'map_marker',
        'items'     => [],
        'is_custom' => true,
    ],
    'talario_messages' => [
        'position'  => 400,
        'title'     => __('talario_vendor_cabinet.messages'),
        'href'      => 'talario_messages.manage',
        'icon'      => 'comments',
        'items'     => [],
        'is_custom' => true,
    ],
    'talario_notifications' => [
        'position'  => 500,
        'title'     => __('talario_vendor_cabinet.notifications'),
        'href'      => 'talario_notifications.manage',
        'icon'      => 'bell',
        'items'     => [],
        'is_custom' => true,
    ],
    'talario_finance' => [
        'position'  => 600,
        'title'     => __('talario_vendor_cabinet.finance'),
        'href'      => 'companies.balance',
        'icon'      => 'money',
        'items'     => [],
        'is_custom' => true,
    ],
    'settings' => [
        'position'  => 700,
        'title'     => __('talario_vendor_cabinet.profile'),
        'href'      => 'companies.update?company_id=' . $company_id,
        'icon'      => 'user',
        'items'     => [],
        'is_custom' => true,
    ],
    'talario_documents' => [
        'position'  => 800,
        'title'     => __('talario_vendor_cabinet.documents'),
        'href'      => 'talario_documents.manage',
        'icon'      => 'file_text',
        'items'     => [],
        'is_custom' => true,
    ],
    'content' => [
        'position'  => 900,
        'title'     => __('talario_vendor_cabinet.support'),
        'href'      => 'talario_support.manage',
        'icon'      => 'life_ring',
        'items'     => [],
        'is_custom' => true,
    ],
];

return $schema;
