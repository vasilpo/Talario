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
use Tygh\Addons\ProductReviews\ServiceProvider as ProductReviewsProvider;
defined('BOOTSTRAP') || die('Access denied');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}
if ($mode == 'get_product_reviews_gallery_popup') {
$params = $_REQUEST;
$images_per_page = 30;
$page = $params['page'] ?? 1;
$product_review_id = $params['product_review_id'] ?? null;
if (empty($params['product_id'])) {
return;
}
$search_params = [
'product_id' => $params['product_id'],
'status' => ObjectStatuses::ACTIVE,
'abt__ut2_reviews_period' => $_REQUEST['abt__ut2_reviews_period'] ?? '',
'abt__ut2_reviews_rating' => $_REQUEST['abt__ut2_reviews_rating'] ?? '',
'only_buyers' => $_REQUEST['only_buyers'] ?? '',
'storefront_id' => fn_product_reviews_get_storefront_id_by_setting(),
'with_images' => $_REQUEST['with_images'] ?? true,
'sort_by' => $params['sort_by'] ?? "product_review_timestamp",
'sort_order' => $params['sort_order'] ?? "desc"
];
$product_reviews_repository = ProductReviewsProvider::getProductReviewRepository();
list($product_reviews, $search) = $product_reviews_repository->find($search_params);
$total_images = 0;
$product_review = [];
$reviews_images = [];
if (!empty($product_review_id)) {
foreach ($product_reviews as $review) {
if ($review['product_review_id'] == $product_review_id) {
$product_review = $review;
}
$review_id = $review['product_review_id'];
$reviews_images[$review_id] = $review['images'];
}
} else {
foreach ($product_reviews as $review) {
$total_images += count($review['images']);
}
$img_counter = $images_per_page;
$result_reviews = [];
foreach ($product_reviews as $k => $review) {
if ($img_counter == 0) {
break;
}
foreach ($review['images'] as $i => $img) {
if ($img_counter == 0) {
unset($review['images'][$i]);
continue;
}
$img_counter--;
}
$result_reviews[$k] = $review;
}
$search['total_items'] = $total_images;
}
$view = Tygh::$app['view'];
if (!empty($product_review_id)) {
$view->assign('product_review_id', $product_review_id)
->assign('product_review', $product_review)
->assign('reviews_images', $reviews_images);
} else {
$view->assign('product_reviews', $result_reviews);
if ($total_images > $images_per_page) {
$view->assign('next_page', (int)$page + 1);
}
}
$view->assign('product_id', $params['product_id'])
->assign('search', $search)
->assign('product_reviews_sorting', $product_reviews_repository->getSorting())
->assign('product_reviews_sorting_orders', ['asc', 'desc'])
->assign('product_reviews_avail_sorting', $product_reviews_repository->getAvailableSorts())
->display('addons/product_reviews/views/product_reviews/components/product_reviews_gallery_popup.tpl');
} elseif ($mode == 'get_product_reviews_gallery_popup_page') {
$params = $_REQUEST;
$images_per_page = 30;
$page = $params['page'] ?? 1;
$search_params = [
'product_id' => $params['product_id'],
'status' => ObjectStatuses::ACTIVE,
'abt__ut2_reviews_period' => $_REQUEST['abt__ut2_reviews_period'] ?? '',
'abt__ut2_reviews_rating' => $_REQUEST['abt__ut2_reviews_rating'] ?? '',
'only_buyers' => $_REQUEST['only_buyers'] ?? '',
'storefront_id' => fn_product_reviews_get_storefront_id_by_setting(),
'with_images' => true
];
$product_reviews_repository = ProductReviewsProvider::getProductReviewRepository();
list($product_reviews, $search) = $product_reviews_repository->find($search_params);
$result_reviews = [];
$offset = $images_per_page * ($page - 1);
$next_page = false;
$current_offset = 0;
foreach ($product_reviews as $k => $review) {
$current_offset += count($review['images']);
if ($current_offset <= $offset) {
continue;
} else {
if ($current_offset < $offset + $images_per_page) {
if ($offset - ($current_offset - count($review['images'])) > 0) {
$delete_count = count($review['images']) - ($current_offset - $offset);
for ($i = 0; $i < $delete_count; $i++) {
unset($review['images'][array_key_first($review['images'])]);
}
}
$result_reviews[$k] = $review;
} elseif ($current_offset > $offset + $images_per_page) {
$delete_count = $current_offset - ($offset + $images_per_page);
for ($i = 0; $i < $delete_count; $i++) {
unset($review['images'][array_key_last($review['images'])]);
}
$result_reviews[$k] = $review;
$next_page = true;
break;
}
}
if ($review == $product_reviews[array_key_last($product_reviews)]) {
$next_page = false;
}
}
$view = Tygh::$app['view'];
$html = $view->assign('product_id', $params['product_id'])
->assign('product_reviews', $result_reviews)
->assign('search', $search)
->fetch('addons/product_reviews/views/product_reviews/components/post_popup_images.tpl');
Tygh::$app['ajax']->assign('images', $html);
if ($next_page) {
Tygh::$app['ajax']->assign('next_page', (int)$page + 1);
}
return [CONTROLLER_STATUS_NO_CONTENT];
}