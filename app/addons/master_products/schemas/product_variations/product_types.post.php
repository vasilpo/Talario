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

/**
 * @var array $schema
 */

$schema[PRODUCT_TYPE_VENDOR_PRODUCT_OFFER] = [
    'name'          => __('master_products.product_type.offer'),
    'tabs'          => [
        'detailed',
        'shippings',
        'qty_discounts',
        'variations',
        // added by the Warehouses add-on
        'warehouses_quantity',
        'buy_together',
        'features',
    ],
    'fields'        => [
        'product_id',
        'prices',
        'amount',
        'status',
        'timestamp',
        'updated_timestamp',
        'lang_code',
        'shippings',
        'weight',
        'shipping_freight',
        'shipping_params',
        'min_qty',
        'max_qty',
        'qty_step',
        'list_qty_count',
        'free_shipping',
        'product_type',
        'parent_product_id',
        'company_id',
        'master_product_id',
        'master_product_status',
        'features',
    ],
    'field_aliases' => [
        'detailed_id' => 'detailed_image',
        'image_id'    => 'detailed_image',
        'price'       => 'prices',
        'taxes'       => 'tax_ids',
        'main_pair'   => 'detailed_image',
    ],
    'search_criteria_callback' => function ($table) {
        return sprintf('%s.master_product_id > 0 AND %s.parent_product_id = 0', $table, $table);
    },
    'allow_generate_variations' => false,
];

$schema[PRODUCT_TYPE_PRODUCT_OFFER_VARIATION] = $schema[PRODUCT_TYPE_VENDOR_PRODUCT_OFFER];
$schema[PRODUCT_TYPE_PRODUCT_OFFER_VARIATION]['name'] = __('master_products.product_type.offer_variation');
$schema[PRODUCT_TYPE_PRODUCT_OFFER_VARIATION]['tabs'] = [
    'detailed',
    'shippings',
    'qty_discounts',
    'variations',
    // added by the Warehouses add-on
    'warehouses_quantity',
];
$schema[PRODUCT_TYPE_PRODUCT_OFFER_VARIATION]['search_criteria_callback'] = function ($table) {
    return sprintf('%s.master_product_id > 0 AND %s.parent_product_id > 0', $table, $table);
};


return $schema;
