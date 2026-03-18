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
defined('BOOTSTRAP') || die('Access denied');
use Tygh\Enum\Addons\ProductReviews\ProductReview\ProductReviewVoteValues;
use Tygh\Addons\ProductReviews\ServiceProvider as ProductReviewsProvider;
$auth = & Tygh::$app['session']['auth'];
$service = ProductReviewsProvider::getService();
if ($_SERVER['REQUEST_METHOD'] == "POST"){
if (
$mode === 'vote'
&& (
$action === 'up'
|| $action === 'down'
)
) {
$product_review_id = empty($_REQUEST['product_review_id']) ? 0 : $_REQUEST['product_review_id'];
$value = $action === 'up' ? ProductReviewVoteValues::VOTE_UP_VALUE : ProductReviewVoteValues::VOTE_DOWN_VALUE;
$vote_data = [
'product_review_id' => $product_review_id,
'user_id' => $auth['user_id'],
'value' => $value,
];
if (!empty($auth['ip'])) {
$vote_data['ip_address'] = $auth['ip'];
} else {
$ip = fn_get_ip();
$vote_data['ip_address'] = $ip['host'];
}
$vote_data['ip_address'] = bin2hex(inet_pton($vote_data['ip_address']));
$existed_vote = db_get_array('SELECT * FROM ?:product_review_votes WHERE ?w', $vote_data);
if (!empty($existed_vote)){
db_query('DELETE FROM ?:product_review_votes WHERE ?w', $vote_data);
$service->actualizeProductReviewHelpfulness((int) $vote_data['product_review_id']);
if (!empty($_REQUEST['return_url'])) {
return [CONTROLLER_STATUS_REDIRECT, $_REQUEST['return_url']];
}
}
}
return;
}