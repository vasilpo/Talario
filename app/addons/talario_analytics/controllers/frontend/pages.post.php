<?php

defined('BOOTSTRAP') or die('Access denied');

if (AREA !== 'C' || $mode !== 'view') {
    return;
}

$path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
$path = '/' . ltrim(rtrim(rawurldecode($path), '/'), '/');

$pilots = [
    '/sport/tablica-razrjadov-po-plavaniju-normativy-dlja-vseh-vozrastov' => [
        'category' => 'Плавание',
        'fallback_category' => 'Спорт',
        'title' => 'Ищете плавание для ребёнка?',
        'text' => 'Посмотрите реальные занятия в Красногорске и выберите удобный формат.',
        'target' => '/sport/plavaniye/',
    ],
    '/blog/poyasa-v-thekvondo' => [
        'category' => 'Тхэквондо',
        'fallback_category' => 'Единоборства',
        'title' => 'Ребёнок хочет заниматься тхэквондо?',
        'text' => 'Сравните занятия рядом и посмотрите варианты по возрасту.',
        'target' => '/edinoborstva/thekvondo/',
    ],
    '/blog/thekvondo-dlya-detey' => [
        'category' => 'Тхэквондо',
        'fallback_category' => 'Единоборства',
        'title' => 'Подберите секцию тхэквондо',
        'text' => 'Возраст, адрес и доступные занятия в одном месте.',
        'target' => '/edinoborstva/thekvondo/',
    ],
    '/blog/edinoborstva-dlya-detey-kakoe-vybrat' => [
        'category' => 'Единоборства',
        'fallback_category' => 'Спорт',
        'title' => 'Какое единоборство подойдёт вашему ребёнку?',
        'text' => 'Сравните реальные занятия Красногорска по направлениям и возрасту.',
        'target' => '/edinoborstva/',
    ],
    '/blog/sambo-dlya-detey-chto-eto' => [
        'category' => 'Самбо',
        'fallback_category' => 'Единоборства',
        'title' => 'Найдите секцию самбо рядом',
        'text' => 'Посмотрите реальные занятия для детей и выберите подходящий вариант.',
        'target' => '/edinoborstva/sambo/',
    ],
];

if (!isset($pilots[$path])) {
    return;
}

$config = $pilots[$path];
$products_by_id = [];

foreach ([$config['category'], $config['fallback_category']] as $category_name) {
    if (count($products_by_id) >= 5) {
        break;
    }

    $category_id = (int) db_get_field(
        'SELECT category_id FROM ?:category_descriptions'
        . ' WHERE category = ?s AND lang_code = ?s'
        . ' ORDER BY category_id ASC LIMIT 1',
        $category_name,
        CART_LANGUAGE
    );

    if (!$category_id) {
        continue;
    }

    [$products] = fn_get_products([
        'cid' => $category_id,
        'subcats' => 'Y',
        'status' => 'A',
        'sort_by' => 'popularity',
        'sort_order' => 'desc',
    ], 5, CART_LANGUAGE);

    foreach ($products as $product) {
        $product_id = (int) ($product['product_id'] ?? 0);
        if (!$product_id || isset($products_by_id[$product_id])) {
            continue;
        }
        $products_by_id[$product_id] = $product;
        if (count($products_by_id) >= 5) {
            break;
        }
    }
}

$products = array_values($products_by_id);
if ($products) {
    fn_gather_additional_products_data($products, [
        'get_icon' => true,
        'get_detailed' => false,
        'get_options' => false,
        'get_discounts' => true,
        'get_features' => false,
    ]);
}

Tygh::$app['view']->assign('talario_article_marketplace_pilot', $config);
Tygh::$app['view']->assign('talario_article_marketplace_products', $products);
