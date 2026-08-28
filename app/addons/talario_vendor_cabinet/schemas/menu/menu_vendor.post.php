<?php

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

$company_id = (int) Registry::get('runtime.company_id');
if (!$company_id) {
    return $schema;
}

Registry::set('config.tweaks.validate_menu', false);

$schema['central'] = [
    'seller_tools' => [
        'position'  => 100,
        'title'     => 'Центр',
        'href'      => 'talario_locations.manage',
        'alt'       => 'talario_locations.update',
        'icon'      => 'map_marker',
        'items'     => [],
        'is_custom' => true,
    ],
    'products' => [
        'position'  => 200,
        'title'     => 'Занятия',
        'href'      => 'talario_classes.manage',
        'alt'       => 'products.add,products.update',
        'icon'      => 'tag',
        'items'     => [],
        'is_custom' => true,
    ],
    'orders' => [
        'position'  => 300,
        'title'     => 'Бронирования',
        'href'      => 'ec_table_booking_system.booked_orders',
        'icon'      => 'calendar',
        'items'     => [],
        'is_custom' => true,
    ],
    'talario_messages' => [
        'position'  => 400,
        'title'     => 'Сообщения',
        'href'      => 'vendor_communication.threads?communication_type=vendor_to_admin',
        'alt'       => 'vendor_communication.view,vendor_communication.create_thread,vendor_communication.threads',
        'icon'      => 'comments',
        'items'     => [],
        'is_custom' => true,
    ],
    'talario_notifications' => [
        'position'  => 500,
        'title'     => 'Уведомления',
        'href'      => 'talario_notifications.manage',
        'icon'      => 'bell',
        'items'     => [],
        'is_custom' => true,
    ],
    'talario_finance' => [
        'position'  => 600,
        'title'     => 'Финансы',
        'href'      => 'talario_finance.manage',
        'icon'      => 'money',
        'items'     => [],
        'is_custom' => true,
    ],
    'settings' => [
        'position'  => 700,
        'title'     => 'Профиль',
        'href'      => 'talario_profile.manage',
        'icon'      => 'user',
        'items'     => [],
        'is_custom' => true,
    ],
    'talario_documents' => [
        'position'  => 800,
        'title'     => 'Документы',
        'href'      => 'talario_documents.manage',
        'icon'      => 'file_text',
        'items'     => [],
        'is_custom' => true,
    ],
];

return $schema;
