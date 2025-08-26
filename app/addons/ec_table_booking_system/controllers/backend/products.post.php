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
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 

}
if ($mode == 'update') {

    if(!empty($_REQUEST['product_id'])) {
        $company_id = db_get_field("SELECT company_id FROM ?:products WHERE product_id = ?i",$_REQUEST['product_id']);
        $plan_data = VendorPlan::model()->find(array('company_id' => $company_id));
        if(!empty($plan_data->offer_booking) && $plan_data->offer_booking) {
            $offer_booking = $plan_data->offer_booking;
            Registry::set(
                'navigation.tabs.ec_table_booking_system', array(
                    'title' => __('ec_table_booking_system.ec_table_booking'),
                    'js'    => true
                )
            );
            
        }

        if($auth['user_type'] == 'A') {
            $offer_booking = 'Y';
            Registry::set(
                'navigation.tabs.ec_table_booking_system', array(
                    'title' => __('ec_table_booking_system.ec_table_booking'),
                    'js'    => true
                )
            );
        }
        Tygh::$app['view']->assign('offer_booking', $offer_booking);

    }
}
