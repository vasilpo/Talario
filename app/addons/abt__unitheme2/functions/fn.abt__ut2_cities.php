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

function fn_abt__unitheme2_get_cities_pre($params, $items_per_page, $lang_code, &$fields, $condition, &$join){
if(AREA === 'A'){
$join .= db_quote(' LEFT JOIN ?:abt__ut2_checkout_cities ON ?:abt__ut2_checkout_cities.city_id = ?:rus_cities.city_id');
$fields[] = '(?:abt__ut2_checkout_cities.city_id IS NOT NULL) as abt__ut2_use_for_checkout';
}
}

function fn_abt__ut2_update_cities($params)
{
$data = [
'status' => $params['status'] ?? 'N',
'city_id' => $params['city_id'],
'country_code' => $params['country_code'],
'state_code' => $params['state_code'],
];
if ($data['status'] === 'N') {
db_query('DELETE FROM ?:abt__ut2_checkout_cities WHERE city_id = ?i', $data['city_id']);
} else {
db_replace_into('abt__ut2_checkout_cities',$data);
}
}

function fn_abt__ut2_get_cities($lang_code = CART_LANGUAGE){
return db_get_hash_multi_array('SELECT
?:abt__ut2_checkout_cities.*,
?:rus_city_descriptions.city,
?:state_descriptions.state
FROM ?:abt__ut2_checkout_cities
LEFT JOIN ?:rus_city_descriptions ON ?:rus_city_descriptions.city_id = ?:abt__ut2_checkout_cities.city_id
AND ?:rus_city_descriptions.lang_code = ?s
LEFT JOIN ?:states ON ?:abt__ut2_checkout_cities.state_code = ?:states.code
AND ?:states.country_code = ?:abt__ut2_checkout_cities.country_code
LEFT JOIN ?:state_descriptions ON ?:states.state_id = ?:state_descriptions.state_id
AND ?:state_descriptions.lang_code = ?s
WHERE ?:rus_city_descriptions.city IS NOT NULL'
, ['country_code', 'city_id'], $lang_code,$lang_code);
}
