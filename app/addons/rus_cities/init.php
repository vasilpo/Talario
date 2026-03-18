<?php
/** %cs-cart copyright% **/

defined('BOOTSTRAP') or die('Access denied');

fn_register_hooks(
    'cities_geo_maps_set_customer_location_pre_post',
    'cities_location_manager_detect_zipcode_post_post',
    'cities_find_cities',
    'uninstall_addon_pre'
);
