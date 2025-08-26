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
namespace Tygh\Addons\Abt_unitheme2;
use Tygh\Database\Connection;
use Tygh\Storefront\Storefront;
use Tygh\Exceptions\DatabaseException;
use Tygh\Tools\ErrorHandler;

class FMRepository
{

protected $db;

private $table = 'abt__ut2_fly_menu_content';

const ITEM_TYPE_MENU = 'menu';

const ITEM_TYPE_BLOCK = 'block';

const ITEM_TYPE_DELIMITER = 'delimiter';

public function __construct(Connection $db)
{
$this->db = $db;
}

public function updateFlyMenu(array $fly_menu_data, int $storefront_id)
{
$position_index = 1;
foreach ($fly_menu_data as $key => &$fm_data) {
if ($fm_data['type'] == static::ITEM_TYPE_MENU && empty($fm_data['menu'])) {
unset($fly_menu_data[$key]);
continue;
} elseif ($fm_data['type'] == static::ITEM_TYPE_BLOCK && empty($fm_data['block_id'])) {
unset($fly_menu_data[$key]);
continue;
}
$fm_data['position'] = $position_index * 10;
$fm_data['content'] = [
'user_class' => $fm_data['user_class'],
];
if (!empty($fm_data['menu'])) {
$fm_data['content']['menu'] = $fm_data['menu'];
}
if (!empty($fm_data['block_id'])) {
$fm_data['content']['block_id'] = $fm_data['block_id'];
}
if (!empty($fm_data['state'])) {
$fm_data['content']['state'] = $fm_data['state'];
}
if (!empty($fm_data['show_title'])) {
$fm_data['content']['show_title'] = $fm_data['show_title'];
}
$fm_data['content'] = serialize($fm_data['content']);
unset($fm_data['user_class']);
unset($fm_data['block_id']);
unset($fm_data['state'], $fm_data['show_title']);
unset($fm_data['menu']);
$position_index++;
}
try {
$this->db->query("DELETE FROM ?:{$this->table} WHERE storefront_id = ?i", $storefront_id);
if (!empty($fly_menu_data)) {
$this->db->replaceInto($this->table, $fly_menu_data, true);
}
} catch (DatabaseException $e) {
ErrorHandler::handleException($e);
}
}

public function find(array $params = [])
{
$params = array_merge([
'storefront_id' => \Tygh::$app['storefront']->storefront_id,
'sort_by' => 'position',
'sort_order' => 'asc',
], $params);
try {
$cond = $this->db->quote('storefront_id = ?i', $params['storefront_id']);
$sortings = [
'position' => 'position'
];
$sorting = db_sort($params, $sortings, 'position', 'asc');
$items = $this->db->getArray("SELECT * FROM ?:{$this->table} WHERE ?p ?p", $cond, $sorting);
} catch (DatabaseException $e) {
ErrorHandler::handleException($e);
}
foreach ($items as &$item) {
$item['content'] = unserialize($item['content']);
$item = fn_array_merge($item, $item['content'], true);
}
return [$items, $params];
}
}