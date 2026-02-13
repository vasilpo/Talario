<?php

defined('BOOTSTRAP') or die('Access denied');

/** @var array $schema */
$schema['exikane_partner_banner'] = [
    'show_on_locations' => ['product_tabs'],
    'templates'         => 'addons/exikane_changes/blocks/product_tabs/partner_banner.tpl',
    'cache'             => false,
];
$schema['exikane_guest_banner'] = [
    'templates' => 'addons/exikane_changes/blocks/guest_banner.tpl',
    'cache'     => false,
];

return $schema;
