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

    $normalized_query = fn_talario_search_relevance_normalize_query((string) ($params['q'] ?? ''));
    $is_broad_city_query = fn_talario_search_relevance_is_broad_city_query($params);

    if (!$is_broad_city_query && !empty($products)) {
        $filtered_products = fn_talario_search_relevance_filter_dance_products($products, $normalized_query);
        if (count($filtered_products) !== count($products)) {
            $params['total_items'] = isset($params['total_items'])
                ? count($filtered_products)
                : ($params['total_items'] ?? count($filtered_products));
            $params['talario_search_relevance_filtered'] = true;
            $products = $filtered_products;
        }
    }

    if ($fallback_running
        || !empty($params['talario_search_relevance_filtered'])
        || (!$is_broad_city_query && !empty($products))
        || (!$is_broad_city_query && !fn_talario_search_relevance_is_candidate($params))
    ) {
        return;
    }

    if ($is_broad_city_query) {
        $location = preg_replace('/^занятия\s+/u', '', $normalized_query) ?? '';
        $broad_params = fn_talario_search_relevance_build_params($params, $location);
        $page_size = max(1, (int) Registry::get('settings.Appearance.products_per_page'));

        if ($broad_params !== []) {
            [$broad_products, $broad_search] = fn_get_products($broad_params, $page_size, $lang_code);
            if (!empty($broad_products)) {
                $products = $broad_products;
                $params = array_merge($params, $broad_search, [
                    'q' => (string) ($params['q'] ?? ''),
                    'talario_search_relevance_broad_query' => true,
                    'total_items' => (int) ($broad_search['total_items'] ?? count($broad_products)),
                ]);
            }
        }

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
    $fallback_total = null;
    // Use the server-side page size. Client-supplied items_per_page must not
    // control fallback branching or cause oversized backend work.
    $configured_page_size = (int) Registry::get('settings.Appearance.products_per_page');
    $page_size = max(1, $configured_page_size);

    foreach ($fallback_queries as $search_query) {
        $fallback_params = fn_talario_search_relevance_build_params($params, $search_query);

        if ($fallback_params === []) {
            continue;
        }

        [$query_products, $query_search] = fn_get_products($fallback_params, $page_size, $lang_code);

        if (empty($query_products)) {
            continue;
        }

        // A multi-page native result must keep its own total and page. A
        // bounded synonym union cannot represent the full native result set.
        if ((int) ($query_search['total_items'] ?? 0) > $page_size) {
            $fallback_products = $query_products;
            $fallback_search = $query_search;
            $fallback_total = (int) $query_search['total_items'];
            break;
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
        'total_items' => $fallback_total ?? count($fallback_products),
    ]);
}

/**
 * Removes incidental non-dance matches from exact dance-intent searches.
 * Native CS-Cart search may match "хореография" in a swimming description.
 *
 * @param array<int, array<string, mixed>> $products
 * @param string                           $query
 *
 * @return array<int, array<string, mixed>>
 */
function fn_talario_search_relevance_filter_dance_products(array $products, string $query): array
{
    if (fn_talario_search_relevance_dance_intent($query) === '') {
        return $products;
    }

    return array_values(array_filter($products, static function (array $product): bool {
        $title = fn_talario_search_relevance_normalize_query((string) ($product['product'] ?? ''));

        if (preg_match('/(?:плаван|бассейн|синхрон)/u', $title) === 1) {
            return false;
        }

        return preg_match('/(?:танц|хореограф|балет|k[- ]?pop|ритмик|джаз|брейк)/u', $title) === 1;
    }));
}

/**
 * Canonicalizes the explicitly supported dance search forms.
 *
 * @param string $query
 *
 * @return string Empty string when the query contains unsupported qualifiers.
 */
function fn_talario_search_relevance_dance_intent(string $query): string
{
    $query = fn_talario_search_relevance_normalize_query($query);

    if (preg_match('/^(танцы|танцыы|хореография|хореогрфия|хореограыия)(?: красногорск| нахабино| митино)?$/u', $query, $match) !== 1) {
        return '';
    }

    return 'танцы';
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
 * Treat only the explicit exploratory form "занятия + city" as a catalog
 * discovery request. Longer queries retain their constraints and are not
 * silently reduced to a city-only search.
 *
 * @param array<string, mixed> $params
 *
 * @return bool
 */
function fn_talario_search_relevance_is_broad_city_query(array $params): bool
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

    return preg_match('/^занятия (красногорск|нахабино|митино)$/u', $query) === 1;
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
    // Keep pagination useful for normal navigation, but cap fallback work for
    // arbitrary deep pages supplied by a client.
    $fallback_params['page'] = min(10, max(1, (int) ($params['page'] ?? 1)));
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
        'танцыы' => ['танцы'],
        'дзу' => ['дзюдо'],
        'дзюда' => ['дзюдо'],
        'дзюдоо' => ['дзюдо'],
        'дзудо' => ['дзюдо'],
        'плаванье' => ['плавание'],
        'плавние' => ['плавание'],
        'плование' => ['плавание'],
        'басейн' => ['бассейн'],
        'бассеин' => ['бассейн'],
        'кикбокисинг' => ['кикбоксинг'],
        'кик бокинг' => ['кикбоксинг'],
        'кик-боксинг' => ['кикбоксинг'],
        'тайский боксс' => ['тайский бокс'],
        'каратте' => ['карате'],
        'тхеквондо' => ['тхэквондо'],
        'теквондо' => ['тхэквондо'],
        'таэквондо' => ['тхэквондо'],
        'волная борьба' => ['вольная борьба'],
        'борба' => ['борьба'],
        'бадминтонн' => ['бадминтон'],
        'бадментон' => ['бадминтон'],
        'футболл' => ['футбол'],
        'фудбол' => ['футбол'],
        'хореогрфия' => ['хореография'],
        'хореограыия' => ['хореография'],
        'баллет' => ['балет'],
        'керамитка' => ['керамика'],
        'рисавание' => ['рисование'],
        'рисоване' => ['рисование'],
        'програмирование' => ['программирование'],
        'программиравание' => ['программирование'],
        'роботатехника' => ['робототехника'],
        'робототехнка' => ['робототехника'],
        'калиграфия' => ['каллиграфия'],
        'шахмоты' => ['шахматы'],
        'англицский' => ['английский'],
        'испанскй' => ['испанский'],
        'матиматика' => ['математика'],
        'математиа' => ['математика'],
        'лагопед' => ['логопед'],
        'логопет' => ['логопед'],
        'психолг' => ['психолог'],
        'псехолог' => ['психолог'],
        'вакал' => ['вокал'],
        'вокалл' => ['вокал'],
        'биэмикс' => ['bmx'],
        'бмкс' => ['bmx'],
        'бмх' => ['bmx'],
        'биговел' => ['беговел'],
        'бегавел' => ['беговел'],
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
