<?php
/** %cs-cart copyright% **/

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

function fn_rus_cities_addon_install()
{
    db_query("UPDATE ?:addon_descriptions SET name = CONCAT(name, ' [Deprecated]') WHERE addon = 'rus_cities'");
}

function fn_get_cities($params = array(), $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    return fn_cities_get_cities($params, $items_per_page, $lang_code);
}

function fn_update_city($city_data, $city_id = 0, $lang_code = DESCR_SL)
{
    return fn_cities_update_city($city_data, $city_id, $lang_code);
}

function fn_rus_cities_find_cities($params, $lang_code = CART_LANGUAGE, $items_per_page = 10)
{
    return fn_cities_find_cities($params, $lang_code, $items_per_page);
}

function fn_rus_cities_format_to_autocomplete($cities)
{
    return fn_cities_format_to_autocomplete($cities);
}

function fn_rus_cities_get_city_ids($city, $state, $country, $lang_code = CART_LANGUAGE)
{
    return fn_cities_get_city_ids($city, $state, $country, $lang_code);
}

function fn_rus_cities_read_cities_by_chunk($path, $size, $function_callback)
{
   return fn_cities_read_cities_by_chunk($path, $size, $function_callback);
}

function fn_rus_cities_add_cities_in_table($rows)
{
    fn_cities_add_cities_in_table($rows);
}

function fn_rus_cities_get_all_cities($rows)
{
    return fn_cities_get_all_cities($rows);
}

function fn_rus_cities_delete_city($city_id)
{
    fn_cities_delete_city($city_id);
}

function fn_rus_cities_get_location_from_session($stored_location = false, $customer_loc = true, $user_data = true)
{
    return fn_cities_get_location_from_session($stored_location, $customer_loc, $user_data);
}

function fn_rus_city_get_city_data($city_ids)
{
    return fn_cities_get_city_data($city_ids);
}

function fn_rus_cities_cities_geo_maps_set_customer_location_pre_post(&$location, $cities)
{
    fn_set_hook('rus_cities_geo_maps_set_customer_location_pre_post', $location, $cities);
}

function fn_rus_cities_cities_location_manager_detect_zipcode_post_post($country_code, $state_code, $city, &$zipcode)
{
    fn_set_hook('rus_cities_location_manager_detect_zipcode_post_post', $country_code, $state_code, $city, $zipcode);
}

function fn_rus_cities_cities_find_cities(&$params, &$lang_code, &$items_per_page, &$search, &$fields, &$join, &$condition)
{
    fn_set_hook('rus_cities_find_cities', $params, $lang_code, $items_per_page, $search, $fields, $join, $condition);
}

function fn_rus_cities_uninstall_addon_pre($addon_name, $show_message, $allow_unmanaged, &$execute_schema_queries)
{
    if ($addon_name !== 'rus_cities' || !Registry::get('addons.cities')) {
        return;
    }

    $execute_schema_queries = false;

    $langvars = [
        'add_city',
        'new_city',
        'select_city',
        'empty_state',
        'not_selected_state',
        'other_town',
        'new_city_state',
        'code_sdek',
        'addons.cities.label_cities_update',
        'addons.cities.cities_update',
        'addons.cities.text_update_cities',
        'addons.rus_cities.city_prefix'
    ];

    db_query('DROP TABLE IF EXISTS ?:rus_cities');
    db_query('DROP TABLE IF EXISTS ?:rus_city_descriptions');

    foreach ($langvars as $langvar) {
        db_query('DELETE FROM ?:language_values WHERE name = ?s', $langvar);

        if (fn_allowed_for('ULTIMATE')) {
            db_query('DELETE FROM ?:ult_language_values WHERE name = ?s', $langvar);
        }
    }
}
