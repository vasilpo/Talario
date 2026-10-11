<?php

define('BOOTSTRAP', true);
define('AREA', 'C');
require __DIR__ . '/../../app/addons/talario_search_relevance/func.php';

$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->sqliteCreateFunction('regexp', static function ($pattern, $value) {
    return preg_match('~' . $pattern . '~u', $value) === 1 ? 1 : 0;
}, 2);
$db->sqliteCreateFunction('lower', static function ($value) {
    return mb_strtolower($value, 'UTF-8');
}, 1);

function db_quote($sql, $value)
{
    global $db;
    return str_replace('?s', $db->quote($value), $sql);
}

function expect_karate($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$db->exec('CREATE TABLE products (id INTEGER PRIMARY KEY, product TEXT, company_id INTEGER, status TEXT, age INTEGER)');
$insert = $db->prepare('INSERT INTO products VALUES (?, ?, ?, ?, ?)');
foreach ([
    [1, 'Школа карате «Первый Удар» в Красногорске', 10, 'A', 7],
    [2, 'Карате в клубе Зевс. Красногорск', 10, 'A', 7],
    [3, 'Каратэ. Красногорск', 10, 'A', 7],
    [4, 'Плавание. Клуб Каратэ', 10, 'A', 7],
    [5, 'Карате. Нахабино', 10, 'A', 7],
    [6, 'Карате. Красногорск', 11, 'A', 7],
    [7, 'Карате. Красногорск', 10, 'D', 7],
    [8, 'Карате. Красногорск', 10, 'A', 8],
] as $row) {
    $insert->execute($row);
}

foreach (['каратэ', 'карате', 'каратте', 'каратж'] as $query) {
    $tmp = 'native_search_condition';
    $params = ['dispatch' => 'products.search', 'q' => $query];
    fn_talario_search_relevance_additional_fields_in_search(
        $params, [], [], '', '', '', '', $tmp, $query, [], 'ru'
    );
    $ids = array_map('intval', $db->query('SELECT id FROM products WHERE company_id=10 AND status=\'A\' AND age=7 AND (' . $tmp . ') ORDER BY id')->fetchAll(PDO::FETCH_COLUMN));
    expect_karate($ids === [1, 2, 3, 5], 'All karate spellings must share the complete relevant result: ' . $query);
}

foreach ([
    ['dispatch' => 'products.search', 'q' => 'каратэ 7 лет'],
    ['dispatch' => 'categories.view', 'q' => 'каратэ'],
    ['dispatch' => 'products.search', 'q' => 'каратэ', 'area' => 'A'],
] as $params) {
    $tmp = 'native_search_condition';
    fn_talario_search_relevance_additional_fields_in_search(
        $params, [], [], '', '', '', '', $tmp, 'каратэ', [], 'ru'
    );
    expect_karate($tmp === 'native_search_condition', 'Unsupported karate scope must remain native');
}

echo "TALARIO_KARATE_SQL_CONTRACT=PASS\n";
