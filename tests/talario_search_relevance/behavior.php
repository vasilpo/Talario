<?php

namespace Tygh {
    class Registry
    {
        public static function get($key)
        {
            return 20;
        }
    }
}

namespace {
    define('BOOTSTRAP', true);
    require __DIR__ . '/../../app/addons/talario_search_relevance/func.php';

    // Controlled native-search substitute: descriptions deliberately mention
    // unrelated activities to catch the reported false-positive regression.
    $catalog = [
        ['product_id' => 1, 'product' => 'Лепка из глины. Красногорск', 'full_description' => ''],
        ['product_id' => 2, 'product' => 'Керамика. Красногорск', 'full_description' => ''],
        ['product_id' => 3, 'product' => 'Танцы. Красногорск', 'full_description' => 'У нас также керамика, глина и гончарное искусство'],
        ['product_id' => 4, 'product' => 'Программирование. Инсайт', 'full_description' => ''],
        ['product_id' => 5, 'product' => 'Артистическое синхронное плавание', 'search_words' => 'хореография', 'full_description' => ''],
    ];
    $calls = [];

    function fn_get_products($params, $limit, $language)
    {
        global $catalog, $calls;
        $calls[] = $params;
        $found = [];
        foreach ($catalog as $product) {
            $text = $product['product'];
            if (($params['pkeywords'] ?? 'N') === 'Y') {
                $text .= ' ' . ($product['search_words'] ?? '');
            }
            if (($params['pfull'] ?? 'N') === 'Y') {
                $text .= ' ' . $product['full_description'];
            }
            $matches = [];
            foreach (preg_split('/\s+/u', $params['q']) as $word) {
                $matches[] = mb_stripos($text, $word, 0, 'UTF-8') !== false;
            }
            $match = $params['match'] === 'all'
                ? !in_array(false, $matches, true)
                : in_array(true, $matches, true);
            if ($match) {
                $found[] = $product;
            }
        }
        return [array_slice($found, 0, $limit), ['total_items' => count($found)]];
    }

    function check($condition, $message)
    {
        if (!$condition) {
            throw new \RuntimeException($message);
        }
    }

    $cases = [
        'исайт' => [4],
        'инсйат' => [4],
        'танцыы' => [3],
        'гончарная мастерская' => [1, 2],
        'гонарка' => [1, 2],
        'занятия по керамике' => [1, 2],
        'армянские тарцы' => [],
        'гончарная мастерская москва' => [],
        'несуществующий запрос xyzqv' => [],
    ];
    foreach ($cases as $query => $expected) {
        $calls = [];
        $products = [];
        $params = ['dispatch' => 'products.search', 'q' => $query, 'company_id' => 10];
        fn_talario_search_relevance_get_products_post($products, $params, 'ru');
        $ids = array_column($products, 'product_id');
        sort($ids);
        check($ids === $expected, 'Unexpected results for ' . $query);
        check($params['q'] === $query, 'Original query must be preserved');
        check(count($calls) <= 6, 'Expansion must be bounded');
        foreach ($calls as $call) {
            check($call['company_id'] === 10, 'Vendor scope must be preserved');
            check(!isset($call['dispatch']), 'Internal queries must not be logged as visitor searches');
        }
    }
    $calls = [];
    $products = [$catalog[0]];
    $params = ['dispatch' => 'products.search', 'q' => 'глина'];
    fn_talario_search_relevance_get_products_post($products, $params, 'ru');
    check($products === [$catalog[0]] && $calls === [], 'Successful native search must stay unchanged');
    foreach (['features_hash', 'filter_variants', 'pid'] as $filter) {
        $products = [];
        $params = ['dispatch' => 'products.search', 'q' => 'исайт', $filter => '1'];
        fn_talario_search_relevance_get_products_post($products, $params, 'ru');
        check($calls === [], 'Explicit filter must not trigger fallback');
    }
    echo "TALARIO_SEARCH_BEHAVIOR=PASS\n";
}
