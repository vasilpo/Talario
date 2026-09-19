<?php

defined('BOOTSTRAP') or die('Access denied');

if (isset($schema['vkontakte'])) {
    $schema['vkontakte']['callback'] = fn_url('/auth/vkontakte');
    $schema['vkontakte']['params']['vkontakte_callback'] = [
        'type' => 'template',
        'template' => 'addons/hybrid_auth/components/callback_url.tpl',
        'callback_url' => '/auth/vkontakte',
    ];
    $schema['vkontakte']['adapter'] = 'Tygh\\Addons\\TalarioVkAuth\\Providers\\Vkontakte';
}

return $schema;
