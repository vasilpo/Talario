<?php

defined('BOOTSTRAP') or die('Access denied');

$schema['talario_analytics'] = [
    'allow' => [
        'catalog' => true,
        'catalog_variant_bootstrap' => true,
        'dispatcher_status' => true,
        'penaty_bootstrap' => true,
        'penaty_apply' => true,
        'crm' => true,
    ],
    'default_allow' => false,
    'areas' => ['C'],
];

return $schema;
