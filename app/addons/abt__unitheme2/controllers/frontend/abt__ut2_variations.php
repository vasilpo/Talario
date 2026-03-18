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
use Tygh\BlockManager\Block;
use Tygh\BlockManager\RenderManager;
use Tygh\Enum\YesNo;
if (!defined('BOOTSTRAP')) { die('Access denied'); }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return;
}
if ($mode === 'render_product_card') {
if (empty($_REQUEST['block_key']) || empty($_REQUEST['variation_id'])) {
exit;
}
$object_key = $_REQUEST['block_key'];
$object_key = fn_decrypt_text($object_key);
$template_name = $_REQUEST['template_name'] ?? '';
list($block_id, $snapping_id) = explode(':', $object_key);
$block_id = (int) $block_id;
$snapping_id = (int) $snapping_id;
$block = Block::instance()->getById($block_id, $snapping_id);
if (($block['type'] ?? '') !== 'products') {
$block = [
'block_id' => 0,
'type' => 'products',
'name' => $block['name']??'default',
'properties' => [
'template' => fn_abt__ut2_get_product_templates()[$template_name] ?? 'products'
],
'company_id' => fn_get_runtime_company_id(),
'storefront_id' => \Tygh::$app['storefront']->storefront_id,
'lang_code' => CART_LANGUAGE,
'snapping_id' => $snapping_id,
'object_id' => 0,
'object_type' => '',
'availability' => [
'phone' => 1,
'tablet' => 1,
'desktop' => 1,
]
];
}
if ($block) {
$block = array_merge([
'grid_id' => 0,
'order' => 0,
'abt__ut2_variation' => YesNo::YES
], $block);
$block['content'] = [
'items' => [
'filling' => 'manually',
'item_ids' => $_REQUEST['variation_id']
]
];
if (!empty($_REQUEST['redirect_url'])) {

$smarty = Tygh::$app['view'];
$smarty->assign('redirect_url', $_REQUEST['redirect_url']);
}

$ajax = \Tygh::$app['ajax'];
$ajax->assign('product_card', RenderManager::renderBlock($block, [], 'C', ['use_cache' => false]));
}
exit;
}