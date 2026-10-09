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
        ['product_id' => 6, 'product' => 'Дзюдо. Красногорск', 'full_description' => ''],
        ['product_id' => 7, 'product' => 'Самбо. Красногорск', 'full_description' => ''],
        ['product_id' => 8, 'product' => 'Плавание. Красногорск', 'full_description' => ''],
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
        $page = max(1, (int) ($params['page'] ?? 1));
        return [array_slice($found, ($page - 1) * $limit, $limit), ['total_items' => count($found), 'page' => $page]];
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
        'дзюда' => [6],
        'дзу' => [6],
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
    // A corrected broad query must preserve both the count and navigation,
    // rather than reporting the first twenty products as the entire result.
    for ($id = 100; $id < 145; $id++) {
        $catalog[] = ['product_id' => $id, 'product' => 'Занятие Красногорск ' . $id, 'full_description' => ''];
    }
    $seen = [];
    foreach ([1, 2, 3] as $page) {
        $products = [];
        $params = ['dispatch' => 'products.search', 'q' => 'занятия красногрск', 'page' => $page];
        fn_talario_search_relevance_get_products_post($products, $params, 'ru');
        check($params['total_items'] === 51, 'Corrected search must preserve the full native count');
        check($params['page'] === $page, 'Requested page must not reset to page one');
        check(count($products) === ($page === 3 ? 11 : 20), 'Unexpected page size');
        foreach (array_column($products, 'product_id') as $id) {
            check(!isset($seen[$id]), 'Pages must not repeat the same products');
            $seen[$id] = true;
        }
    }
    check(count($seen) === 51, 'All native results must remain reachable');

    // The explicit exploratory form must search the city catalog instead of
    // returning an arbitrary lesson whose text happens to contain "занятия".
    $products = [$catalog[7]];
    $params = ['dispatch' => 'products.search', 'q' => 'занятия красногорск'];
    $calls = [];
    fn_talario_search_relevance_get_products_post($products, $params, 'ru');
    $broad_ids = array_column($products, 'product_id');
    check($broad_ids === array_merge([1, 2, 3, 6, 7, 8], range(100, 113)), 'Broad city search must return the city catalog');
    check($params['q'] === 'занятия красногорск', 'Broad query must remain visible and analytics-safe');
    check($params['total_items'] === 51, 'Broad city search must preserve its catalog total');
    check(count($calls) === 1 && !isset($calls[0]['dispatch']), 'Broad city lookup must be internal and bounded');
    check(fn_talario_search_relevance_is_broad_city_query(['dispatch' => 'products.search', 'q' => 'занятия для дошкольников 6 лет']) === false, 'Long constrained query must not be silently reduced');

    // Client-controlled page size must not change the server-side fallback branch.
    $products = [];
    $params = [
        'dispatch' => 'products.search',
        'q' => 'занятия красногрск',
        'items_per_page' => 1,
    ];
    fn_talario_search_relevance_get_products_post($products, $params, 'ru');
    check($params['total_items'] === 51, 'Client page size must not change fallback total');
    check(count($products) === 20, 'Server page size must control fallback work');
    require __DIR__ . '/../../app/addons/talario_search_relevance/related.php';
    $catalog[] = ['product_id' => 200, 'product' => 'Кикбоксинг. Красногорск', 'full_description' => ''];
    $catalog[] = ['product_id' => 201, 'product' => 'Плавание. Школа Самбо', 'search_words' => 'самбо', 'full_description' => ''];
    $catalog[] = ['product_id' => 202, 'product' => 'Танцы. Клуб Кикбоксинг', 'full_description' => ''];
    $catalog[] = ['product_id' => 203, 'product' => 'Самбо / Дзюдо. Красногорск', 'full_description' => ''];
    $catalog[] = ['product_id' => 204, 'product' => 'Самбо. Нахабино', 'full_description' => ''];
    $catalog[] = ['product_id' => 205, 'product' => 'Хореография. Красногорск', 'full_description' => ''];
    $catalog[] = ['product_id' => 206, 'product' => 'K-POP. Красногорск', 'full_description' => ''];
    $catalog[] = ['product_id' => 207, 'product' => 'Балет. Красногорск', 'full_description' => ''];
    $catalog[] = ['product_id' => 208, 'product' => 'Плавание. Клуб Хореография', 'full_description' => ''];
    $catalog[] = ['product_id' => 209, 'product' => 'Танцы / Хореография. Красногорск', 'full_description' => ''];
    $dance_results = [$catalog[4], $catalog[13], $catalog[14], $catalog[15], $catalog[16], $catalog[17]];
    $dance_params = ['dispatch' => 'products.search', 'q' => 'хореография', 'total_items' => count($dance_results)];
    fn_talario_search_relevance_get_products_post($dance_results, $dance_params, 'ru');
    check(array_column($dance_results, 'product_id') === [205, 206, 207, 209], 'Choreography search must keep dance titles and exclude swimming');
    check($dance_params['total_items'] === 4, 'Filtered choreography count must match visible dance results');
    $primary = [$catalog[5]];
    $calls = [];
    $params = ['q' => 'дзюда красногорск', 'company_id' => 10];
    $related = fn_talario_search_relevance_related_products($params, $primary, 'ru');
    check(array_column($related, 'product_id') === [7, 200], 'Related must exclude swimming, dances, exact lessons and other locations');
    check($primary === [$catalog[5]] && $params['q'] === 'дзюда красногорск', 'Primary results and original query must stay unchanged');
    check(count($calls) === 3, 'Related query budget must be bounded');
    foreach ($calls as $call) {
        check(!isset($call['dispatch']) && $call['pkeywords'] === 'N', 'Related lookup must not log searches or use broad keywords');
        check($call['company_id'] === 10 && $call['items_per_page'] === 6, 'Scope and query bounds must be preserved');
    }
    $primary = [$catalog[2]];
    $calls = [];
    $related = fn_talario_search_relevance_related_products(['q' => 'танцыы красногорск', 'company_id' => 10], $primary, 'ru');
    check(array_column($related, 'product_id') === [205, 206, 207], 'Dance related must include choreography, K-POP and ballet only');
    check(count($calls) === 3, 'Dance related query budget must be bounded');
    check(fn_talario_search_relevance_related_plan(['q' => 'танцыы красногорск'])['intent'] === 'танцы', 'Dance typo must canonicalize to dance intent');
    foreach (['дзюдо 6 лет', 'дзюдо по субботам', 'дзюдо кречет', 'плавание', 'танцы 6 лет', 'несуществующий запрос'] as $q) {
        check(fn_talario_search_relevance_related_plan(['q' => $q]) === [], 'Unknown or constrained intent must not be broadened');
    }
    foreach (['features_hash', 'filter_variants', 'pid', 'cid', 'price_from', 'price_to'] as $filter) {
        check(fn_talario_search_relevance_related_plan(['q' => 'дзюдо', $filter => 1]) === [], 'Filtered results must not be broadened');
    }
    check(fn_talario_search_relevance_related_plan(['q' => 'дзюдо', 'page' => 2]) === [], 'Related block belongs to first page only');
    check(fn_talario_search_relevance_related_products(['q' => 'дзюдо'], [], 'ru') === [], 'Empty native search must keep existing no-results flow');
    echo "TALARIO_SEARCH_BEHAVIOR=PASS\n";
}
