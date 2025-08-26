<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2025   *
* / /_\ | | _____  _| |_/ /_ __ __ _ _ __   __| |_ _ __   __ _   | |_ ___  __ _ _ __ ___   *
* |  _  | |/ _ \ \/ / ___ \ '__/ _` | '_ \ / _` | | '_ \ / _` |  | __/ _ \/ _` | '_ ` _ \  *
* | | | | |  __/>  <| |_/ / | | (_| | | | | (_| | | | | | (_| |  | ||  __/ (_| | | | | | | *
* \_| |_/_|\___/_/\_\____/|_|  \__,_|_| |_|\__,_|_|_| |_|\__, |  \___\___|\__,_|_| |_| |_| *
*                                                         __/ |                            *
*                                                        |___/                             *
* ---------------------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license and accept    *
* to the terms of the License Agreement can install and use this program.                  *
* ---------------------------------------------------------------------------------------- *
* website: https://cs-cart.alexbranding.com                                                *
*   email: info@alexbranding.com                                                           *
*******************************************************************************************/
use Tygh\Enum\Addons\Ab_stickers\StickerTypes;
use Tygh\Enum\Addons\Discussion\DiscussionObjectTypes;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\YesNo;
use Tygh\Registry;
defined('BOOTSTRAP') or die('Access denied');

function fn_ab__stickers_generate_links($sticker_ids = '', $storefront_id = 0, $is_ajax = true)
{
$generated_for = [];
$params = [
'item_ids' => empty($sticker_ids) ? '' : $sticker_ids,
'status' => (empty($sticker_ids) && strlen((trim($sticker_ids))) == 0) ? [ObjectStatuses::ACTIVE] : [],
'storefront_id' => $storefront_id,
'type' => StickerTypes::CONSTANT,
];
$time_update_barrier = 0;

$repository = Tygh::$app['addons.ab__stickers.repository'];
$stickers_count = $repository->find(array_merge($params, [
'count_only' => true,
]));
if ($stickers_count) {
$is_commet = defined('AJAX_REQUEST') && $is_ajax;
if ($is_commet) {
fn_set_progress('parts', $stickers_count);
fn_set_progress('title', __('ab__stickers.generate.in_progress'));
$stickers_completed = $repository->find(array_merge($params, [
'update_time_from' => $time_update_barrier,
'count_only' => true,
]));
for ($i = 0; $i < $stickers_completed; $i++) {
fn_set_progress('echo', __('ab__stickers.generate.checking_generated'));
}
}
list($stickers) = $repository->find(array_merge($params, [
'update_time_to' => $time_update_barrier,
]));
foreach ($stickers as $sticker_id => $sticker) {
list($product_ids) = fn_ab__stickers_generate_sticker_links($sticker_id, $sticker['conditions'], $storefront_id);
$products_count = count($product_ids);
$generated_for[] = defined('CONSOLE') ?
"sticker $sticker_id (links count: {$products_count})" :
__('ab__stickers.generated_for_sticker', [
'[sticker]' => $sticker['name_for_admin'],
'[sticker_href]' => fn_url('ab__stickers.update?sticker_id=' . $sticker_id),
'[links]' => $products_count, '[links_href]' => fn_url('products.manage?ab__stickers_generated_ids=&ab__stickers_generated_ids[]=' . $sticker_id), ]
);
}
}
return [!empty($generated_for), $generated_for];
}

function fn_ab__stickers_generate_sticker_links($sticker_id, $conditions = [], $storefront_id = 0)
{

$storefront = Tygh::$app['storefront.repository']->findById($storefront_id);
if (!empty($conditions['conditions'])) {
list($products) = fn_get_products([
'ab__sticker_conditions' => $conditions,
'storefront' => $storefront,
'load_products_extra_data' => false,
'show_all_products' => true,
'sort_by' => 'null'
]);
$product_ids = array_keys($products);
} else {
$product_ids = [];
}
fn_ab__stickers_attach_sticker_to_products($sticker_id, $product_ids, 'generated');
return [$product_ids, $sticker_id];
}

function fn_ab__stickers_build_sticker_conditions_query($conditions)
{
$auth = Tygh::$app['session']['auth'];
$usergroup_ids = !empty($auth['usergroup_ids']) ? $auth['usergroup_ids'] : [];
$operators = [
'1' => [
'eq' => '=',
'neq' => '<>',
'lte' => '<=',
'gte' => '>=',
'lt' => '<',
'gt' => '>',
'in' => 'IN',
'nin' => 'NOT IN',
'exist' => 'IS NOT NULL',
'cont' => 'CONTAINS',
'ncont' => 'NOT CONTAINS'
],
'0' => [
'eq' => '<>',
'neq' => '=',
'lte' => '>',
'gte' => '<',
'lt' => '>=',
'gt' => '<=',
'in' => 'NOT IN',
'nin' => 'IN',
'exist' => 'IS NULL',
'cont' => 'NOT CONTAINS',
'ncont' => 'CONTAINS'
],
];
$join = [];
$condition_value_required = [
'categories',
];
if (!empty($conditions['set']) && $conditions['set'] === 'all') {
$where = $having = '1';
$and_or = 'AND';
} else {
$where = $having = '0';
$and_or = 'OR';
}
if (!empty($conditions['conditions'])) {
foreach ($conditions['conditions'] as $condition) {
if (isset($condition['set'], $condition['conditions'])) {
list($sub_where, $sub_join, $sub_having) = fn_ab__stickers_build_sticker_conditions_query($condition);
$where .= db_quote(' ?p (?p)', $and_or, $sub_where);
$having .= db_quote(' ?p (?p)', $and_or, $sub_having);
$join = array_merge($join, $sub_join);
} elseif (in_array($condition['condition'], $condition_value_required) && empty($condition['value'])) {
$where = $having = 0;
$join = [];
break;
} elseif ($condition['condition'] === 'price') {
$where .= db_quote(' ?p ab__product_prices.price ?p ?d', $and_or, $operators[$conditions['set_value']][$condition['operator']], $condition['value']);
$join['product_prices'] = db_quote('LEFT JOIN ?:product_prices AS ab__product_prices ON ab__product_prices.product_id = products.product_id AND ab__product_prices.lower_limit = 1 AND ab__product_prices.usergroup_id IN (?n)', array_merge([USERGROUP_ALL], $usergroup_ids));
} elseif ($condition['condition'] === 'categories') {
$cats = explode(',', $condition['value']);
if ($condition['include_subcats'] === YesNo::YES) {
$_ids = db_get_fields('SELECT a.category_id
FROM ?:categories as a
LEFT JOIN ?:categories as b
ON b.category_id IN (?n)
WHERE a.id_path LIKE CONCAT(b.id_path, "/%")', $cats);
$cats = fn_array_merge($cats, $_ids, false);
}
$where .= db_quote(' ?p ab__products_categories.category_id ?p (?n)', $and_or, $operators[$conditions['set_value']][$condition['operator']], $cats);
$join['products_categories'] = 'LEFT JOIN ?:products_categories AS ab__products_categories ON ab__products_categories.product_id = products.product_id';
} elseif ($condition['condition'] === 'products') {
if (is_array($condition['value'])) {
$condition['value'] = implode(',', $condition['value']);
}
$where .= db_quote(' ?p products.product_id ?p (?p)', $and_or, $operators[$conditions['set_value']][$condition['operator']], $condition['value']);
} elseif ($condition['condition'] === 'feature' && !empty($condition['condition_element'])) {
$table_id = 'product_features_values_' . $condition['condition_element'];
$condition_query = fn_ab__stickers_build_feature_condition($table_id, $operators[$conditions['set_value']][$condition['operator']], $condition['condition_element'], $condition['value'] ?? null);
if (!empty($condition_query)) {
$where .= db_quote(' ?p ?p', $and_or, $condition_query);
$join[$table_id] = db_quote("LEFT JOIN ?:product_features_values AS {$table_id} ON {$table_id}.product_id = products.product_id AND {$table_id}.feature_id = ?i", $condition['condition_element']);
}
} elseif ($condition['condition'] === 'tags' && Registry::get('addons.tags.status') === ObjectStatuses::ACTIVE) {
$where .= db_quote(' ?p ab__tag_links.tag_id ?p ?i', $and_or, $operators[$conditions['set_value']][$condition['operator']], $condition['value']);
$join['tag_links'] = db_quote('LEFT JOIN ?:tag_links as ab__tag_links ON ab__tag_links.object_id = products.product_id AND object_type = "P"');
} elseif ($condition['condition'] === 'amount') {
$where .= db_quote(' ?p products.amount ?p ?i', $and_or, $operators[$conditions['set_value']][$condition['operator']], $condition['value']);
} elseif ($condition['condition'] === 'sales_amount' && Registry::get('addons.bestsellers.status') === ObjectStatuses::ACTIVE) {
$where .= db_quote(' ?p ab__product_sales.amount ?p ?i', $and_or, $operators[$conditions['set_value']][$condition['operator']], $condition['value']);
$join['product_sales'] = 'LEFT JOIN ?:product_sales as ab__product_sales ON ab__product_sales.product_id = products.product_id';
} elseif ($condition['condition'] === 'popularity') {
$where .= db_quote(' ?p ab__product_popularity.total ?p ?i', $and_or, $operators[$conditions['set_value']][$condition['operator']], $condition['value']);
$join['popularity'] = 'LEFT JOIN ?:product_popularity as ab__product_popularity ON ab__product_popularity.product_id = products.product_id';
} elseif ($condition['condition'] === 'comments') {
$where .= db_quote(' ?p 1', $and_or);
if (Registry::get('addons.product_reviews.status') === ObjectStatuses::ACTIVE) {
$where .= db_quote(' ?p ab__product_reviews.reviews_count ?p ?i', $and_or, $operators[$conditions['set_value']][$condition['operator']], $condition['value']);
$join['product_reviews_count'] = 'LEFT JOIN ?:product_review_prepared_data as ab__product_reviews ON ab__product_reviews.product_id = products.product_id';
} elseif (Registry::get('addons.discussion.status') === ObjectStatuses::ACTIVE) {
$having .= db_quote(' ?p COUNT(DISTINCT(ab__discussion_posts.post_id)) ?p ?i', $and_or, $operators[$conditions['set_value']][$condition['operator']], $condition['value']);
$join['comments_count'] = 'LEFT JOIN ?:discussion as ab__discussion ON ab__discussion.object_id = products.product_id AND ab__discussion.object_type = "' . DiscussionObjectTypes::PRODUCT
. '" LEFT JOIN ?:discussion_posts as ab__discussion_posts ON ab__discussion_posts.thread_id = ab__discussion.thread_id AND ab__discussion_posts.status = "A"';
}
} elseif ($condition['condition'] === 'weight') {
$where .= db_quote(' ?p products.weight ?p ?d', $and_or, $operators[$conditions['set_value']][$condition['operator']], fn_convert_weight($condition['value']));
} elseif ($condition['condition'] === 'creating_date') {
$date = new DateTime();
$date->setTime(0, 0);
$where .= db_quote(' ?p ?i ?p products.timestamp', $and_or, $date->getTimestamp() - (int) $condition['value'] * SECONDS_IN_DAY, $operators[$conditions['set_value']][$condition['operator']]);
} elseif ($condition['condition'] === 'age_verification') {
$where .= db_quote(' ?p (products.age_verification = ?s AND products.age_limit ?p ?i)', $and_or, YesNo::YES, $operators[$conditions['set_value']][$condition['operator']], $condition['value']);
} elseif ($condition['condition'] === 'free_shipping') {
$where .= db_quote(' ?p products.free_shipping ?p ?s', $and_or, $operators[$conditions['set_value']][$condition['operator']], YesNo::YES);
} elseif ($condition['condition'] === 'buy_together' && Registry::get('addons.buy_together.status') === ObjectStatuses::ACTIVE) {
$join['buy_together'] = db_quote('INNER JOIN ?:buy_together ON ?:buy_together.product_id = products.product_id');
$where .= ' AND 1';
} elseif ($condition['condition'] === 'company_id' && fn_allowed_for('MULTIVENDOR')) {
$company_condition = fn_get_company_condition('products.company_id', true, explode(',', $condition['value']), false, true);
switch ($condition['operator']) {
case 'nin':
$company_condition = preg_replace('/(products\.company_id)\s+(IN)/i', '$1 NOT IN', $company_condition);
break;
case 'in':
default:
break;
}
$where .= db_quote(' ?p', $company_condition);
}

fn_set_hook('ab__stickers_build_sticker_conditions_query_post', $conditions, $condition, $where, $join, $having, $operators, $and_or);
}
}
if ($where === '1') {
$where = '0';
}
if (strlen($having) === 1) {
$having = null;
}
return [$where, $join, $having];
}

function fn_ab__stickers_build_feature_condition($table_id, $operator, $feature_id, $value)
{
$feature_type = db_get_field('SELECT feature_type FROM ?:product_features WHERE feature_id = ?i', $feature_id);
$lang_code = Registry::ifGet('runtime.ab__stickers.cond_lang_code', CART_LANGUAGE);
$query = false;
if (in_array($operator, ['CONTAINS', 'NOT CONTAINS']) && $feature_type != 'T') {
$operator = $operator === 'CONTAINS' ? 'IN' : 'NOT IN';
}
if ($operator === 'IS NOT NULL') {
$query = db_quote("{$table_id}.variant_id IS NOT NULL");
} elseif ($operator === 'IS NULL') {
$query = db_quote("{$table_id}.variant_id IS NULL");
} elseif (in_array($feature_type, ['E', 'S', 'M', 'N'])) {
if ($operator === 'IN') {
$query = db_quote("{$table_id}.variant_id IN (?p)", $value);
} elseif ($operator === 'NOT IN') {
$query = db_quote("({$table_id}.variant_id NOT IN (?p) OR {$table_id}.variant_id IS NULL)", $value);
} elseif ($feature_type === 'N') {
$value_int = db_get_field('SELECT variant FROM ?:product_feature_variant_descriptions WHERE variant_id = ?i AND lang_code = ?s', $value, CART_LANGUAGE);
$query = db_quote("{$table_id}.value_int ?p ?d", $operator, $value_int);
} else {
$query = db_quote("{$table_id}.variant_id ?p ?i", $operator, $value);
}
} elseif (in_array($feature_type, ['C', 'T'])) {
if (in_array($operator, ['IN', 'NOT IN'])) {
$query = db_quote("{$table_id}.value ?p (?a)", $operator, explode(',', $value));
} elseif (in_array($operator, ['CONTAINS', 'NOT CONTAINS'])) {
$operator = $operator === 'CONTAINS' ? '<>' : '=';
$query = db_quote("INSTR({$table_id}.value, ?s) ?p 0 AND {$table_id}.lang_code = ?s", $value, $operator, $lang_code);
} else {
$query = db_quote("{$table_id}.value ?p ?s", $operator, $value);
}
} elseif (in_array($feature_type, ['O'])) {
if (in_array($operator, ['IN', 'NOT IN'])) {
$query = db_quote("{$table_id}.value ?p (?a)", $operator, explode(',', $value));
} else {
$query = db_quote("{$table_id}.value ?p ?d", $operator, $value);
}
}
return $query;
}
