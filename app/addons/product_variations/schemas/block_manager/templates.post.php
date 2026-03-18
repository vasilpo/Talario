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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

$schema['addons/product_variations/blocks/products/variations_list.tpl'] = [
    'settings'      => [
        'product_variations.hide_add_to_wishlist_button' => [
            'type'          => 'checkbox',
            'default_value' => 'N'
        ],
        'product_variations.show_variation_thumbnails'   => [
            'type'          => 'checkbox',
            'default_value' => 'Y'
        ],
        'product_variations.show_product_code'           => [
            'type'          => 'checkbox',
            'default_value' => 'Y'
        ]
    ],
    'bulk_modifier' => [
        'fn_gather_additional_products_data' => [
            'products' => '#this',
            'params'   => [
                'get_icon'           => true,
                'get_detailed'       => true,
                'get_options'        => true,
                'get_variation_info' => true,
            ],
        ],
    ],
];

return $schema;