#!/usr/bin/env bash
set -euo pipefail

php -r '
define("BOOTSTRAP", true);
require "app/addons/talario_search_relevance/func.php";

$cases = [
    ["КЕРАМИКА", ["глина", "гончарное искусство"]],
    ["керамику", ["керамика", "гончарная мастерская"]],
    ["гончарка", ["керамика", "гончарное искусство"]],
    ["гончарная мастерская", ["глина", "керамика"]],
    ["гончарному искусству", ["керамика", "гончарная мастерская"]],
    ["гончарное искусство", ["глина", "керамика"]],
    ["гармония кидсс", ["гармония кидс"]],
    ["скорочтение", ["быстрое чтение"]],
    ["исайт", ["инсайт"]],
    ["инсйат", ["инсайт"]],
    ["танцыы", ["танцы"]],
    ["дзу", ["дзюдо"]],
    ["дзюда", ["дзюдо"]],
    ["плавние", ["плавание"]],
    ["басейн", ["бассейн"]],
    ["кикбокисинг", ["кикбоксинг"]],
    ["тхеквондо", ["тхэквондо"]],
    ["хореогрфия", ["хореография"]],
    ["керамитка", ["керамика"]],
    ["програмирование", ["программирование"]],
    ["робототехнка", ["робототехника"]],
    ["шахмоты", ["шахматы"]],
    ["англицский", ["английский"]],
    ["психолг", ["психолог"]],
    ["биолабораториум", ["биолаб"]],
    ["биолабвраториум", ["биолаб"]],
    ["биолаборатриум", ["биолаб"]],
    ["biolaboratorium", ["биолаб"]],
    ["армянские тарцы", ["армянские танцы"]],
    ["балеь", ["балет"]],
    ["гонарка", ["гончарка"]],
    ["гочарка", ["гончарка"]],
    ["гарже", ["гарде"]],
    ["генезим", ["генезис"]],
    ["каратж", ["каратэ"]],
    ["клиграфия", ["каллиграфия"]],
    ["кречкет", ["кречет"]],
    ["шахмаы", ["шахматы"]],
    ["юокс", ["бокс"]],
    ["занятия красногрск", ["занятия красногорск"]],
    ["4науки", ["четыре науки"]],
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

foreach (["гончарка", "гончарная мастерская", "гончарное искусство", "гончарному искусству", "керамику", "занятия по керамике"] as $query) {
    $variants = fn_talario_search_relevance_build_queries(["q" => $query]);

    if (!in_array("керамика", $variants, true)) {
        fwrite(STDERR, "Missing ceramic result path for query: {$query}\n");
        exit(1);
    }
}

if (fn_talario_search_relevance_normalize_query("Ёлки!!!  6 ЛЕТ") !== "елки 6 лет") {
    fwrite(STDERR, "Normalization failed\n");
    exit(1);
}

$specific_results = [
    ["product_id" => 10, "product" => "Лепка из глины"],
];
$ceramic_results = [
    ["product_id" => 20, "product" => "Керамика"],
    ["product_id" => 10, "product" => "Лепка из глины"],
];

$merged_results = fn_talario_search_relevance_merge_variant_products(
    $specific_results,
    $ceramic_results,
    20
);

if (count($merged_results) !== 2
    || $merged_results[0]["product"] !== "Лепка из глины"
    || $merged_results[1]["product"] !== "Керамика"
) {
    fwrite(STDERR, "Related pottery and ceramic results must be combined without duplicates\n");
    exit(1);
}

$single_word_search = fn_talario_search_relevance_is_candidate([
    "dispatch" => "products.search",
    "q" => "гончарка",
]);
$ordinary_single_word_search = fn_talario_search_relevance_is_candidate([
    "dispatch" => "products.search",
    "q" => "плавание",
]);

if (!$single_word_search || $ordinary_single_word_search) {
    fwrite(STDERR, "Fallback must include known one-word aliases only\n");
    exit(1);
}

$single_word_params = fn_talario_search_relevance_build_params([], "глина");
$phrase_params = fn_talario_search_relevance_build_params([], "лепка из глины");

if ($single_word_params["match"] !== "any" || $phrase_params["match"] !== "all") {
    fwrite(STDERR, "Semantic fallback must require all words in a multi-word variant\n");
    exit(1);
}

$deep_page_params = fn_talario_search_relevance_build_params([], "глина");
$deep_page_params = fn_talario_search_relevance_build_params(["page" => 99], "глина");
if ($deep_page_params["page"] !== 10) {
    fwrite(STDERR, "Fallback page depth must be capped\n");
    exit(1);
}

if (isset($phrase_params["dispatch"]) || empty($phrase_params["disable_searchanise"])) {
    fwrite(STDERR, "Internal synonym queries must bypass external search and user-search analytics hooks\n");
    exit(1);
}

echo "TALARIO_SEARCH_RELEVANCE_TESTS=PASS\n";
'

php tests/talario_search_relevance/behavior.php
