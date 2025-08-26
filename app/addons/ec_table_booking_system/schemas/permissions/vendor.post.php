<?php
$schema['controllers']['ec_table_booking_system'] = array(
    'modes' => array(
        'manage' => array(
            'permissions' => true
        ),
        'booked_orders' => array (
            'permissions' => true
        ),
        'update' => array(
            'permissions' => true
        ),
        'm_delete' => array(
            'permissions' => true
        ),
        'delete' => array(
            'permissions' => true
        )
    ),
    'permissions' => true
);
$schema['controllers']['tools']['modes']['update_status']['param_permissions']['table']['ec_table_booking_system'] = true;
$schema['controllers']['tools']['modes']['update_status']['param_permissions']['table']['ec_table_booking_system_booking_info'] = true;
return $schema;