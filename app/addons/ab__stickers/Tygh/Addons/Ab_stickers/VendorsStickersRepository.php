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
namespace Tygh\Addons\Ab_stickers;
use mysqli_result;
use PDOStatement;
use Tygh\Database\Connection;
use Tygh\Enum\ObjectStatuses;
use Tygh\Enum\YesNo;
use Tygh\Exceptions\DatabaseException;
use Tygh\Storefront\Storefront;

class VendorsStickersRepository
{

public $settings = [];

protected $db;

protected $storefront;

protected $area;

protected $table = 'ab__vendor_stickers';

protected $main_table = 'ab__stickers';

protected $key = 'sticker_id';

protected $alt_key = 'company_id';

private $auth = [];

public function __construct($settings, Connection $db, Storefront $storefront, array $auth = [], $area = '')
{
$this->settings = $settings;
$this->db = $db;
$this->area = $area;
$this->auth = $auth;
$this->storefront = $storefront;
}

public function updateStatus($sticker_id, $company_id, $status = ObjectStatuses::ACTIVE)
{

fn_set_hook('ab__stickers_update_vendor_status_pre', $sticker_id, $company_id, $status);
return $this->db->replaceInto($this->table, [
'sticker_id' => $sticker_id,
'company_id' => $company_id,
'vendor_status' => $status,
]);
}

public function getStickersVendorData(array $sticker_ids)
{
$statuses = $this->db->getMultiHash("SELECT ?:{$this->table}.*
FROM ?:{$this->table}
LEFT JOIN ?:{$this->main_table} AS stickers ON stickers.sticker_id = ?:{$this->table}.sticker_id
WHERE stickers.sticker_id IN (?n)
AND stickers.vendor_edit = ?s", ['sticker_id', 'company_id'], $sticker_ids, YesNo::YES);
return $statuses;
}

public function deleteStickersVendorDataByStickerIds(array $sticker_ids)
{
return $this->db->query("DELETE FROM ?:{$this->table} WHERE {$this->key} IN (?n)", $sticker_ids);
}

public function deleteStickersVendorDataByCompanyIds(array $company_ids)
{
return $this->db->query("DELETE FROM ?:{$this->table} WHERE {$this->alt_key} IN (?n)", $company_ids);
}
}
