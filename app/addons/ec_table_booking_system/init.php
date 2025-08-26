<?php
/**
 * CS-Cart Table Booking System - ec_table_booking_system
 * 
 * PHP version 7.1
 * 
 * @category  Add-on
 * @package   CS_Cart
 * @author    Ecarter Technologies Private Limited <support@ecarter.co>
 * @copyright 2021 Ecarter Technologies Private Limited
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
if (!defined('AREA')) {
    die('Access denied');
}
fn_register_hooks(
    'update_product_pre',
    'get_product_data',
    'get_cart_product_data',
    'get_product_data_post',
    'get_products',
    'pre_add_to_cart',
    'order_placement_routines',
    'reorder',
    'calculate_cart',
    'tools_change_status',
    'get_my_product_data',
    'update_product_post',
    'change_order_status',
    'pre_place_order',
    'get_product_filter_fields',
    'check_amount_in_stock',
    'update_company_pre',
    'get_company_data_post',
    'get_current_filters_post',
    'generate_filter_field_params',
    'get_additional_information'
);
