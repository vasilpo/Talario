<?php

defined('BOOTSTRAP') or die('Access denied');

$schema['talario_analytics'] = [
    'allow' => [
        'catalog' => true,
        'crm' => true,
    ],
    'default_allow' => false,
    'areas' => ['C'],
];

return $schema;
