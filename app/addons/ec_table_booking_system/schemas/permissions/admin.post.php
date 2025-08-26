<?php
$schema['ec_table_booking_system'] = array(
    'modes' => array (
        'manage' => array (
            'permissions' => 'view_ec_table_booking_system'
        ),
        'booked_orders' => array (
            'permissions' => 'view_ec_table_booking_system'
        ),
        'update' => array (
            'permissions' => 'manage_ec_table_booking_system'
        ),
        'm_delete' => array (
            'permissions' => 'manage_ec_table_booking_system'
        ),
        'delete' => array (
            'permissions' => 'manage_ec_table_booking_system'
        )
    ),
    'permissions' => array (
        'GET' => 'view_ec_table_booking_system',
        'POST' => 'manage_ec_table_booking_system'
    )
);
$schema['tools']['modes']['update_status']['param_permissions']['table']['ec_table_booking_system'] = 'manage_ec_table_booking_system';
$schema['tools']['modes']['update_status']['param_permissions']['table']['ec_table_booking_system_booking_info'] = 'manage_ec_table_booking_system';
return $schema;