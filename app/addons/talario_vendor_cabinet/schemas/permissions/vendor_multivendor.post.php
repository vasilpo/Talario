<?php

defined('BOOTSTRAP') or die('Access denied');

$schema['controllers']['talario_dashboard'] = [
    'modes' => ['manage' => ['permissions' => true]],
];
$schema['controllers']['talario_classes'] = [
    'modes' => ['manage' => ['permissions' => 'manage_catalog']],
];
$schema['controllers']['talario_locations'] = [
    'modes' => [
        'manage' => ['permissions' => true],
        'update' => ['permissions' => true],
        'update_status' => ['permissions' => true],
    ],
];

return $schema;
