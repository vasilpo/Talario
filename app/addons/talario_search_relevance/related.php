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
    // Dance and choreography already include the full dance family in primary results.
    if (preg_match('/^(хореография|хореогрфия|хореограыия|танцы|танцыы)( красногорск| нахабино| митино)?$/u', $query)) {
        return [];
    }
    // Unknown qualifiers (age, district, schedule, brand) must not be discarded.
    if (!preg_match('/^(дзюдо|дзюда|дзюдоо|дзудо|дзу|самбо|кикбоксинг|кикбокисинг|бокс|каратэ|карате|каратте|каратж|танцы|танцыы|керамика|керамику|керамитка)( красногорск| нахабино| митино)?$/u', $query, $match)) {
        return [];
    }
    $intent = $match[1];
    if (in_array($intent, ['дзюда', 'дзюдоо', 'дзудо', 'дзу'], true)) {
        $intent = 'дзюдо';
    } elseif ($intent === 'кикбокисинг') {
        $intent = 'кикбоксинг';
    } elseif ($intent === 'танцыы') {
        $intent = 'танцы';
    } elseif (in_array($intent, ['керамику', 'керамитка'], true)) {
        $intent = 'керамика';
    }
    $map = [
        'дзюдо' => ['самбо', 'бразильское джиу-джитсу', 'кикбоксинг'],
        'самбо' => ['дзюдо', 'бразильское джиу-джитсу', 'вольная борьба'],
        'кикбоксинг' => ['тайский бокс', 'бокс'],
        'бокс' => ['кикбоксинг', 'тайский бокс'],
        'каратэ' => ['карате', 'каратте'],
        'карате' => ['каратэ', 'каратте'],
        'каратте' => ['каратэ', 'карате'],
        'каратж' => ['каратэ', 'карате', 'каратте'],
        'танцы' => ['хореография', 'k-pop', 'балет'],
        // Explicit catalog-backed clay activities, not arbitrary crafts.
        'керамика' => ['лепка из глины', 'лепим сказку', 'гончарное искусство'],
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

/**
 * Prepare related products with the same data enrichment as the catalog.
 *
 * The renderer is shared with the normal product grid, so related products
 * must receive its image, price, address and theme feature data as well.
 */
function fn_talario_search_relevance_prepare_related_products(array $products, string $lang_code): array
{
    if (!$products) {
        return [];
    }

    fn_gather_additional_products_data($products, [
        'get_icon'        => true,
        'get_detailed'    => true,
        'get_additional'  => true,
        'get_options'     => true,
        'get_discounts'   => true,
        'get_features'    => false,
    ], $lang_code);

    // The active theme uses this bulk modifier to expose catalog age/features.
    if (function_exists('fn_abt__ut2_add_products_features_list')) {
        fn_abt__ut2_add_products_features_list($products);
    }

    return $products;
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
        // Keep the same product fields as the normal catalog card. The theme's
        // sd_design_changes hook supplies address when description is extended.
        $internal['extend'] = array_values(array_unique(array_merge(
            is_array($internal['extend'] ?? null) ? $internal['extend'] : [],
            ['description', 'product_name']
        )));
        $internal['get_images'] = true;
        [$candidates] = fn_get_products($internal, 6, $lang_code);
        $added = 0;
        foreach ($candidates as $product) {
            $id = (int) ($product['product_id'] ?? 0);
            if (!$id || isset($seen[$id]) || !fn_talario_search_relevance_related_matches($product, $term, $plan)) {
                continue;
            }
            $seen[$id] = true;
            $result[] = $product;
            if (++$added === 2) {
                break;
            }
        }
    }

    return fn_talario_search_relevance_prepare_related_products($result, $lang_code);
}
