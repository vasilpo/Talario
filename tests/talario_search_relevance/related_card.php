<?php

define('BOOTSTRAP', true);

function fn_gather_additional_products_data(&$products, $params, $lang_code = 'ru')
{
    foreach ($products as &$product) {
        $product['main_pair'] = ['image_id' => $product['product_id']];
        $product['address'] = 'Красногорск, тестовый адрес';
    }
}

function fn_abt__ut2_add_products_features_list(&$products, $feature_id = 0, $return = false)
{
    foreach ($products as &$product) {
        $product['abt__ut2_features'] = ['age' => ['variant' => 'с 3х лет']];
    }
}

require __DIR__ . '/../../app/addons/talario_search_relevance/related.php';

$products = [
    ['product_id' => 42, 'product' => 'Лепка из глины'],
];

$prepared = fn_talario_search_relevance_prepare_related_products($products, 'ru');

if (empty($prepared[0]['main_pair'])
    || empty($prepared[0]['address'])
    || empty($prepared[0]['abt__ut2_features'])
) {
    fwrite(STDERR, "Related products must receive the standard card data\n");
    exit(1);
}

echo "TALARIO_RELATED_CARD_DATA=PASS\n";
