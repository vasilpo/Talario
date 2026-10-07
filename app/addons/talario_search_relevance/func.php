<?php

// phpcs:disable PSR1.Files.SideEffects

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/**
 * Re-runs a failed storefront text search with meaningful terms only.
 *
 * The first CS-Cart search remains authoritative. This fallback is used only
 * for no-result products.search requests with several words or a known
 * semantic alias, so successful exact searches and filtered searches keep
 * their standard behaviour.
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
    $page_size = max(1, (int) Registry::get('settings.Appearance.products_per_page'));

    foreach ($fallback_queries as $search_query) {
        $fallback_params = fn_talario_search_relevance_build_params($params, $search_query);

        if ($fallback_params === []) {
            continue;
        }

        [$query_products, $query_search] = fn_get_products($fallback_params, $page_size, $lang_code);

        if (empty($query_products)) {
            continue;
        }

        if (empty($fallback_search)) {
            $fallback_search = $query_search;
        }

        $fallback_products = fn_talario_search_relevance_merge_variant_products(
            $fallback_products,
            $query_products,
            $page_size
        );

        if (count($fallback_products) >= $page_size) {
            break;
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

    $query = fn_talario_search_relevance_normalize_query((string) $params['q']);

    return preg_match('/\s/u', $query) === 1
        || fn_talario_search_relevance_expand_terms($query) !== [];
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
    $candidates = array_merge(fn_talario_search_relevance_expand_terms($normalized_query), [$normalized_query]);
    $queries = [];
    $stop_words = [
        'а', 'в', 'во', 'для', 'и', 'из', 'к', 'на', 'по', 'с', 'у',
        'занятие', 'занятия', 'занятий', 'кружок', 'кружки', 'лет',
        'года', 'год', 'дошкольник', 'дошкольники', 'дошкольников',
    ];
    foreach ($candidates as $candidate) {
        $pieces = preg_split('/\s+/u', $candidate, -1, PREG_SPLIT_NO_EMPTY);
        $meaningful = [];
        foreach ($pieces as $piece) {
            if (!in_array($piece, $stop_words, true) && !ctype_digit($piece)) {
                $meaningful[] = $piece;
            }
        }
        if ($meaningful !== []) {
            $queries[] = implode(' ', array_values(array_unique($meaningful)));
        }
    }

    return array_slice(array_values(array_unique(array_filter($queries))), 0, 6);
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
    $fallback_params['match'] = preg_match('/\s/u', $query) === 1 ? 'all' : 'any';
    $fallback_params['page'] = 1;
    $fallback_params['search_performed'] = 'Y';
    $fallback_params['pname'] = 'Y';
    // A studio description can mention many unrelated activities. Expand only
    // against the lesson title and explicit search keywords.
    $fallback_params['pshort'] = 'N';
    $fallback_params['pfull'] = 'N';
    $fallback_params['pkeywords'] = 'Y';
    $fallback_params['talario_search_relevance_fallback_attempted'] = true;
    $fallback_params['disable_searchanise'] = true;
    unset($fallback_params['dispatch']);

    return $fallback_params;
}

/**
 * Combines products from related variants without duplicate product IDs.
 *
 * @param array<int, array<string, mixed>> $selected_products
 * @param array<int, array<string, mixed>> $candidate_products
 * @param int                              $page_size Maximum number of results
 *
 * @return array<int, array<string, mixed>>
 */
function fn_talario_search_relevance_merge_variant_products(
    array $selected_products,
    array $candidate_products,
    int $page_size
): array {
    $merged_products = [];
    $seen_product_ids = [];

    foreach (array_merge($selected_products, $candidate_products) as $product) {
        $product_id = (int) ($product['product_id'] ?? 0);

        if ($product_id === 0 || isset($seen_product_ids[$product_id])) {
            continue;
        }

        $seen_product_ids[$product_id] = true;
        $merged_products[] = $product;

        if (count($merged_products) >= max(1, $page_size)) {
            break;
        }
    }

    return $merged_products;
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
    $pottery_terms = [
        'глина',
        'лепка из глины',
        'гончар',
        'керамика',
        'гончарка',
        'гончарная мастерская',
        'гончарное искусство',
        'гончарное мастерство',
        'гончарное дело',
    ];
    $pottery_aliases = [
        'глина',
        'лепка из глины',
        'гончар',
        'керамика',
        'керамику',
        'керамике',
        'керамики',
        'керамикой',
        'гончарка',
        'гончарку',
        'гончарке',
        'гончарки',
        'гонарка',
        'гочарка',
        'гончарная мастерская',
        'гончарную мастерскую',
        'гончарной мастерской',
        'гончарные мастерские',
        'гончарных мастерских',
        'гончарное искусство',
        'гончарного искусства',
        'гончарному искусству',
        'гончарным искусством',
        'гончарное мастерство',
        'гончарного мастерства',
        'гончарному мастерству',
        'гончарным мастерством',
        'гончарное дело',
        'гончарному делу',
    ];
    $dictionary = [
        'скорочтение' => ['скорочтение', 'быстрое чтение'],
        'гармония кидс' => ['гармония kids', 'гармония'],
        'гармония kids' => ['гармония кидс', 'гармония'],
        'гармония кидсс' => ['гармония кидс', 'гармония kids'],
        'англиский' => ['английский', 'английский язык'],
        'исайт' => ['инсайт'],
        'инсйат' => ['инсайт'],
        'танцыы' => ['танцы', 'хореография'],
        'дзу' => ['дзюдо'],
        'биолабораториум' => ['биолаб', 'биолаборатория'],
        'биолабвраториум' => ['биолаб', 'биолаборатория'],
        'биолаборатриум' => ['биолаб', 'биолаборатория'],
        'biolaboratorium' => ['биолаб', 'биолаборатория'],
        'армянские тарцы' => ['армянские танцы'],
        'балеь' => ['балет'],
        'гонарка' => ['гончарка', 'керамика'],
        'гочарка' => ['гончарка', 'керамика'],
        'гарже' => ['гарде'],
        'генезим' => ['генезис'],
        'каратж' => ['каратэ', 'карате'],
        'клиграфия' => ['каллиграфия', 'калиграфия'],
        'кречкет' => ['кречет'],
        'шахмаы' => ['шахматы'],
        'юокс' => ['бокс'],
        'занятия красногрск' => ['занятия красногорск'],
        '4науки' => ['четыре науки', '4 науки'],
        'программирование' => ['кодинг', 'программирование для детей'],
        'рисование' => ['живопись', 'изобразительное искусство'],
        'танцы' => ['хореография'],
    ];

    foreach ($pottery_aliases as $alias) {
        if ($query === $alias
            || preg_match('/(?:^|\s)' . preg_quote($alias, '/') . '(?:$|\s)/u', $query) === 1
        ) {
            $variants = [];
            foreach ($pottery_terms as $term) {
                // Keep location and other qualifiers surrounding the alias.
                $variant = preg_replace('/(?<!\S)' . preg_quote($alias, '/') . '(?!\S)/u', $term, $query);
                if ($variant !== null && $variant !== $query) {
                    $variants[] = $variant;
                }
            }
            return array_values(array_unique($variants));
        }
    }

    $variants = $dictionary[$query] ?? [];

    return array_values(array_unique(array_filter($variants)));
}
