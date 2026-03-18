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


use Tygh\Addons\ProductVariations\Product\Sync\Table\MainTable;
use Tygh\Addons\ProductVariations\Product\Sync\Table\OneToManyViaPrimaryKeyTable;

require_once __DIR__ . '/functions.php';

$schema = [
    'products'                      => MainTable::create('products', 'product_id', ['product_type', 'parent_product_id', 'master_product_id', 'master_product_status', 'timestamp', 'updated_timestamp', 'company_id', 'amount']),
    'product_descriptions'          => OneToManyViaPrimaryKeyTable::create('product_descriptions', ['product_id', 'lang_code'], 'product_id'),
    'product_global_option_links'   => OneToManyViaPrimaryKeyTable::create('product_global_option_links', ['product_id', 'option_id'], 'product_id'),
    'products_categories'           => OneToManyViaPrimaryKeyTable::create('products_categories', ['product_id', 'category_id'], 'product_id', [], ['after_sync_callback' => 'fn_master_products_sync_update_products_count']),
    'images_links'                  => OneToManyViaPrimaryKeyTable::create('images_links', ['object_id', 'image_id', 'detailed_id'], 'object_id', ['pair_id'], ['conditions' => ['object_type' => 'product']]),
    'videos_links'                  => OneToManyViaPrimaryKeyTable::create('videos_links', ['video_id', 'object_id'], 'object_id', ['pair_id'], ['conditions' => ['object_type' => 'product']]),
    'product_features_values'       => OneToManyViaPrimaryKeyTable::create('product_features_values', ['product_id', 'feature_id', 'variant_id', 'lang_code'], 'product_id'),
    'product_prices'                => OneToManyViaPrimaryKeyTable::create('product_prices', ['product_id', 'usergroup_id', 'lower_limit'], 'product_id'),
    'product_popularity'            => OneToManyViaPrimaryKeyTable::create('product_popularity', ['product_id'], 'product_id'),
];

return $schema;