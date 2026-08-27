<?php

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

$company_id = (int) Registry::get('runtime.company_id');
if (!$company_id) {
    return $schema;
}

// The vendor cabinet fully owns its central menu. Core menu validation removes
// custom top-level sections, so disable it only for this vendor-panel request.
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
        'href'      => 'talario_messages.manage',
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
        'href'      => 'companies.balance',
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
    'content' => [
        'position'  => 900,
        'title'     => 'Поддержка',
        'href'      => 'talario_support.manage',
        'icon'      => 'life_ring',
        'items'     => [],
        'is_custom' => true,
    ],
];

return $schema;
