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

use Tygh\Enum\YesNo;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

$general_settings = Registry::get('settings.General');
$checkout_settings = Registry::get('settings.Checkout');
$appearance_settings = Registry::get('settings.Appearance');

$schema = [
    'detailed'      => [
        'is_optional' => false,
        'title'       => 'general',
        'position'    => 100,
        'sections'    => [
            'information'       => [
                'is_optional' => false,
                'title'       => 'information',
                'position'    => 100,
                'fields'      => [
                    'product'                   => ['is_optional' => false, 'title' => 'name', 'position' => 100],
                    'company_id'                => ['is_optional' => false, 'title' => 'vendor', 'position' => 200],
                    'category_ids'              => ['is_optional' => false, 'title' => 'categories', 'position' => 300],
                    'price'                     => ['is_optional' => false, 'title' => 'price', 'position' => 400],
                    'full_description'          => ['is_optional' => false, 'title' => 'full_description', 'position' => 500],
                    'status'                    => ['is_optional' => true, 'title' => 'status', 'position' => 600],
                    'images'                    => ['is_optional' => false, 'title' => 'images', 'position' => 700],
                    'videos'                    => ['is_optional' => false, 'title' => 'videos', 'position' => 800],
                    'show_videos_before_images' => [
                        'is_optional' => true,
                        'title' => 'show_videos_before_images',
                        'position' => 900,
                        $appearance_settings['global_show_videos_before_images'] !== null,
                        'section' => 'Appearance'
                    ],
                    'autoplay_videos' => [
                        'is_optional' => true,
                        'title' => 'autoplay_videos',
                        'position' => 950,
                        $appearance_settings['global_autoplay_videos'] !== null,
                        'section' => 'Appearance'
                    ],
                ],
            ],
            'options_settings'  => [
                'is_optional' => true,
                'title'       => 'options_settings',
                'position'    => 200,
                'fields'      => [
                    'options_type'    => [
                        'is_optional' => true,
                        'title' => 'options_type',
                        'position' => 100,
                        'is_global' => $general_settings['global_options_type'] !== null,
                        'section' => 'General'
                    ],
                    'exceptions_type' => [
                        'is_optional' => true,
                        'title' => 'exceptions_type',
                        'position' => 200,
                        'is_global' => $general_settings['global_exceptions_type'] !== null,
                        'section' => 'General'
                    ],
                ],
            ],
            'pricing_inventory' => [
                'is_optional' => false,
                'title'       => 'pricing_inventory',
                'position'    => 300,
                'fields'      => [
                    'product_code'      => ['is_optional' => false, 'title' => 'sku', 'position' => 100],
                    'list_price'        => ['is_optional' => true, 'title' => 'list_price', 'position' => 200],
                    'amount'            => ['is_optional' => false, 'title' => 'in_stock', 'position' => 300],
                    'zero_price_action' => [
                        'is_optional' => true,
                        'title' => 'zero_price_action',
                        'position' => 400,
                        'is_global' => $checkout_settings['global_zero_price_action'] !== null,
                        'section' => 'Checkout'
                    ],
                    'tracking'          => [
                        'is_optional' => true,
                        'title' => 'track_inventory',
                        'position' => 500,
                        'is_global' => $general_settings['global_tracking'] !== null,
                        'section' => 'General'
                    ],
                    'min_qty'           => [
                        'is_optional' => true,
                        'title' => 'min_order_qty',
                        'position' => 600,
                        'is_global' => $checkout_settings['global_min_qty'] !== null,
                        'section' => 'Checkout'
                    ],
                    'max_qty'           => [
                        'is_optional' => true,
                        'title' => 'max_order_qty',
                        'position' => 700,
                        'is_global' => $checkout_settings['global_max_qty'] !== null,
                        'section' => 'Checkout'
                    ],
                    'qty_step'          => [
                        'is_optional' => true,
                        'title' => 'quantity_step',
                        'position' => 800,
                        'is_global' => $checkout_settings['global_qty_step'] !== null,
                        'section' => 'Checkout'
                    ],
                    'list_qty_count'    => [
                        'is_optional' => true,
                        'title' => 'list_quantity_count',
                        'position' => 900,
                        'is_global' => $checkout_settings['global_list_qty_count'] !== null,
                        'section' => 'Checkout'
                    ],
                    'tax_ids'           => ['is_optional' => false, 'title' => 'taxes', 'position' => 1000],
                ],
            ],
            'availability'      => [
                'is_optional' => true,
                'title'       => 'availability',
                'position'    => 400,
                'fields'      => [
                    'usergroup_ids'        => ['is_optional' => true, 'title' => 'usergroups', 'position' => 100],
                    'timestamp'            => ['is_optional' => true, 'title' => 'creation_date', 'position' => 200],
                    'avail_since'          => ['is_optional' => true, 'title' => 'available_since', 'position' => 300],
                    'out_of_stock_actions' => [
                        'is_optional' => true,
                        'title'       => 'out_of_stock_actions',
                        'position'    => 400,
                    ],
                ],
            ],
            'extra'             => [
                'is_optional' => true,
                'title'       => 'extra',
                'position'    => 500,
                'fields'      => [
                    'details_layout'     => [
                        'is_optional' => true,
                        'title'       => 'product_details_view',
                        'position'    => 100,
                    ],
                    'is_edp'             => ['is_optional' => false, 'title' => 'downloadable', 'position' => 200],
                    'edp_shipping'       => [
                        'is_optional' => true,
                        'title'       => 'edp_enable_shipping',
                        'position'    => 300,
                    ],
                    'unlimited_download' => [
                        'is_optional' => true,
                        'title'       => 'time_unlimited_download',
                        'position'    => 400,
                    ],
                    'short_description'  => ['is_optional' => true, 'title' => 'short_description', 'position' => 500],
                    'popularity'         => ['is_optional' => true, 'title' => 'popularity', 'position' => 600],
                    'search_words'       => ['is_optional' => true, 'title' => 'search_words', 'position' => 700],
                    'promo_text'         => ['is_optional' => true, 'title' => 'promo_text', 'position' => 800],
                ],
            ],
        ],
    ],
    'shippings'     => [
        'position'    => 200,
        'title'       => 'shipping_properties',
        'is_optional' => true,
    ],
    'options'       => [
        'position'    => 300,
        'title'       => 'options',
        'is_optional' => true,
    ],
    'features'      => [
        'position'    => 400,
        'title'       => 'features',
        'is_optional' => true,
    ],
    'seo'           => [
        'position'    => 500,
        'title'       => 'seo',
        'is_optional' => true,
    ],
    'qty_discounts' => [
        'position'    => 600,
        'title'       => 'qty_discounts',
        'is_optional' => true,
    ],
    'files'         => [
        'position'    => 700,
        'title'       => 'sell_files',
        'is_optional' => false,
    ],
    'subscribers'   => [
        'position'    => 600,
        'title'       => 'subscribers',
        'is_optional' => true,
    ],
    'addons'        => [
        'position'    => 700,
        'title'       => 'addons',
        'is_optional' => true,
    ],
];

if (Registry::get('settings.General.enable_edp') !== YesNo::YES) {
    unset(
        $schema['files'],
        $schema['detailed']['sections']['extra']['fields']['is_edp'],
        $schema['detailed']['sections']['extra']['fields']['edp_shipping'],
        $schema['detailed']['sections']['extra']['fields']['unlimited_download']
    );
}

return $schema;
