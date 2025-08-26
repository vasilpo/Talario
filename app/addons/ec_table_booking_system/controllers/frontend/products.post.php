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
use Tygh\Models\VendorPlan;

if (!defined('BOOTSTRAP')) {
    die('Access denied');
}
if ($mode == 'check_slots') {
    if (defined('AJAX_REQUEST')) {
        $_REQUEST['selected_date'] = str_replace('/', '-', $_REQUEST['selected_date']);
        $selected_date          = strtotime($_REQUEST['selected_date']);
        $selected_date_format   = fn_date_format($selected_date, "%Y-%m-%d");
        $all_slots              = fn_ec_table_booking_system_get_single_day_all_slots($selected_date_format, $_REQUEST['product_id']);
        if(!empty($all_slots['available_time_slots'])){
            Tygh::$app['view']->assign('available_time_slots', $all_slots['available_time_slots']);
        }
        if(isset($_REQUEST['selected_time'])){
            Tygh::$app['view']->assign('selected_time', $_REQUEST['selected_time']);
        }

        $check_quantity_selector = fn_ec_table_booking_system_check_quantity_selector($_REQUEST['product_id']);
        Tygh::$app['view']->assign('product_id', $_REQUEST['product_id']);
        Tygh::$app['view']->assign('check_quantity_selector', $check_quantity_selector);
        Tygh::$app['view']->assign('obj_id', $_REQUEST['product_id']);
        Tygh::$app['view']->assign('selected_date', $_REQUEST['selected_date']);
    }
}

if ($mode == 'set_price') {
    if (defined('AJAX_REQUEST')) {
        parse_str($_REQUEST['form_data'],$form_data);
        $product_data= $form_data['product_data'];
        if($_REQUEST['booking_type'] == 'T') {
            $dates =  str_replace('/', '-', $_REQUEST['selected_date']);
            $product_data[$key]['booking_amount'] = 1;
        }
        else {
            $dates_selected = explode("to",$_REQUEST['selected_date']);
            $dates = fn_ec_table_booking_get_between_dates($dates_selected[0],$dates_selected[1]);
            $product_data[$key]['booking_amount'] = count($dates);
            $dates = implode("|",$dates);
        }
        $price = fn_ec_table_booking_price_wise_product_price($dates,$_REQUEST['product_id']);
        if(isset($product_data[$_REQUEST['product_id']]['product_options']))
            $price = fn_apply_options_modifiers($product_data[$_REQUEST['product_id']]['product_options'], $price, 'P', array(), array('product_data' => $product_data[$key]));
        

        $product_data[$_REQUEST['product_id']]['price'] = $price;
        $form_data['product_data'] = $product_data;
        Tygh::$app['view']->assign('product', reset($product_data));
        Tygh::$app['view']->display('views/products/view.tpl');
        exit;
        
    }
}

if($mode == 'view') {
    if(!empty($_REQUEST['product_id'])) {
        $company_id = db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i",$_REQUEST['product_id']);
        $plan_data = VendorPlan::model()->find(array('company_id' => $company_id));
        if(!empty($plan_data))
        Tygh::$app['view']->assign('offer_booking', $plan_data->offer_booking);

    }
}
