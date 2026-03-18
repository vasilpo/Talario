<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

defined('BOOTSTRAP') or die('Access denied');

/** @var array<string, string|array> $schema */
$schema = [
    'options_type'      => [
        'global_setting'  => 'General.global_options_type',
        'default_setting' => 'General.default_options_type',
    ],
    'exceptions_type'   => [
        'global_setting'  => 'General.global_exceptions_type',
        'default_setting' => 'General.default_exceptions_type',
    ],
    'tracking'          => [
        'global_setting'  => 'General.global_tracking',
        'default_setting' => 'General.default_tracking',
    ],
    'zero_price_action' => [
        'global_setting'  => 'Checkout.global_zero_price_action',
        'default_setting' => 'Checkout.default_zero_price_action',
    ],
    'min_qty'           => [
        'global_setting'  => 'Checkout.global_min_qty',
        'default_setting' => 'Checkout.default_min_qty',
    ],
    'max_qty'           => [
        'global_setting'  => 'Checkout.global_max_qty',
        'default_setting' => 'Checkout.default_max_qty',
    ],
    'qty_step'          => [
        'global_setting'  => 'Checkout.global_qty_step',
        'default_setting' => 'Checkout.default_qty_step',
    ],
    'list_qty_count'    => [
        'global_setting'  => 'Checkout.global_list_qty_count',
        'default_setting' => 'Checkout.default_list_qty_count',
    ],
    'details_layout' => [
        'global_setting'  => 'Appearance.global_product_details_view',
        'default_setting' => 'Appearance.default_product_details_view',
    ],
    'show_videos_before_images'   => [
        'global_setting'  => 'Appearance.global_show_videos_before_images',
        'default_setting' => 'Appearance.default_show_videos_before_images',
    ],
    'autoplay_videos'   => [
        'global_setting'  => 'Appearance.global_autoplay_videos',
        'default_setting' => 'Appearance.default_autoplay_videos',
    ]
];

return $schema;
