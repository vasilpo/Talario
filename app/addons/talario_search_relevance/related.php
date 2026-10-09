<?php

defined('BOOTSTRAP') or die('Access denied');

/** Explicit alternatives, never synonyms or part of the native result count. */
function fn_talario_search_relevance_related_plan(array $params): array
{
    foreach (['features_hash', 'filter_variants', 'pid', 'cid', 'price_from', 'price_to'] as $filter) {
        if (!empty($params[$filter])) {
            return [];
        }
    }
    if ((int) ($params['page'] ?? 1) > 1) {
        return [];
    }
    $query = fn_talario_search_relevance_normalize_query((string) ($params['q'] ?? ''));
    // Unknown qualifiers (age, district, schedule, brand) must not be discarded.
    if (!preg_match('/^(дзюдо|дзюда|дзюдоо|дзудо|дзу|самбо|кикбоксинг|кикбокисинг|бокс|танцы|танцыы|хореография|хореогрфия|хореограыия)( красногорск| нахабино| митино)?$/u', $query, $match)) {
        return [];
    }
    $intent = $match[1];
    if (in_array($intent, ['дзюда', 'дзюдоо', 'дзудо', 'дзу'], true)) {
        $intent = 'дзюдо';
    } elseif ($intent === 'кикбокисинг') {
        $intent = 'кикбоксинг';
    } elseif (in_array($intent, ['танцыы', 'хореогрфия', 'хореограыия'], true)) {
        $intent = 'танцы';
    }
    $map = [
        'дзюдо' => ['самбо', 'бразильское джиу-джитсу', 'кикбоксинг'],
        'самбо' => ['дзюдо', 'бразильское джиу-джитсу', 'вольная борьба'],
        'кикбоксинг' => ['тайский бокс', 'бокс'],
        'бокс' => ['кикбоксинг', 'тайский бокс'],
        'танцы' => ['хореография', 'k-pop', 'балет'],
    ];
    return ['intent' => $intent, 'terms' => $map[$intent], 'location' => trim($match[2] ?? '')];
}

/** Reject incidental mentions in school names/descriptions; preserve location. */
function fn_talario_search_relevance_related_matches(array $product, string $term, array $plan): bool
{
    $title = fn_talario_search_relevance_normalize_query((string) ($product['product'] ?? ''));
    if (preg_match('/^' . preg_quote($term, '/') . '(?:\s|$)/u', $title) !== 1) {
        return false;
    }
    if (preg_match('/(?<!\p{L})' . preg_quote($plan['intent'], '/') . '(?!\p{L})/u', $title) === 1) {
        return false;
    }
    return $plan['location'] === '' || preg_match('/(?<!\p{L})' . preg_quote($plan['location'], '/') . '(?!\p{L})/u', $title) === 1;
}

/** At most three bounded native queries, no visitor-search analytics dispatch. */
function fn_talario_search_relevance_related_products(array $params, array $primary, string $lang_code): array
{
    $plan = fn_talario_search_relevance_related_plan($params);
    if (!$plan || !$primary) {
        return [];
    }
    $seen = array_fill_keys(array_column($primary, 'product_id'), true);
    $result = [];
    foreach ($plan['terms'] as $term) {
        $internal = fn_talario_search_relevance_build_params($params, trim($term . ' ' . $plan['location']));
        $internal['pkeywords'] = 'N';
        $internal['page'] = 1;
        $internal['items_per_page'] = 6;
        [$candidates] = fn_get_products($internal, 6, $lang_code);
        $added = 0;
        foreach ($candidates as $product) {
            $id = (int) ($product['product_id'] ?? 0);
            if (!$id || isset($seen[$id]) || !fn_talario_search_relevance_related_matches($product, $term, $plan)) {
                continue;
            }
            $seen[$id] = true;
            $result[] = ['product_id' => $id, 'product' => $product['product']];
            if (++$added === 2) {
                break;
            }
        }
    }
    return $result;
}
