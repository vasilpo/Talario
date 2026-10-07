<?php

// phpcs:disable PSR1.Files.SideEffects

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Re-runs a failed storefront text search with meaningful terms only.
 *
 * The first CS-Cart search remains authoritative. This fallback is used only
 * for products.search requests with several words and no products, so exact
 * successful searches and filtered searches keep their standard behaviour.
 *
 * @param array<int, array<string, mixed>> $products
 * @param array<string, mixed>             $params
 * @param string                           $lang_code
 *
 * @return void
 */
function fn_talario_search_relevance_get_products_post(
    array &$products,
    array &$params,
    $lang_code
): void {
    static $fallback_running = false;

    if ($fallback_running || !empty($products) || !fn_talario_search_relevance_is_candidate($params)) {
        return;
    }

    $fallback_queries = fn_talario_search_relevance_build_queries($params);

    if ($fallback_queries === []) {
        return;
    }

    $original_query = (string) ($params['q'] ?? '');
    $fallback_running = true;
    $fallback_products = [];
    $fallback_search = [];
    $seen_product_ids = [];
    $page_size = max(1, (int) Registry::get('settings.Appearance.products_per_page'));

    foreach ($fallback_queries as $search_query) {
        $fallback_params = fn_talario_search_relevance_build_params($params, $search_query);

        if ($fallback_params === []) {
            continue;
        }

        [$query_products, $query_search] = fn_get_products($fallback_params, $page_size, $lang_code);

        if (empty($fallback_search)) {
            $fallback_search = $query_search;
        }

        foreach ($query_products as $product) {
            $product_id = (int) ($product['product_id'] ?? 0);

            if ($product_id === 0 || isset($seen_product_ids[$product_id])) {
                continue;
            }

            $seen_product_ids[$product_id] = true;
            $fallback_products[] = $product;

            if (count($fallback_products) >= $page_size) {
                break 2;
            }
        }
    }

    $fallback_running = false;

    if (empty($fallback_products)) {
        return;
    }

    $products = $fallback_products;
    $params = array_merge($params, $fallback_search, [
        'q' => $original_query,
        'talario_search_relevance_fallback' => true,
        'talario_search_relevance_original_query' => $original_query,
        'total_items' => count($fallback_products),
    ]);
}

/**
 * Restricts fallback to the public text search without explicit filters.
 *
 * @param array<string, mixed> $params
 *
 * @return bool
 */
function fn_talario_search_relevance_is_candidate(array $params): bool
{
    if (($params['dispatch'] ?? '') !== 'products.search'
        || empty($params['q'])
        || !empty($params['features_hash'])
        || !empty($params['filter_variants'])
        || !empty($params['pid'])
    ) {
        return false;
    }

    return preg_match('/\s/u', trim((string) $params['q'])) === 1;
}

/**
 * Builds a conservative second-pass search.
 *
 * @param array<string, mixed> $params
 *
 * @return array<string, mixed>
 */
function fn_talario_search_relevance_build_queries(array $params): array
{
    $query = trim((string) ($params['q'] ?? ''));
    $normalized_query = fn_talario_search_relevance_normalize_query($query);
    $queries = fn_talario_search_relevance_expand_terms($normalized_query);
    $pieces = preg_split('/\s+/u', mb_strtolower($query, 'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);
    $stop_words = [
        'а', 'в', 'во', 'для', 'и', 'из', 'к', 'на', 'по', 'с', 'у',
        'занятие', 'занятия', 'занятий', 'кружок', 'кружки', 'лет',
        'года', 'год', 'дошкольник', 'дошкольники', 'дошкольников',
    ];
    $meaningful = [];

    foreach ($pieces as $piece) {
        $piece = trim($piece, " \t\n\r\0\x0B.,!?;:()[]{}\"");

        if ($piece === '' || in_array($piece, $stop_words, true) || ctype_digit($piece)) {
            continue;
        }

        $meaningful[] = $piece;
    }

    if (count($meaningful) >= 1) {
        $queries[] = implode(' ', array_values(array_unique($meaningful)));
    }

    return array_values(array_unique(array_filter(array_slice($queries, 0, 6))));
}

/**
 * Builds a second-pass search for one normalized query variant.
 *
 * @param array<string, mixed> $params
 * @param string               $query
 *
 * @return array<string, mixed>
 */
function fn_talario_search_relevance_build_params(array $params, string $query): array
{
    if ($query === '') {
        return [];
    }

    $fallback_params = $params;
    $fallback_params['q'] = $query;
    $fallback_params['match'] = 'any';
    $fallback_params['page'] = 1;
    $fallback_params['search_performed'] = 'Y';
    $fallback_params['pname'] = 'Y';
    $fallback_params['pshort'] = 'Y';
    $fallback_params['pfull'] = 'Y';
    $fallback_params['pkeywords'] = 'Y';
    $fallback_params['talario_search_relevance_fallback_attempted'] = true;

    return $fallback_params;
}

/**
 * Normalizes Russian storefront search text without changing the original query.
 *
 * @param string $query Search query
 *
 * @return string
 */
function fn_talario_search_relevance_normalize_query(string $query): string
{
    $query = mb_strtolower(trim($query), 'UTF-8');
    $query = str_replace('ё', 'е', $query);
    $query = preg_replace('/[^\p{L}\p{N}\s-]+/u', ' ', $query) ?? '';
    $query = preg_replace('/\s+/u', ' ', $query) ?? '';

    return trim($query);
}

/**
 * Returns controlled synonym/typo variants. Related terms are intentionally
 * explicit rather than guessed, so a broad query cannot flood results.
 *
 * @param string $query Normalized query
 *
 * @return array<int, string>
 */
function fn_talario_search_relevance_expand_terms(string $query): array
{
    $dictionary = [
        'керамика' => ['гончар', 'гончарное искусство', 'гончарное дело', 'лепка из глины'],
        'гончарное искусство' => ['гончар', 'гончарное мастерство', 'гончарная мастерская', 'керамика'],
        'гончарная мастерская' => ['гончар', 'гончарное искусство', 'гончарное дело', 'керамика'],
        'скорочтение' => ['скорочтение', 'быстрое чтение'],
        'гармония кидс' => ['гармония kids', 'гармония'],
        'гармония kids' => ['гармония кидс', 'гармония'],
        'гармония кидсс' => ['гармония кидс', 'гармония kids'],
        'англиский' => ['английский', 'английский язык'],
        'программирование' => ['кодинг', 'программирование для детей'],
        'рисование' => ['живопись', 'изобразительное искусство'],
        'танцы' => ['хореография'],
    ];

    $variants = [];

    foreach ($dictionary as $term => $related_terms) {
        if ($query === $term) {
            $variants = array_merge($variants, $related_terms);
        }
    }

    return array_values(array_unique(array_filter($variants)));
}
