<?php

defined('BOOTSTRAP') or die('Access denied');

$schema['talario_analytics'] = [
    'allow' => [
        'catalog' => true,
        'catalog_variant_bootstrap' => true,
        'dispatcher_status' => true,
        'dispatcher_install' => true,
        'crm' => true,
    ],
    'default_allow' => false,
    'areas' => ['C'],
];

return $schema;
