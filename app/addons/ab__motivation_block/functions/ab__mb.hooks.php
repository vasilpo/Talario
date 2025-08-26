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
if (!defined('BOOTSTRAP')) {
die('Access denied');
}

function fn_vendor_debt_payout_ab__mb_get_motivation_items_before_select($params, $fields, $join, &$condition, $group_by, $lang_code)
{
if (!empty($params['product_id']) && $params['product_id'] == fn_vendor_debt_payout_get_payout_product()) {
$condition .= ' AND 0';
}
}

function fn_ab__motivation_block_chown_company($from, $to, &$excluded_tables, $tables)
{
$items_for_delete = db_get_array("SELECT ab__mb_vd.motivation_item_id, ab__mb_vd.company_id, ab__mb_vd.lang_code FROM ?:ab__mb_vendors_descriptions ab__mb_vd LEFT JOIN ?:ab__mb_vendors_descriptions ab__mb_vd2 ON ab__mb_vd.motivation_item_id = ab__mb_vd2.motivation_item_id AND ab__mb_vd.lang_code = ab__mb_vd2.lang_code WHERE ab__mb_vd.company_id = ?i AND ab__mb_vd2.company_id = ?i", $from, $to);
foreach ($items_for_delete as $item) {
db_query("DELETE FROM ?:ab__mb_vendors_descriptions WHERE motivation_item_id = ?i AND company_id = ?i AND lang_code = ?s", $item['motivation_item_id'], $item['company_id'], $item['lang_code']);
}
}