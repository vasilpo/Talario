<?php

defined('BOOTSTRAP') or die('Access denied');

$schema['talario_analytics'] = [
    'allow' => [
        'catalog' => true,
        'catalog_apply' => true,
    ],
    'default_allow' => false,
    'areas' => ['C'],
];

return $schema;
