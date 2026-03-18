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
use Tygh\Enum\ObjectStatuses;
use Tygh\Registry;
use Tygh\Addons\ProductReviews\ServiceProvider as ProductReviewsProvider;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return [];
}
if ($mode == 'view') {
if (Registry::get('addons.social_buttons.status') == 'A') {
$social_buttons_settings = Registry::get('settings.social_buttons');
if (!empty($social_buttons_settings) && is_array($social_buttons_settings)) {
$provider_settings = Tygh::$app['view']->getTemplateVars('provider_settings');
foreach ($social_buttons_settings as $provider => $settings) {
$provider_display = $provider . '_display_on';
if (empty($settings[$provider_display]['products'])) {
unset($provider_settings[$provider]);
}
}
Tygh::$app['view']->assign('provider_settings', $provider_settings);
}
}
if (Registry::get('addons.product_reviews.status') == 'A') {
$product = Tygh::$app['view']->getTemplateVars('product');
$product_reviews_repository = ProductReviewsProvider::getProductReviewRepository();
$search_params = [
'product_id' => $product['product_id'],
'status' => ObjectStatuses::ACTIVE,
'abt__ut2_reviews_period' => $_REQUEST['abt__ut2_reviews_period'] ?? '',
'abt__ut2_reviews_rating' => $_REQUEST['abt__ut2_reviews_rating'] ?? '',
'only_buyers' => $_REQUEST['only_buyers'] ?? '',
'with_images' => $_REQUEST['with_images'] ?? false,
'storefront_id' => fn_product_reviews_get_storefront_id_by_setting(),
];
list($product_reviews, $search) = $product_reviews_repository->find($search_params);
$reviews_gallery = [];
foreach ($product_reviews as $review) {
$images = $review['images'];
if (!empty($images)) {
$k=0;
foreach ($images as &$image) {
$image['product_review_id'] = $review['product_review_id'];
$image['product_review_rating'] = $review['rating_value'];
$image['index'] = $k++;
}
}
$reviews_gallery = array_merge($reviews_gallery, $images);
if (count($reviews_gallery) > 18) {
break;
}
}
$total_reviews_count = count($product_reviews);
$most_helpful_reviews = [];
if ($product['product_reviews_count'] >= 5){
$most_helpful_reviews = [
'positive' => [],
'negative' => []
];
$most_helpful_search_params = [
'product_id' => $product['product_id'],
'status' => ObjectStatuses::ACTIVE,
'abt__ut2_reviews_period' => $_REQUEST['abt__ut2_reviews_period'] ?? '',
'storefront_id' => fn_product_reviews_get_storefront_id_by_setting(),
];
$most_helpful_search_params = array_merge($most_helpful_search_params, [
'most_helpful' => true,
'rating_type' => 'negative'
]);
list($product_reviews,) = $product_reviews_repository->find($most_helpful_search_params);
if (!empty($product_reviews)){
$most_helpful_reviews['negative'] = reset($product_reviews);
}
$most_helpful_search_params['rating_type'] = 'positive';
list($product_reviews,) = $product_reviews_repository->find($most_helpful_search_params);
if (!empty($product_reviews)){
$most_helpful_reviews['positive'] = reset($product_reviews);
}
}
Tygh::$app['view']->assign('abt__ut2_product_reviews_gallery', $reviews_gallery);
Tygh::$app['view']->assign('abt__ut2_product_reviews_count', $total_reviews_count);
Tygh::$app['view']->assign('abt__ut2_product_reviews_most_helpful', $most_helpful_reviews);
}
}
