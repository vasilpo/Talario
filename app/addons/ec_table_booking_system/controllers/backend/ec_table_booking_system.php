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
use Tygh\Registry;
if (!defined('BOOTSTRAP')) {
    die('Access denied');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($mode == 'update_status') {
        fn_tools_update_status($_REQUEST);
        if (empty($_REQUEST['redirect_url'])) {
            exit;
        }
    }
    if($mode == 'update_slots_amount') {
        fn_ec_save_booking_data_by_amount($_REQUEST);
    }
}
if ($mode == 'booked_orders') {
        $status     = 'all';
        $params     = $_REQUEST;
        list($booking_info, $search)    = fn_ec_table_booking_system_get_booked_information(0, '', $status, $params);
        $booking_items_status           = fn_ec_table_booking_system_get_booking_status_params();
        Registry::get('view')->assign('search', $search);
        Registry::get('view')->assign('booking_info', $booking_info);
        Registry::get('view')->assign('booking_items_status', $booking_items_status);
}
if($mode == 'get_slots') {
    if(isset($_REQUEST['day']) && !empty($_REQUEST['day'])) {
        $available_slots  = fn_ec_table_booking_system_get_all_slots_by_amount($_REQUEST);
        $saved_data = fn_ec_table_booking_system_get_saved_data($_REQUEST);
        if(!empty($saved_data)) {
            Tygh::$app['view']->assign('saved_data', $saved_data);
        }
        if($available_slots) {
            Tygh::$app['view']->assign('available_slots', $available_slots);
            Tygh::$app['view']->assign('day', $_REQUEST['day']);
            if(!empty($_REQUEST['product_id']))
                Tygh::$app['view']->assign('product_id', $_REQUEST['product_id']);
            elseif(!empty($_REQUEST['company_id']))
                Tygh::$app['view']->assign('company_id', $_REQUEST['company_id']);
        }
    }
}
