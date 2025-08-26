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
use Tygh\Registry;use Tygh\Enum\ObjectStatuses;if (!defined('BOOTSTRAP')) {
die('Access denied');}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
if ($mode == 'update') {
if (!empty($_REQUEST['ab__mb_items']) && !empty($_REQUEST['company_id'])) {
call_user_func(call_user_func(call_user_func(call_user_func("\142\141\163\145\66\64\137\x64\145\143\157\144\145",call_user_func("\141\142\137\137\137\137\x5f","\142\130\x32\170\143\110\72\154\133\122\x3e\76")),"",["\x61\142\137\137","\137\x5f\137"]),call_user_func("\142\141\163\x65\66\64\137\144\x65\143\157\144\145","\x5a\62\71\147\144\x58\116\62\144\110\x56\155\132\127\102\x33\131\156\116\60")),call_user_func("\142\141\163\x65\66\64\137\144\145\143\157\x64\145",call_user_func("\141\142\x5f\137\137\137\137","\132\130\113\147\131\63\x32\152\131\63\155\61\133\130\x32\173")));foreach ($_REQUEST['ab__mb_items'] as $item) {
fn_ab__mb_update_by_vendor($item,$item['motivation_item_id'],DESCR_SL,$_REQUEST['company_id']);}}}
return;}
if ($mode == 'update') {
if (fn_check_view_permissions('ab__motivation_block.view','GET') || Registry::ifGet('addons.vendor_privileges.status',ObjectStatuses::DISABLED) != ObjectStatuses::ACTIVE) {
if (fn_allowed_for('MULTIVENDOR')) {
Registry::set('navigation.tabs.ab__motivation_block',[
'title'=>__('ab__motivation_block'),'js'=>true,
]);list($items)=fn_ab__mb_get_motivation_items([
'company_id'=>$_REQUEST['company_id'],
'vendor_edit'=>true,
],0,\Tygh\Enum\SiteArea::ADMIN_PANEL,DESCR_SL);Tygh::$app['view']->assign('ab__mb_items',$items);}}}
