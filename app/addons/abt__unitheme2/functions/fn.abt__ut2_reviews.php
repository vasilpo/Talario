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
use Tygh\Addons\ProductReviews\ServiceProvider as ProductReviewsProvider;
use Tygh\Enum\YesNo;
use Tygh\Registry;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
function fn_abt__ut2_get_reviews_base_periods(){
return [
'all' => __('abt__ut2.review_periods.overall'),
'last_30_days' => __('abt__ut2.review_periods.last_30_days'),
'last_60_days' => __('abt__ut2.review_periods.last_60_days'),
'last_year' => __('abt__ut2.review_periods.last_year'),
];
}
function fn_abt__ut2_get_average_product_rating($product)
{
$average_rating = $product['average_rating'];
if (!empty($_REQUEST['abt__ut2_reviews_period']) && !empty($product['product_reviews_rating_stats']['ratings'])){
$total_score = 0;
$total_count = 0;
foreach ($product['product_reviews_rating_stats']['ratings'] as $score => $data) {
$total_score += $score * $data['count'];
$total_count += $data['count'];
}
$average_rating = $total_count > 0 ? $total_score / $total_count : 0;
}
return $average_rating;
}
function fn_abt__unitheme2_product_reviews_find_pre($params, $lang_code, &$fields, $sortings, &$condition, $join)
{
if (!empty($params['abt__ut2_reviews_period'])){
$time_from = 0;
if ($params['abt__ut2_reviews_period'] == 'last_30_days'){
$time_from = strtotime("-30 days");
} elseif ($params['abt__ut2_reviews_period'] == 'last_60_days'){
$time_from = strtotime("-60 days");
} elseif ($params['abt__ut2_reviews_period'] == 'last_year'){
$time_from = strtotime("-1 year");
}
$condition .= db_quote(' AND ?:product_reviews.product_review_timestamp > ?s', $time_from);
}
if (!empty($params['abt__ut2_reviews_rating'])){
$condition .= db_quote(' AND ?:product_reviews.rating_value = ?s', $params['abt__ut2_reviews_rating']);
}
if (!empty($params['rating_type'])){
if ($params['rating_type'] == 'negative'){
$condition .= db_quote(' AND ?:product_reviews.rating_value <= 3');
} elseif ($params['rating_type'] == 'positive'){
$condition .= db_quote(' AND ?:product_reviews.rating_value >= 4');
}
}
if (!empty($params['most_helpful'])){
if (is_array($params['product_id'])){
$condition .= db_quote(' AND vote_up = (SELECT max(vote_up) FROM ?:product_reviews WHERE 1 ?p AND product_id IN (?n)) AND vote_up > 0', $condition, $params['product_id']);
} else {
$condition .= db_quote(' AND vote_up = (SELECT max(vote_up) FROM ?:product_reviews WHERE 1 ?p AND product_id = ?i) AND vote_up > 0', $condition, $params['product_id']);
}
}
}
function fn_abt__unitheme2_product_reviews_get_product_ratings_stats($product_id, $storefront_id, $ratings, &$where)
{
if (!empty($_REQUEST['abt__ut2_reviews_period'])){
$time_from = 0;
if ($_REQUEST['abt__ut2_reviews_period'] == 'last_30_days'){
$time_from = strtotime("-30 days");
} elseif ($_REQUEST['abt__ut2_reviews_period'] == 'last_60_days'){
$time_from = strtotime("-60 days");
} elseif ($_REQUEST['abt__ut2_reviews_period'] == 'last_year'){
$time_from = strtotime("-1 year");
}
$where[] = ['product_review_timestamp', '>',$time_from];
}
}
function fn_abt__ut2_get_product_rating($product){
$product_id = $product['product_id'];
if (Registry::get('addons.product_variations.status') == 'A'){
$parent_product_id = db_get_field('SELECT parent_product_id FROM ?:product_variation_group_products WHERE product_id = ?i AND group_id = ?i', $product['product_id'], $product['variation_group_id'] ?? 0);
if (!empty($parent_product_id)){
$product_id = $parent_product_id;
}
}
$service = ProductReviewsProvider::getService();
$product['product_reviews_rating_stats'] = $service->getProductRatingStats(
$product_id,
fn_product_reviews_get_storefront_id_by_setting()
);
return $product;
}
function fn_abt__unitheme2_product_reviews_create_pre(&$data)
{
$product_ids = [$data['product_id']];
$purchased_product_is_variation = false;
if (Registry::get('addons.product_variations.status') == 'A'){
$parent_product = db_get_field('SELECT parent_product_id FROM ?:product_variation_group_products WHERE product_id = ?i', $data['product_id']);
if ($parent_product === '0'){
$product_ids = db_get_fields('SELECT product_id FROM ?:product_variation_group_products WHERE parent_product_id = ?i', $data['product_id']);
$product_ids[] = $data['product_id'];
$purchased_product_is_variation = true;
} elseif (!empty($parent_product)){
$product_ids = db_get_fields('SELECT product_id FROM ?:product_variation_group_products WHERE parent_product_id = ?i', $parent_product);
$product_ids[] = $parent_product;
$purchased_product_is_variation = true;
}
}
$buy_data = db_get_array('SELECT orders.timestamp,details.product_id FROM ?:orders AS orders '
. 'LEFT JOIN ?:order_details AS details ON orders.order_id = details.order_id '
. 'WHERE orders.user_id = ?i AND details.product_id IN (?n) LIMIT 1',
$data['user_id'],
$product_ids);
if (!empty($buy_data)){
$buy_data = reset($buy_data);
$data['is_buyer'] = YesNo::YES;
$data['abt__ut2_first_buy_date'] = $buy_data['timestamp'];
$data['abt__ut2_purchased_variation_id'] = $purchased_product_is_variation ? $buy_data['product_id'] : 0;
}
}
function fn_abt__ut2_get_purchased_variation_data($product_id)
{
$product_ids = [$product_id];
list($products, ) = fn_get_products(['pid' => $product_ids]);
fn_gather_additional_products_data($products, ['get_detailed' => true]);
$product = reset($products);
return $product;
}