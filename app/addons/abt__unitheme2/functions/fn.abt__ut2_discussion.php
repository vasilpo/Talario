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
use Tygh\Registry;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
function fn_abt__unitheme2_get_discussion_posts(&$params, $items_per_page, &$fields, &$join, &$condition, $order_by, $limit)
{
if (strpos($join, 'discussion_rating') !== false) {
$data = db_get_array(
"SELECT ?:discussion_rating.rating_value, COUNT(?:discussion_rating.post_id) as qty FROM ?:discussion_posts $join "
. "WHERE $condition GROUP BY ?:discussion_rating.rating_value"
);
$params['posts_rating_count'] = [
'5' => 0,
'4' => 0,
'3' => 0,
'2' => 0,
'1' => 0,
];
foreach ($data as $row) {
$params['posts_rating_count'][$row['rating_value']] = $row['qty'];
}
if (!empty($params['abt__rating'])) {
$condition .= db_quote(' AND ?:discussion_rating.rating_value = ?i', $params['abt__rating']);
$params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:discussion_posts $join WHERE $condition");
}
}
if (Registry::get('settings.abt__ut2.addons.discussion.highlight_administrator') === 'Y') {
$fields .= ', ?:users.user_type';
$join .= ' LEFT JOIN ?:users ON ?:users.user_id = ?:discussion_posts.user_id';
}
}
function fn_abt__unitheme2_get_discussion_posts_post($params, $items_per_page, &$posts)
{
if (!empty($params['product_id']) && !empty($posts) && Registry::get('settings.abt__ut2.addons.discussion.verified_buyer') === 'Y') {
$user_ids = [];
foreach ($posts as $post) {
if ($post['user_id'] > 0) {
$user_ids[$post['user_id']] = $post['user_id'];
}
}
$buyers = db_get_hash_single_array(
'SELECT o.user_id FROM ?:orders AS o'
.' INNER JOIN ?:order_details AS od ON o.order_id = od.order_id AND od.product_id = ?i'
.' WHERE o.user_id IN (?n) AND o.status IN (?a)'
, ['user_id', 'user_id'], $params['product_id'], $user_ids, fn_get_settled_order_statuses()
);
foreach ($posts as $key => $post) {
if (isset($buyers[$post['user_id']])) {
$posts[$key]['abt__is_buyer'] = true;
}
}
}
}
