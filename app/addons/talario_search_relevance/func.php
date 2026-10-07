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

    $fallback_params = fn_talario_search_relevance_build_params($params);

    if ($fallback_params === []) {
        return;
    }

    $fallback_running = true;
    [$fallback_products, $fallback_search] = fn_get_products(
        $fallback_params,
        Registry::get('settings.Appearance.products_per_page'),
        $lang_code
    );
    $fallback_running = false;

    if (empty($fallback_products)) {
        return;
    }

    $products = $fallback_products;
    $params = array_merge($params, $fallback_search, [
        'talario_search_relevance_fallback' => true,
        'talario_search_relevance_original_query' => (string) ($params['q'] ?? ''),
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
function fn_talario_search_relevance_build_params(array $params): array
{
    $query = trim((string) ($params['q'] ?? ''));
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

    if (count($meaningful) < 1) {
        return [];
    }

    $fallback_params = $params;
    $fallback_params['q'] = implode(' ', array_values(array_unique($meaningful)));
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
