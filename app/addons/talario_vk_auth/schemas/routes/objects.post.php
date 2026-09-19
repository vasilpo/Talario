<?php

defined('BOOTSTRAP') or die('Access denied');

$schema['/auth/vkontakte'] = [
    'dispatch' => 'auth.process',
];

return $schema;
