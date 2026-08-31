<?php

defined('BOOTSTRAP') or die('Access denied');

$schema['controllers']['talario_dashboard'] = [
    'modes' => ['manage' => ['permissions' => true]],
];
$schema['controllers']['talario_classes'] = [
    'modes' => [
        'manage' => ['permissions' => 'manage_catalog'],
        'add' => ['permissions' => 'manage_catalog'],
        'update' => ['permissions' => 'manage_catalog'],
        'save_class' => ['permissions' => 'manage_catalog'],
        'schedule' => ['permissions' => 'manage_catalog'],
        'save_schedule' => ['permissions' => 'manage_catalog'],
        'save_schedules' => ['permissions' => 'manage_catalog'],
    ],
];
$schema['controllers']['talario_locations'] = [
    'modes' => [
        'manage' => ['permissions' => true],
        'update' => ['permissions' => true],
        'update_center' => ['permissions' => true],
        'update_status' => ['permissions' => true],
    ],
];
$schema['controllers']['talario_messages'] = [
    'modes' => ['manage' => ['permissions' => true]],
];
$schema['controllers']['talario_notifications'] = [
    'modes' => ['manage' => ['permissions' => true]],
];
$schema['controllers']['talario_finance'] = [
    'modes' => ['manage' => ['permissions' => true]],
];
$schema['controllers']['talario_profile'] = [
    'modes' => ['manage' => ['permissions' => true]],
];
$schema['controllers']['talario_documents'] = [
    'modes' => ['manage' => ['permissions' => true]],
];

return $schema;
