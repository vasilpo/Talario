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

echo "TALARIO_SEARCH_RELEVANCE_TESTS=PASS\n";
'
