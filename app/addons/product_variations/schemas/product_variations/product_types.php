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

use Tygh\Addons\ProductVariations\Product\Type\Type;
use Tygh\Addons\ProductVariations\ServiceProvider;

$schema = [
    Type::PRODUCT_TYPE_SIMPLE    => [
        'name' => __('product_variations.product_type.catalog_item'),
        'allow_generate_variations' => true,
    ],
    Type::PRODUCT_TYPE_VARIATION => [
        'name'          => __('product_variations.product_type.variation_of_catalog_item'),
        'tabs'          => ['detailed', 'images', 'shippings', 'qty_discounts', 'files', 'subscribers', 'variations'],
        'fields'        => [
            'product_id',
            'product_type',
            'product_code',
            'list_price',
            'prices',
            'amount',
            'tax_ids',
            'subscribers',
            'files',
            'variation_code',
            'status',
            'timestamp',
            'shippings',
            'weight',
            'shipping_freight',
            'box_height',
            'box_length',
            'box_width',
            'min_items_in_box',
            'max_items_in_box',
            'min_qty',
            'max_qty',
            'qty_step',
            'list_qty_count',
            'availability',
            'avail_since',
            'free_shipping',
            'parent_product_id',
            'variation_features',
            'shipping_params',
            'position'
        ],
        'field_aliases' => [
            'detailed_id' => 'detailed_image',
            'image_id'    => 'detailed_image',
            'price'       => 'prices',
            'taxes'       => 'tax_ids',
            'main_pair'   => 'detailed_image',
        ],
        'allow_generate_variations' => true,
    ]
];

if (ServiceProvider::isAllowOwnImages()) {
    $schema[Type::PRODUCT_TYPE_VARIATION]['fields'][] = 'detailed_image';
    $schema[Type::PRODUCT_TYPE_VARIATION]['fields'][] = 'additional_images';
    $schema[Type::PRODUCT_TYPE_VARIATION]['fields'][] = 'videos';
}

if (ServiceProvider::isAllowOwnFeatures()) {
    $schema[Type::PRODUCT_TYPE_VARIATION]['fields'][] = 'features';
    $schema[Type::PRODUCT_TYPE_VARIATION]['tabs'][] = 'features';
}

return $schema;