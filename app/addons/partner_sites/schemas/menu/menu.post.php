<?php

/***************************************************************************
*                                                                         *
*                             Larionov.tech                               *
*                          https://larionov.tech                          *
*                                                                         *
***************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

$schema['central']['vendors']['items']['partner_sites.partner_site_clicks'] = [
    'attrs' => [
        'class' => 'is-addon',
    ],
    'title'    => __('partner_sites.partner_site_clicks_menu'),
    'href'     => 'partner_sites.partner_site_clicks',
    'position' => 250,
];

return $schema;
