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
if ($mode === 'get_variants_list') {
if (empty($_REQUEST['feature_id']) && empty($_REQUEST['ids'])) {
return [CONTROLLER_STATUS_NO_PAGE];
}
$feature_id = isset($_REQUEST['feature_id']) ? (int)$_REQUEST['feature_id'] : null;
$page_number = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
$page_size = isset($_REQUEST['page_size']) ? (int)$_REQUEST['page_size'] : 10;
$search_query = isset($_REQUEST['q']) ? $_REQUEST['q'] : null;
$lang_code = isset($_REQUEST['lang_code']) ? $_REQUEST['lang_code'] : DESCR_SL;
$search = [
'page' => $page_number,
'feature_id' => $feature_id,
'search_query' => $search_query,
'get_images' => true
];
if (isset($_REQUEST['preselected'])) {
$search['variant_id'] = $_REQUEST['preselected'];
}
if (isset($_REQUEST['ids'])) {
$search['variant_id'] = (array)$_REQUEST['ids'];
$page_size = 0;
}
if (isset($_REQUEST['product_id'])) {
$search['product_id'] = (int)$_REQUEST['product_id'];
}
list($variants, $search) = fn_get_product_feature_variants($search, $page_size, $lang_code);
$objects = array_values(array_map(function ($feature_variant) {
$image_url = null;
if (isset($feature_variant['image_pair'])) {
$image_data = fn_image_to_display(
$feature_variant['image_pair'],
isset($_REQUEST['image_width']) ? (int)$_REQUEST['image_width'] : 50,
isset($_REQUEST['image_height']) ? (int)$_REQUEST['image_height'] : 50
);
if (!empty($image_data['image_path'])) {
$image_url = $image_data['image_path'];
}
}
return [
'id' => $feature_variant['variant_id'],
'text' => $feature_variant['variant'],
'image_url' => $image_url,
'data' => [
'image_url' => $image_url,
'name' => $feature_variant['variant'],
'color' => $feature_variant['color'],
'abt__ut2_color_style' => $feature_variant['abt__ut2_color_style'],
'abt__ut2_multicolor' => $feature_variant['abt__ut2_multicolor'],
]
];
}, $variants));
Tygh::$app['ajax']->assign('objects', $objects);
Tygh::$app['ajax']->assign('total_objects', isset($search['total_items']) ? $search['total_items'] : count($objects));
exit;
}