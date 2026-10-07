#!/usr/bin/env bash
set -euo pipefail

php -r '
define("BOOTSTRAP", true);
require "app/addons/talario_search_relevance/func.php";

$cases = [
    ["КЕРАМИКА", ["глина", "гончарное искусство"]],
    ["гончарная мастерская", ["глина", "керамика"]],
    ["гармония кидсс", ["гармония кидс"]],
    ["скорочтение", ["быстрое чтение"]],
];

foreach ($cases as [$query, $expected]) {
    $variants = fn_talario_search_relevance_expand_terms(
        fn_talario_search_relevance_normalize_query($query)
    );

    foreach ($expected as $term) {
        if (!in_array($term, $variants, true)) {
            fwrite(STDERR, "Missing variant: {$query} -> {$term}\n");
            exit(1);
        }
    }
}

if (fn_talario_search_relevance_normalize_query("Ёлки!!!  6 ЛЕТ") !== "елки 6 лет") {
    fwrite(STDERR, "Normalization failed\n");
    exit(1);
}

$specific_results = [
    ["product_id" => 10, "product" => "Лепка из глины"],
];
$unrelated_results = [
    ["product_id" => 20, "product" => "Плавание"],
    ["product_id" => 30, "product" => "Танцы"],
];

if (!fn_talario_search_relevance_should_select_variant([], $specific_results)
    || fn_talario_search_relevance_should_select_variant($specific_results, $unrelated_results)
) {
    fwrite(STDERR, "Lower-priority variants must not be merged into a successful specific result\n");
    exit(1);
}

$single_word_params = fn_talario_search_relevance_build_params([], "глина");
$phrase_params = fn_talario_search_relevance_build_params([], "лепка из глины");

if ($single_word_params["match"] !== "any" || $phrase_params["match"] !== "all") {
    fwrite(STDERR, "Semantic fallback must require all words in a multi-word variant\n");
    exit(1);
}

echo "TALARIO_SEARCH_RELEVANCE_TESTS=PASS\n";
'
