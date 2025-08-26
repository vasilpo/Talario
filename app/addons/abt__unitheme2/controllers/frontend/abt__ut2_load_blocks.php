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
use Tygh\BlockManager\Block;
use Tygh\BlockManager\RenderManager;
if (!defined('BOOTSTRAP')) {
die('Access denied');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
return [CONTROLLER_STATUS_OK];
}
if ($mode == 'get_block_content') {
if (empty($_REQUEST['block_id']) || empty($_REQUEST['current_page'])) {
return [CONTROLLER_STATUS_DENIED];
}
if (!defined('AJAX_REQUEST')) {
return [CONTROLLER_STATUS_NO_CONTENT];
}

if (!empty($_REQUEST['request_params'])) {
$request_params = json_decode(fn_abt__ut2_fix_json(fn_abt__ut2_urlsafe_text_decrypt($_REQUEST['request_params'])),true);
$_REQUEST = array_merge($_REQUEST, $request_params);
}
if(!empty($_REQUEST['abt__ut2_assign_data'])){
$assign = json_decode(fn_abt__ut2_fix_json(fn_abt__ut2_urlsafe_text_decrypt($_REQUEST['abt__ut2_assign_data'])), true);
Tygh::$app['view']->assign($assign);
if(isset($assign['config'])){
$config = Registry::get('config');
$config = array_merge($config, $assign['config']);
Registry::set('config', $config);
}
}
$block_instance = Block::instance(0, [], Tygh::$app['storefront']->storefront_id);
$block = $block_instance->getById($_REQUEST['block_id']);
$page = ++$_REQUEST['current_page'];
$block['content']['items']['page'] = $page;
$block['grid_id'] = $_REQUEST['grid_id'];
$block['snapping_id'] = $_REQUEST['snapping_id'];
$block['order'] = 1;
$block_content = RenderManager::renderBlockContent($block, [
'use_cache' => false,
]);

$ajax = Tygh::$app['ajax'];
$key = 'ut2_load_more_block_' . $_REQUEST['block_id'] . '_' . $_REQUEST['snapping_id'] . '_' . $page;
$ajax->assignHtml($key, $block_content);
$ajax->assign('key', $key);
return [CONTROLLER_STATUS_NO_CONTENT];
}
