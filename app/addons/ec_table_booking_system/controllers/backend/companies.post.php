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

if ($mode == 'update') {
    $plan_data = VendorPlan::model()->find(array('company_id' => $_REQUEST['company_id']));
    $offer_booking='';
    if(!empty($plan_data->offer_booking) && $plan_data->offer_booking) {
        $offer_booking = $plan_data->offer_booking;
        Registry::set(
            'navigation.tabs.ec_table_booking_system', array(
                'title' => __('ec_table_booking_system.default_ec_table_booking'),
                'js'    => true
            )
        );
    }
    Tygh::$app['view']->assign('offer_booking', $offer_booking);
}
