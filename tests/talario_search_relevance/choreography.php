<?php

// Execute the hook's actual SQL predicate before count/pagination. SQLite is
// only a local contract harness; live MariaDB acceptance remains mandatory.
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

function expect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function predicate(array $params, string $piece): string
{
    $tmp = 'native_search_condition';
    fn_talario_search_relevance_additional_fields_in_search(
        $params, [], [], ' AND company_id=10', '', '', '', $tmp, $piece, [], 'ru'
    );
    return $tmp;
}

$db->exec('CREATE TABLE products (id INTEGER PRIMARY KEY, product TEXT, company_id INTEGER, status TEXT, age INTEGER)');
$insert = $db->prepare('INSERT INTO products VALUES (?, ?, ?, ?, ?)');
$titles = [
    'K-POP. Jam. Красногорск',
    'Армянские танцы. Красногорск',
    'Армянские танцы. Нахабино',
    'Балет. Красногорск',
    'Бальные танцы. Красногорск',
    'Брейк данс. Красногорск',
    'К Поп. Красногорск',
    'Современная хореография. Красногорск',
    'Хореография. Красногорск',
];
foreach ($titles as $index => $title) {
    $insert->execute([$index + 1, $title, 10, 'A', 7]);
}
foreach ([
    'Артистическое синхронное плавание',
    'Плавание. Клуб Хореография',
    'Самбо. Школа Танцы',
    'Логопедическая ритмика',
    'Джазовый вокал',
    'Керамика. Танцы',
] as $index => $title) {
    $insert->execute([20 + $index, $title, 10, 'A', 7]);
}
$params = ['dispatch' => 'products.search', 'q' => 'хореография'];
foreach (['хореография', 'ХОРЕОГРАФИЯ', 'хореогрфия', 'хореограыия'] as $query) {
    $sql = predicate(array_merge($params, ['q' => $query]), $query);
    $ids = $db->query('SELECT id FROM products descr1 WHERE (' . $sql . ') ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
    expect(array_map('intval', $ids) === range(1, 9), 'Every catalog dance, and no incidental mention, must match: ' . $query);
}

// Exercise both native word-combination modes. A city must never turn into
// an OR escape that admits swimming or unrelated activities in that city.
$city = array_merge($params, ['q' => 'хореография красногорск']);
foreach ([' AND ', ' OR '] as $operator) {
    $sql = '(' . predicate($city, 'хореография') . ')' . $operator . '(' . predicate($city, 'красногорск') . ')';
    $ids = array_map('intval', $db->query('SELECT id FROM products descr1 WHERE ' . $sql . ' ORDER BY id')->fetchAll(PDO::FETCH_COLUMN));
    expect($ids === [1, 2, 4, 5, 6, 7, 8, 9], 'City restriction must survive native match mode');
}

// More than one page, and native vendor/status/feature restrictions.
for ($id = 100; $id < 145; $id++) {
    $insert->execute([$id, 'Танцы. Красногорск ' . $id, 10, 'A', 7]);
}
$insert->execute([300, 'Танцы. Красногорск', 11, 'A', 7]);
$insert->execute([301, 'Танцы. Красногорск', 10, 'D', 7]);
$insert->execute([302, 'Танцы. Красногорск', 10, 'A', 8]);
$sql = predicate($city, 'хореография');
$where = ' FROM products descr1 WHERE company_id=10 AND status=\'A\' AND age=7 AND (' . $sql . ')';
$total = (int) $db->query('SELECT COUNT(*)' . $where)->fetchColumn();
expect($total === 53, 'Count must include all matching pages but respect native scopes');
$seen = [];
foreach ([1, 2, 3] as $page) {
    $products = $db->query('SELECT id AS product_id, product' . $where . ' ORDER BY id LIMIT 20 OFFSET ' . (($page - 1) * 20))->fetchAll(PDO::FETCH_ASSOC);
    $search = array_merge($city, ['page' => $page, 'total_items' => $total, 'features_hash' => 'age-7']);
    $before = [$products, $search];
    fn_talario_search_relevance_get_products_post($products, $search, 'ru');
    expect([$products, $search] === $before, 'Post hook must not truncate, re-count or retry the native result');
    expect(count($products) === ($page === 3 ? 13 : 20), 'Pagination must reach the complete result');
    $seen = array_merge($seen, array_column($products, 'product_id'));
}
expect(count(array_unique($seen)) === 53, 'Pages must not duplicate records');
$empty = [];
$search = array_merge($city, ['total_items' => 0, 'features_hash' => 'age-99']);
fn_talario_search_relevance_get_products_post($empty, $search, 'ru');
expect($empty === [] && $search['total_items'] === 0, 'No matching filter must remain genuinely empty');

foreach ([
    ['dispatch' => 'categories.view', 'q' => 'хореография'],
    ['dispatch' => 'products.search', 'q' => 'хореография', 'area' => 'A'],
    ['q' => 'хореография'],
    ['dispatch' => 'products.search', 'q' => 'хореография 6 лет'],
    ['dispatch' => 'products.search', 'q' => 'хореография москва'],
    ['dispatch' => 'products.search', 'q' => 'танцы'],
    ['dispatch' => 'products.search', 'q' => 'гончарная мастерская'],
] as $unchanged) {
    expect(predicate($unchanged, 'хореография') === 'native_search_condition', 'Unrelated scope must retain native conditions');
}
echo "TALARIO_CHOREOGRAPHY_SQL_CONTRACT=PASS\n";
