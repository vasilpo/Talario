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
if (!defined('BOOTSTRAP')) {
    die('Access denied');
}
use Tygh\Registry;
use Tygh\Settings;
use Tygh\Providers\EventDispatcherProvider;
use Tygh\Notifications\EventIdProviders\OrderProvider;
use Tygh\Addons\EcTableBookingSystem\Enum\FilterTypes;

/**
 * Hook update_product_pre used for pre Updation of product data
 *
 * @param mixed $product_data
 * @param mixed $product_id
 * @param mixed $lang_code
 * @param mixed $can_update
 *
 * @return void
 */
function Fn_Ec_Table_Booking_System_Update_Product_pre(&$product_data, &$product_id, $lang_code, $can_update)
{
    if (isset($product_data['booking_data']) && !empty($product_data['booking_data'])) {
        $is_booking_type_available = db_get_field("SELECT booking_type FROM ?:ec_table_booking_system WHERE product_id = ?i",$product_id);
        if(!empty($is_booking_type_available) && $is_booking_type_available == 'Y' && $product_data['booking_data']['booking_type'] == 'N') {
            $product_data['is_edp']='N';
        }
       else if ($product_data['booking_data']['booking_type'] != 'N' && $product_data['booking_data']['booking_type'] != '1T') {
            $product_data['tracking'] = 'D';
            $product_data['is_edp']='Y';
        }
    }
}
function Fn_Ec_Table_Booking_System_Check_Amount_In_Stock($product_id, $amount, $product_options, $cart_id, &$is_edp, $original_amount, $cart){
    if ($is_edp == 'Y' && (db_get_field("SELECT booking_type FROM ?:ec_table_booking_system WHERE product_id = ?i", $product_id) == 'T' || db_get_field("SELECT booking_type FROM ?:ec_table_booking_system WHERE product_id = ?i", $product_id) == 'R')){
        $is_edp = 'N';
    }
}
/**
 * Hook update_product_post is used for post Updation of product data
 *
 * @param mixed $product_data
 * @param mixed $product_id
 * @param mixed $lang_code
 * @param mixed $create
 *
 * @return void
 */
function Fn_Ec_Table_Booking_System_Update_Product_post(&$product_data, &$product_id, &$lang_code, &$create)
{
    if (isset($product_data['booking_data']) && !empty($product_data['booking_data'])) {
        fn_ec_table_booking_system_update_booking_data($product_data['booking_data'], $product_id);
    }
}
function Fn_Ec_Table_Booking_System_Get_products($params, &$fields, $sortings, &$condition, &$join, $sorting, $group_by, $lang_code, $having)
{
    $fields['booking_type'] = "?:ec_table_booking_system.booking_type as booking_type";
    $join .= db_quote(" LEFT JOIN ?:ec_table_booking_system ON ?:ec_table_booking_system.product_id = products.product_id ");
    if(!empty($params['ec_table_booking_type'])){
        $condition .= db_quote(" AND ?:ec_table_booking_system.booking_type IN (?a)", array('T','R'));
    }

    if(!empty($params['search_booking_product']) && $params['search_booking_product'] == 'Y') {
        $condition .= db_quote(" AND ?:ec_table_booking_system.product_id != ?i", '');
    }

    if(isset($params['ec_date']) && !empty($params['ec_date'])) {
        $condition .= db_quote(" AND ?:ec_table_booking_system.booking_type != ?s", '');
        $condition .= db_quote(" AND ?:ec_table_booking_system.from_date <= ?s AND ?:ec_table_booking_system.to_date >= ?s AND ?:ec_table_booking_system.to_date >= ?s",$params['ec_date'][0],$params['ec_date'][0],$params['ec_date'][1]);
    }
}
/**
 * Using join condition and field_list data to get product data
 *
 * @param mixed $product_id
 * @param mixed $field_list
 * @param mixed $join
 * @param mixed $auth
 *
 * @return void
 */
function Fn_Ec_Table_Booking_System_Get_Product_data(&$product_id, &$field_list, &$join, &$auth)
{
    $field_list .= ", ?:ec_table_booking_system.booking_type as booking_type";
    $field_list .= ", ?:ec_table_booking_system.from_date as from_date";
    $field_list .= ", ?:ec_table_booking_system.to_date as to_date";
    $field_list .= ", ?:ec_table_booking_system.slot_time as slot_time";
    $field_list .= ", ?:ec_table_booking_system.free_time as free_time";
    $field_list .= ", ?:ec_table_booking_system.days_data as days_data";
    $field_list .= ", ?:ec_table_booking_system.quantity_selector as quantity_selector";
    $field_list .= ", ?:ec_table_booking_system.show_price_date as show_price_date";
    $field_list .= ", ?:ec_table_booking_system.blocked_date as blocked_date";
    $field_list .= ", ?:ec_table_booking_system.minimum_booking_time as minimum_booking_time";
    $join .= db_quote(" LEFT JOIN ?:ec_table_booking_system ON ?:ec_table_booking_system.product_id = ?:products.product_id AND ?:ec_table_booking_system.product_id = ?i", $product_id);
}

function fn_ec_table_booking_system_get_product_data_post(&$product_data, $auth, $preview, $lang_code)
{
    $product_data['price_wise'] = db_get_array("SELECT * FROM ?:ec_table_booking_system_price WHERE product_id = ?i",$product_data['product_id']);
}

/**
 * Get product data
 *
 * @param mixed $product_id
 * @param mixed $field_list
 * @param mixed $join
 * @param mixed $auth
 *
 * @return void
 */
function Fn_Ec_Table_Booking_System_Get_My_Product_data(&$product_id, &$field_list, &$join, &$auth)
{
    $field_list .= ", ?:ec_table_booking_system.booking_type as booking_type";
    $field_list .= ", ?:ec_table_booking_system.from_date as from_date";
    $field_list .= ", ?:ec_table_booking_system.to_date as to_date";
    $field_list .= ", ?:ec_table_booking_system.slot_time as slot_time";
    $field_list .= ", ?:ec_table_booking_system.free_time as free_time";
    $field_list .= ", ?:ec_table_booking_system.days_data as days_data";
    $field_list .= ", ?:ec_table_booking_system.blocked_date as blocked_date";
    $field_list .= ", ?:ec_table_booking_system.quantity_selector as quantity_selector";
    $field_list .= ", ?:ec_table_booking_system.show_price_date as show_price_date";
    $field_list .= ", ?:ec_table_booking_system.minimum_booking_time as minimum_booking_time";
    $join .= db_quote(" LEFT JOIN ?:ec_table_booking_system ON ?:ec_table_booking_system.product_id = ?:products.product_id AND ?:ec_table_booking_system.product_id = ?i", $product_id);
}
/**
 * Update order details
 *
 * @param mixed $order_id
 * @param mixed $force_notification
 * @param mixed $order_info
 * @param mixed $_error
 *
 * @return void
 */
function Fn_Ec_Table_Booking_System_Order_Placement_routines(&$order_id, &$force_notification, &$order_info, &$_error)
{
    $status = $order_info['status'];


    if (in_array($status, fn_get_order_paid_statuses())) {
        $cart = fn_get_order_info($order_id);
        if (isset($cart['product_groups'])) {
            foreach ($cart['product_groups'] as $index => $company_data) {
                foreach ($company_data['products'] as $hash => $order_product) {
                    if (isset($order_product['extra']['booking_info']) && !empty($order_product['extra']['booking_info'])) {
                        if (!fn_ec_table_booking_system_check_slot_if_already_booked($order_product['product_id'], $order_product['extra']['booking_info'])) {
                            fn_set_notification('W', __('warning'), __('ec_table_booking_system.booking_already_reserved_or_expired'));
                            $input_data = array(
                                'order_id' => $order_id,
                                'product_id' => $order_product['product_id'],
                                'booking_info' => '',
                                'start_date' => '',
                                'end_date' => '',
                                'slot' => '',
                                'quantitiy' => '',
                                'type' => '',
                                'status' => 'D'
                                );
                        } else {
                            $input_data = array(
                                'order_id' => $order_id,
                                'product_id' => $order_product['product_id'],
                                'start_date' => '',
                                'end_date' => '',
                                'slot' => '',
                                'quantitiy' => '',
                                'booking_info' => '',
                                'type' => '',
                                'status' => 'A'
                                );
                        }
                        if ($order_product['extra']['booking_info']['booking_type'] == 'T') {
                            // $days_data = db_get_field("SELECT days_data FROM ?:ec_table_booking_system WHERE product_id = ?i",$order_product['product_id']);
                            // $days_data = unserialize($days_data);
                            // $day = strtolower(date('l', $order_product['extra']['booking_info']['booking_date']));
                            // $data = $days_data[$day]['time_by_amount'];
                            // foreach($data as $key => $item) {
                            //     $time_slots = explode(" - ",$order_product['extra']['booking_info']['booking_slot']);
                            //     if(strcmp($item['start_time'], $time_slots[0]) == 0) {
                            //         if(strcmp($item['end_time'], $time_slots[1]) == 0)
                            //             $days_data[$day]['time_by_amount'][$key]['amount'] = $item['amount'] - $order_product['extra']['booking_info']['booking_slot_amount'];
                            //     }
                            // }
                            // $booking_data = array(
                            //     'days_data' => serialize($days_data)
                            // );
                            // db_query('UPDATE ?:ec_table_booking_system SET ?u WHERE product_id = ?i', $booking_data, $order_product['product_id']);
                            $selected_date_format   = fn_date_format($order_product['extra']['booking_info']['booking_date'], "%Y-%m-%d");
                            $input_data['start_date'] = $selected_date_format;
                            $input_data['slot'] = $order_product['extra']['booking_info']['booking_slot'];
                            $input_data['quantity'] = $order_product['extra']['booking_info']['booking_slot_amount'];

                        }
                        else {
                            $startDate = $order_product['extra']['booking_info']['from'];
                            $startDate = str_replace('/', '-', $startDate);
                            $from   = fn_date_format(strtotime($startDate), "%Y-%m-%d");
                            $input_data['start_date'] = $from;


                            $endDate = $order_product['extra']['booking_info']['to'];
                            $endDate = str_replace('/', '-', $endDate);
                            $to   = fn_date_format(strtotime($endDate), "%Y-%m-%d");
                            $input_data['end_date'] = $to;

                        }


                        $input_data['booking_type'] = $order_product['extra']['booking_info']['booking_type'];
                        $input_data['booking_info'] = serialize($order_product['extra']['booking_info']);
                        db_query("INSERT INTO ?:ec_table_booking_system_booking_info ?e", $input_data);
                    }
                }
            }
        }
    }
}
/**
 * Update and Verify booking details
 *
 * @param mixed $booking_data
 * @param mixed $product_id
 *
 * @return void
 */
function Fn_Ec_Table_Booking_System_Update_Booking_data($booking_data, $product_id)
{
    if (!isset($booking_data['booking_type']) || !$product_id) {
        return false;
    }
    $param_data = array(
        'product_id'   => $product_id,
        'booking_type' => '',
        'from_date'    => TIME,
        'to_date'      => TIME,
        'slot_time'    => '0',
        'free_time'   => '0',
        'days_data'    => '',
        'quantity_selector'    => '',
    );
    $f = 0;
    $from_date = fn_parse_date($booking_data['from_date']);
    $to_date = fn_parse_date($booking_data['to_date']);
    $date_curr = strtotime('today midnight');
    if(isset($booking_data['booking_type']) && $booking_data['booking_type'] == 'N') {
        $booking_data = array_merge($param_data, $booking_data);
        $p_id  = db_get_field("SELECT product_id FROM ?:ec_table_booking_system WHERE product_id = ?i", $product_id);
        if (empty($p_id)) {
            db_query('INSERT INTO ?:ec_table_booking_system ?e', $booking_data);
        } else {
            db_query('UPDATE ?:ec_table_booking_system SET ?u WHERE product_id = ?i', $booking_data, $product_id);
        }
        return true;
    }
    if(isset($booking_data['booking_type']) && $booking_data['booking_type'] == 'R') {
        $service_data  = $booking_data['R'];
        $service_data['blocked_date']  = $booking_data['blocked_date'];
        $service_data['booking_type'] = 'R';
        $service_data['quantity_selector'] = 'Y';
        $service_data['show_price_date'] = $booking_data['show_price_date'];
        $from_date = fn_parse_date($service_data['from_date']);
        $to_date = fn_parse_date($service_data['to_date']);
        $date_curr = strtotime('today midnight');
        $service_data['from_date']  = isset($service_data['from_date'])? fn_parse_date($service_data['from_date']):'';
        $service_data['to_date']    = isset($service_data['to_date'])? fn_parse_date($service_data['to_date']):'';
        $service_data               = array_merge($param_data, $service_data);
        $p_id                       = db_get_field("SELECT product_id FROM ?:ec_table_booking_system WHERE product_id = ?i", $product_id);
        if (empty($p_id)) {
            db_query('INSERT INTO ?:ec_table_booking_system ?e', $service_data);
        } else {
            db_query('UPDATE ?:ec_table_booking_system SET ?u WHERE product_id = ?i', $service_data, $product_id);
        }

        if(!empty($service_data['price_wise'])) {
            db_query("DELETE FROM ?:ec_table_booking_system_price WHERE product_id = ?i", $product_id);
            foreach($service_data['price_wise'] as $key => $item) {
                if(empty($item['price'])) {
                    unset($service_data['price_wise'][$key]);
                }
                else {
                    $price_data = array(
                        'product_id' => $product_id,
                        'from_date' => $item['from_date'],
                        'to_date' => $item['to_date'],
                        'price' => $item['price'],
                    );
                    db_query('INSERT INTO ?:ec_table_booking_system_price ?e', $price_data);
                }
            }
        }
        return true;
    }
    else {
        $f = 0;
        $from_date = fn_parse_date($booking_data['from_date']);
        $to_date = fn_parse_date($booking_data['to_date']);
        $date_curr = strtotime('today midnight');
        // if ($from_date <= $date_curr || $from_date >= $to_date) {
        //     fn_set_notification('E', __('error'), __('ec_table_booking_system.date_from_early'));
        //     return false;
        // }
        if(!isset($booking_data['sunday_status'])) {
            $booking_data['sunday_status'] = 0;
        }
        if(!isset($booking_data['monday_status'])) {
            $booking_data['monday_status'] = 0;
        }
        if(!isset($booking_data['tuesday_status'])) {
            $booking_data['tuesday_status'] = 0;
        }
        if(!isset($booking_data['wednesday_status'])) {
            $booking_data['wednesday_status'] = 0;
        }
        if(!isset($booking_data['thursday_status'])) {
            $booking_data['thursday_status'] = 0;
        }
        if(!isset($booking_data['friday_status'])) {
            $booking_data['friday_status'] = 0;
        }
        if(!isset($booking_data['saturday_status'])) {
            $booking_data['saturday_status'] = 0;
        }
        if (isset($booking_data['sunday_status'])) {
            $days_data = array(
            'sunday_status'               => $booking_data['sunday_status'],
            'sunday_timing_start_time'    => $booking_data['sunday_timing_start_time'],
            'sunday_timing_end_time'      => $booking_data['sunday_timing_end_time'],
            'monday_status'               => $booking_data['monday_status'],
            'monday_timing_start_time'    => $booking_data['monday_timing_start_time'],
            'monday_timing_end_time'      => $booking_data['monday_timing_end_time'],
            'tuesday_status'              => $booking_data['tuesday_status'],
            'tuesday_timing_start_time'   => $booking_data['tuesday_timing_start_time'],
            'tuesday_timing_end_time'     => $booking_data['tuesday_timing_end_time'],
            'wednesday_status'            => $booking_data['wednesday_status'],
            'wednesday_timing_start_time' => $booking_data['wednesday_timing_start_time'],
            'wednesday_timing_end_time'   => $booking_data['wednesday_timing_end_time'],
            'thursday_status'             => $booking_data['thursday_status'],
            'thursday_timing_start_time'  => $booking_data['thursday_timing_start_time'],
            'thursday_timing_end_time'    => $booking_data['thursday_timing_end_time'],
            'friday_status'               => $booking_data['friday_status'],
            'friday_timing_start_time'    => $booking_data['friday_timing_start_time'],
            'friday_timing_end_time'      => $booking_data['friday_timing_end_time'],
            'saturday_status'             => $booking_data['saturday_status'],
            'saturday_timing_start_time'  => $booking_data['saturday_timing_start_time'],
            'saturday_timing_end_time'    => $booking_data['saturday_timing_end_time']
            );
            $diff = [];
            //change for invalid book time start
            if ($days_data['sunday_status']=='1') {
                $diff[]=strtotime($days_data['sunday_timing_end_time'])-strtotime($days_data['sunday_timing_start_time']);
            }
            if ($days_data['monday_status']=='1') {
                $diff[]=strtotime($days_data['monday_timing_end_time'])-strtotime($days_data['monday_timing_start_time']);
            }
            if ($days_data['tuesday_status']=='1') {
                $diff[]=strtotime($days_data['tuesday_timing_end_time'])-strtotime($days_data['tuesday_timing_start_time']);
            }
            if ($days_data['wednesday_status']=='1') {
                $diff[]=strtotime($days_data['wednesday_timing_end_time'])-strtotime($days_data['wednesday_timing_start_time']);
            }
            if ($days_data['thursday_status']=='1') {
                $diff[]=strtotime($days_data['thursday_timing_end_time'])-strtotime($days_data['thursday_timing_start_time']);
            }
            if ($days_data['friday_status']=='1') {
                $diff[]=strtotime($days_data['friday_timing_end_time'])-strtotime($days_data['friday_timing_start_time']);
            }
            if ($days_data['saturday_status']=='1') {
                $diff[]=strtotime($days_data['saturday_timing_end_time'])-strtotime($days_data['saturday_timing_start_time']);
            }
            if(!empty($diff)){
                $t_diff=min($diff)/60;
            } else {
                $t_diff=0;
            }
        }

        //change for invalid date end
        $_days_data = db_get_field("SELECT days_data FROM ?:ec_table_booking_system WHERE product_id = ?i", $product_id);
        if ($_days_data) {
            $_days_data = unserialize($_days_data);
            // fn_print_die($days_data, $_days_data);
            $days_data = array_merge($_days_data, $days_data);
            // foreach($_days_data as $day=>$)
            // $days_data[$day]['time_by_amount'] = $request_data['booking_data'];
        }
        $booking_data['days_data'] = serialize($days_data);
        if (!is_numeric($booking_data['slot_time'])) {
            $booking_data['slot_time'] = 0;
            fn_set_notification('W', __('warning'), __('ec_table_booking_system.please_set_booking_time_numeric_only'));
        }
        if (!is_numeric($booking_data['free_time'])) {
            $booking_data['free_time'] = 0;
            fn_set_notification('W', __('warning'), __('ec_table_booking_system.please_set_break_time_numeric_only'));
        }
    }
    if ($f == 0) {
        $booking_data['from_date']  = isset($booking_data['from_date'])? fn_parse_date($booking_data['from_date']):'';
        $booking_data['to_date']    = isset($booking_data['to_date'])? fn_parse_date($booking_data['to_date']):'';
        $booking_data               = array_merge($param_data, $booking_data);
        $p_id                       = db_get_field("SELECT product_id FROM ?:ec_table_booking_system WHERE product_id = ?i", $product_id);
        if (empty($p_id)) {
            db_query('INSERT INTO ?:ec_table_booking_system ?e', $booking_data);
        } else {
            db_query('UPDATE ?:ec_table_booking_system SET ?u WHERE product_id = ?i', $booking_data, $product_id);
            if (db_get_field("SELECT company_id FROM ?:products WHERE product_id=?i", $p_id)==0) {
                $cm_product_ids=db_get_fields("SELECT product_id FROM ?:products WHERE parent_product_id=?i", $p_id);
                if (!empty($cm_product_ids)) {
                    $bk_data=$booking_data;
                    foreach ($cm_product_ids as $cm_key=>$cm_value) {
                        $bk_data['product_id']=$cm_value;
                        db_query('REPLACE INTO ?:ec_table_booking_system ?e', $bk_data);
                    }
                }
            }
        }
        if(!empty($booking_data['R']['price_wise'])) {
            db_query("DELETE FROM ?:ec_table_booking_system_price WHERE product_id = ?i", $product_id);
            foreach($booking_data['R']['price_wise'] as $key => $item) {
                if(empty($item['price'])) {
                    unset($booking_data['R']['price_wise'][$key]);
                }
                else {
                    $price_data = array(
                        'product_id' => $product_id,
                        'from_date' => $item['from_date'],
                        'to_date' => $item['to_date'],
                        'price' => $item['price'],
                    );
                    db_query('INSERT INTO ?:ec_table_booking_system_price ?e', $price_data);
                }
            }
        }
        return true;
    } else {
        fn_set_notification('E', __('error'), __('ec_table_booking_system.time_clashes'));
    }
    return false;
}

function fn_ec_table_booking_system_get_cart_product_data($product_id, &$_pdata, &$product, &$auth, &$cart, $hash) {
    if(!empty($product['extra']['booking_info'])) {
        $booking_info = $product['extra']['booking_info'];
        if ($booking_info['booking_type'] == 'T') {
            $dates = $booking_info['original_booking_date'];
            $price = fn_ec_table_booking_price_wise_product_price($dates,$product_id);

            if(isset($product['product_options']))
            $price = fn_apply_options_modifiers($product['product_options'], $price, 'P', array(), array('product_data' => $product));

            $_pdata['price'] = $price;
        } elseif($booking_info['booking_type'] == 'R') {
            $booking_date = explode("to",$booking_info['booking_date'] );
            if(!empty($booking_date[0]) && !empty($booking_date[1])) {
                $dates = fn_ec_table_booking_get_between_dates($booking_date[0], $booking_date[1]);
                $dates = implode("|",$dates);
                $price = fn_ec_table_booking_price_wise_product_price($dates,$product_id);
                if(isset($product['product_options']))
                $price = fn_apply_options_modifiers($product['product_options'], $price, 'P', array(), array('product_data' => $product));

                $_pdata['price'] = $price;
            }
        }
    }
}

/**
 * Product data updation before adding product to cart
 *
 * @param mixed $product_data
 * @param mixed $cart
 * @param mixed $auth
 * @param mixed $update
 *
 * @return void
 */
function Fn_Ec_Table_Booking_System_Pre_Add_To_cart(&$product_data, &$cart, &$auth, &$update)
{
    if (defined('ORDER_MANAGEMENT') && Registry::get('runtime.mode') == 'add') {
        foreach ($product_data as $product_id => $single_product_data) {
            if (fn_ec_table_booking_system_check_if_booking_product($product_id)) {
                unset($product_data[$product_id]);
                fn_set_notification('W', __('warning'), __('ec_table_booking_system.booking_product_not_allowed_in_cart_from_admin'));
            }
        }
    }
    if ($update == true) {
        foreach ($product_data as $key => $data) {
            $product_id = $data['product_id'];
            if (fn_ec_table_booking_system_check_if_booking_product($product_id)) {
                if (isset($cart['products'][$key]['extra']['booking_info']['booking_type']) && !empty($cart['products'][$key]['extra']['booking_info']['booking_type'])) {
                    if($cart['products'][$key]['extra']['booking_info']['booking_type'] == 'T') {
                        $product_data[$key]['extra']['booking_info']['booking_type'] = $cart['products'][$key]['extra']['booking_info']['booking_type'];
                        $product_data[$key]['extra']['booking_info']['booking_date'] = $cart['products'][$key]['extra']['booking_info']['booking_date'];
                        $product_data[$key]['extra']['booking_info']['booking_slot_amount'] = $cart['products'][$key]['extra']['booking_info']['booking_slot_amount'];
                        $product_data[$key]['extra']['booking_info']['booking_slot'] = $cart['products'][$key]['extra']['booking_info']['booking_slot'];
                    }
                    if (!fn_ec_table_booking_system_check_slot_if_already_booked($product_id, $product_data[$key]['extra']['booking_info'])) {
                        fn_set_notification('W', __('warning'), __('ec_table_booking_system.booking_already_reserved_or_expired'));
                        unset($product_data[$key]);
                        unset($cart['products'][$key]);
                        continue;
                    }
                } else {
                    fn_set_notification('W', __('warning'), __('ec_table_booking_system.no_booking_info_please_choose_booking'));
                    $product_data[$key]['amount'] = 0;
                }
            }
        }
    } else {
        foreach ($product_data as $key => $value) {
            if (isset($product_data[$key]['booking_info'])) {
                $product_id = $value['product_id'];
                if (isset($cart['products'])) {
                    foreach ($cart['products'] as $in_dex => $p_data) {
                        if ($p_data['product_id'] == $product_id) {
                            unset($cart['products'][$in_dex]);
                            fn_set_notification('N', __('notice'), __('ec_table_booking_system.product_booking_info_updated'));
                            $cart['skip_notification'] = true;
                        }
                    }
                }
                $product_data[$key]['extra']['booking_info']['booking_type'] = $product_data[$key]['booking_info']['booking_type'];
                if ($product_data[$key]['booking_info']['booking_type'] == 'T') {
                    fn_define('ORDER_MANAGEMENT', true);
                    $product_data[$key]['extra']['booking_info']['booking_slot'] = $product_data[$key]['booking_info']['booking_slot'];
                    $product_data[$key]['extra']['booking_info']['booking_date'] = strtotime($product_data[$key]['booking_info']['booking_date']);
                    $product_data[$key]['extra']['booking_info']['original_booking_date'] = $product_data[$key]['booking_info']['booking_date'];
                    $product_data[$key]['extra']['booking_info']['booking_slot_amount'] = $product_data[$key]['booking_info']['booking_slot_amount'];
                    unset($product_data[$key]['is_edp']);
                    if(!empty($product_data[$key]['booking_info']['booking_slot_amount']))
                        $product_data[$key]['amount'] = $product_data[$key]['booking_info']['booking_slot_amount'];
                    else
                        $product_data[$key]['extra']['booking_info']['booking_slot_amount'] = 1;
                }
                elseif($product_data[$key]['booking_info']['booking_type'] == 'R') {
                    unset($product_data[$key]['is_edp']);
                    fn_define('ORDER_MANAGEMENT', true);
                    $product_data[$key]['extra']['booking_info']['booking_date'] = $product_data[$key]['booking_info']['booking_date'];
                    $booking_date = explode("to",$product_data[$key]['extra']['booking_info']['booking_date'] );
                    if(!empty($booking_date[0]) && !empty($booking_date[1])) {
                        $product_data[$key]['extra']['booking_info']['from'] = $booking_date[0];
                        $product_data[$key]['extra']['booking_info']['to'] = $booking_date[1];
                        $product_data[$key]['amount'] = 1;
                    }
                    else {
                        fn_set_notification('W', __('warning'), __('ec_table_booking_system.please_add_date_properly'));
                        unset($product_data[$key]);
                        $product_data[$key]['amount'] = 0;
                        if (isset($cart['products'][$key])) {
                            unset($cart['products'][$key]);
                        }
                        continue;
                    }
                }
                if (!fn_ec_table_booking_system_check_slot_if_already_booked($product_id, $product_data[$key]['extra']['booking_info'])) {
                    fn_set_notification('W', __('warning'), __('ec_table_booking_system.booking_already_reserved_or_expired'));
                    unset($product_data[$key]);
                    $product_data[$key]['amount'] = 0;
                    if (isset($cart['products'][$key])) {
                        unset($cart['products'][$key]);
                    }
                    continue;
                }
                unset($product_data[$key]['booking_info']);
            } else {
                if (AREA == 'A' && $_REQUEST['dispatch'] == "order_management.edit") {
                    if (isset($value['extra']['booking_info']['booking_type']) && !empty($value['extra']['booking_info']['booking_type'])) {
                        if (isset($_REQUEST['copy'])) {
                            fn_set_notification('E', __('ERROR'), __('ec_table_booking_system.cannot_copy_booked_order'));
                        } else {
                            fn_set_notification('E', __('ERROR'), __('ec_table_booking_system.cannot_edit_booked_order'));
                        }
                        fn_redirect("orders.details?order_id=" . $_REQUEST['order_id']);
                    }
                }
            }
        }
    }
}

function fn_ec_table_booking_price_wise_product_price($date_selected,$product_id) {
    $price_wise_in_array = db_get_array("SELECT * FROM ?:ec_table_booking_system_price  WHERE product_id = ?i",$product_id);

    $price = 0;
    $number_of_dates_done = 0;
    $number_dats_available = 0;
    if(!empty($date_selected)) {
        $dates_selected = explode("|",$date_selected);
        $number_dats_available = count($dates_selected);
        if(!empty($price_wise_in_array)) {
            foreach($price_wise_in_array as $key => $item) {
                // $price_wise_in_array[$key]['price'] = fn_format_price_by_currency($item['price'],CART_PRIMARY_CURRENCY,CART_SECONDARY_CURRENCY);
                $price_wise_in_array[$key]['price'] = $item['price'];
            }
            // $price_wise_in_array = unserialize($price_wise);
            foreach($dates_selected as $key => $item) {
                $dates_selected[$key] = strtotime($item);
            }
            $remaining = 0;
            // fn_print_R($price_wise_in_array,$dates_selected);
            foreach($price_wise_in_array as $key => $item) {
                if(!empty($dates_selected)) {
                    foreach($dates_selected as $ke => $ite) {
                            // fn_print_R(strtotime(fn_date_format($item['to_date'], '%d-%b-%Y')),strtotime(fn_date_format($item['from_date'], '%d-%b-%Y')),strtotime(fn_date_format($ite, '%d-%b-%Y')));
                            $from_date = strtotime(fn_date_format($item['from_date'], '%d-%b-%Y'));
                            $to_date = strtotime(fn_date_format($item['to_date'], '%d-%b-%Y'));
                            $current = strtotime(fn_date_format($ite, '%d-%b-%Y'));
                        if($from_date <= $current && $current<= $to_date) {
                            $price += $item['price'];
                            unset($dates_selected[$ke]);
                            $number_of_dates_done++;
                        }
                    }
                }
            }
        }
    }

    // fn_print_R($price);

    if(!empty($number_dats_available)) {
        if($number_dats_available != $number_of_dates_done) {
            $remaining = $number_dats_available - $number_of_dates_done;
        }
    }
    $original_price = fn_get_product_price($product_id,1,$_SESSION['auth']);
    if(isset($remaining)) {
        $price += $original_price * $remaining;
    }
    else {
        $price = $original_price;
    }
    // fn_print_R($price);
    return $price;
}

function fn_ec_table_booking_get_between_dates($startDate, $endDate)
{
    $rangArray = [];

    // $startDate = strtotime($startDate);
    // $endDate = strtotime($endDate);

    $startDate = str_replace('/', '-', $startDate);
    $startDate = strtotime($startDate);

    $endDate = str_replace('/', '-', $endDate);
    $endDate = strtotime($endDate);


    for ($currentDate = $startDate; $currentDate <= $endDate;
                                    $currentDate += (86400)) {

        $date = date('Y-m-d', $currentDate);
        $rangArray[] = $date;
    }

    return $rangArray;
}
/**
 * Return available and unavailable time slots
 *
 * @param mixed $start_time
 * @param mixed $end_time
 * @param mixed $booking_slot
 * @param mixed $break_slot
 * @param mixed $booked
 *
 * @return array $out,$out_avoid
 */
function Fn_Ec_Table_Booking_System_Time_Slots_array($start_time, $end_time, $booking_slot, $break_slot, $booked = array(),$time_by_amount = array(),$selected_date='',$n_booked_info = array())
{
    if (!$booking_slot) {
        return array(array(), array());
    }
    $start       = DateTime::createFromFormat('Y-m-d H:i:s', $start_time); //create date time objects
    $end         = DateTime::createFromFormat('Y-m-d H:i:s', $end_time); //create date time objects
    $time1       = $start;
    $count       = 0; //number of slots
    $count_avoid = 0; //number of slots
    $out         = array(); //array of slots
    $out_avoid   = array(); //array of slots
    $total_slots = array(); //array of total slots
    $addons_settings = fn_get_ec_table_booking_system_settings();
    if(!empty($time_by_amount)) {
        $main_preparation_time = TIME;
        foreach($time_by_amount as $key => $item) {
            $time_by_amount[$key][0] = $item['start_time'];
            $time_by_amount[$key][1] = $item['end_time'];
            $start_time_date = $selected_date . ' ' . $item['start_time'];
            if($addons_settings['time_format'] == 24){
                $st = DateTime::createFromFormat('Y-m-d H:i', $start_time_date);
            }
            else {
                $st = DateTime::createFromFormat('Y-m-d H:i a', $start_time_date);
            }
            unset($time_by_amount[$key]['start_time']);
            unset($time_by_amount[$key]['end_time']);
            $current_slots = $time_by_amount[$key][0].' - '.$time_by_amount[$key][1];
            if(isset($n_booked_info[$selected_date][$current_slots])) {
                $item['amount'] = $item['amount'] - $n_booked_info[$selected_date][$current_slots];
                $time_by_amount[$key]['amount'] = $item['amount'];
            }

            if($item['amount'] >0 && date_timestamp_get($st) >= $main_preparation_time) {
                $count++;
                array_push($out, $time_by_amount[$key]); //add slot to array
                $total_slots[] = array('slots' => $time_by_amount[$key], 'available' => 'yes');
            }
            else {
                $count_avoid++;
                array_push($out_avoid, $time_by_amount[$key]); //add slot to array
                $total_slots[] = array('slots' => $time_by_amount[$key], 'available' => 'no');
            }
        }
    }
    else {
        for ($i = $start; $i < $end;) { //for loop
            $avoid = false;
            $t1    = date_timestamp_get($i);
            $t2    = $t1 + ($booking_slot * 60);
            for ($k = 0; $k < sizeof($booked); $k += 2) { //if booked hour
                $st = DateTime::createFromFormat('Y-m-d H:i a', $booked[$k]);
                $en = DateTime::createFromFormat('Y-m-d H:i a', $booked[$k + 1]);
                if ($t1 >= date_timestamp_get($st) && $t2 <= date_timestamp_get($en)) {
                    $avoid = true;
                }
                //yes. booked
            }
            if($addons_settings['time_format'] == 24){
                $slots = [$i->format('H:i'), $i->modify("+" . $booking_slot . " minutes")->format('H:i')];
            }
            else {
                $slots = [$i->format('h:i a'), $i->modify("+" . $booking_slot . " minutes")->format('h:i a')];
            }
            // fn_print_R($slots);
            if (!$avoid && $i < $end) {
                //if not booked and less than end time
                $count++;
                array_push($out, $slots); //add slot to array
                $total_slots[] = array('slots' => $slots, 'available' => 'yes');
            } else {
                $count_avoid++;
                array_push($out_avoid, $slots); //add slot to array
                $total_slots[] = array('slots' => $slots, 'available' => 'no');
            }
            $i->modify("+" . $break_slot . " minutes");
        }
    }
    return array($out, $out_avoid);
}


function fn_ec_table_booking_system_get_saved_data($request_data)
{
    if(!empty($request_data['product_id'])) {
        $product_id = $request_data['product_id'];
        $day = $request_data['day'];
        $days_data = db_get_field("SELECT days_data FROM ?:ec_table_booking_system WHERE product_id = ?i",$product_id);
        $days_data = unserialize($days_data);
    }
    else {
        $company_id = $request_data['company_id'];
        $day = $request_data['day'];
        $booking_data = db_get_field("SELECT booking_data FROM ?:companies WHERE company_id = ?i",$company_id);
        $booking_data = unserialize($booking_data);
        $days_data = unserialize($booking_data['days_data']);
    }
    if(isset($days_data[$day]['time_by_amount'])) {
        $date_by_amount = $days_data[$day]['time_by_amount'];
        return $date_by_amount;
    }
    else {
        return false;
    }
}
function fn_ec_save_booking_data_by_amount($request_data)
{
    if(!empty($request_data['product_id'])) {
        $product_id = $request_data['product_id'];
        $day = $request_data['day'];
        $days_data = db_get_field("SELECT days_data FROM ?:ec_table_booking_system WHERE product_id = ?i",$product_id);
        $days_data = unserialize($days_data);
        $days_data[$day]['time_by_amount'] = $request_data['booking_data'];
        $booking_data = array(
            'days_data' => serialize($days_data)
        );
        db_query('UPDATE ?:ec_table_booking_system SET ?u WHERE product_id = ?i', $booking_data, $product_id);
        fn_set_notification("N",__("notice"),__("ec_table_booking_system.saved_amount"));
    }
    else {
        $company_id = $request_data['company_id'];
        $day = $request_data['day'];
        $booking_data = db_get_field("SELECT booking_data FROM ?:companies WHERE company_id = ?i",$company_id);
        $booking_data = unserialize($booking_data);
        $days_data = unserialize($booking_data['days_data']);
        $days_data[$day]['time_by_amount'] = $request_data['booking_data'];
        $booking_data['days_data'] = serialize($days_data);
        $_booking_data = array(
            'booking_data' => serialize($booking_data)
        );

        db_query('UPDATE ?:companies SET ?u WHERE company_id = ?i', $_booking_data, $company_id);
        fn_set_notification("N",__("notice"),__("ec_table_booking_system.saved_amount"));

    }
}
/**
 * Get all available and unavailable slots of single day multiple slots
 *
 * @param mixed $selected_date
 * @param mixed $product_id
 *
 * @return array $available_time_slots,$unavailable_time_slots
 */
function Fn_Ec_Table_Booking_System_Get_Single_Day_All_slots($selected_date, $product_id)
{
    $product_data               = fn_get_product_data($product_id, $_SESSION['auth']);
    if ($product_data['booking_type'] == 'T') {
        $start_date                 = $product_data['from_date'];
        $end_date                   = $product_data['to_date'];
        $selected_date_timestamp    = strtotime($selected_date);
        $blocked_date               = $product_data['blocked_date'];
        if (isset($product_data['company_id']) && !empty($product_data['company_id'])) {
            $blocked_date =fn_ec_table_booking_get_blocked_date($product_data['company_id'],$product_data['blocked_date']);
        }else {
            $blocked_date =$product_data['blocked_date'];
        }
        $available_time_slots       = array();
        $unavailable_time_slots     = array();
        if(!empty($blocked_date)) {
            $f = 0;
            $blocked_date = explode(",",$blocked_date);
            foreach($blocked_date as $key => $item) {
                $date = str_replace('/', '-', $item);
                if($selected_date_timestamp == strtotime($date)) {
                    $f= 1;
                }
            }
            if($f) {
                return false;
            }
        }
        if ($start_date <= $selected_date_timestamp && $end_date >= $selected_date_timestamp) {
            $days_data          = unserialize($product_data['days_data']);
            $book_slot          = $product_data['slot_time'];
            $break_slot         = $product_data['free_time'];
            $start_date_format  = date("Y-m-d", $start_date);
            $end_date_format    = date("Y-m-d", $end_date);
            $day                = date('D', strtotime($selected_date));
            $booked_info                = fn_ec_table_booking_system_get_booked_info($product_id, 'T');
            $booked_info = reset($booked_info);
            $n_booked_info = array();
            if(!empty($booked_info)) {
                foreach($booked_info as $key => $item) {
                    if(isset($n_booked_info[$item['slot']]))
                        $n_booked_info[$item['start_date']][$item['slot']] = $n_booked_info[$item['slot']] + $item['quantity'];
                    else
                        $n_booked_info[$item['start_date']][$item['slot']] = $item['quantity'];
                }
            }
            $booking_info       = array();
            if ($day == 'Sun') {
                if ($days_data['sunday_status'] == '1') {
                    $start_time_in_24_hour_format = date("H:i:s", strtotime($days_data['sunday_timing_start_time']));
                    $end_time_in_24_hour_format = date("H:i:s", strtotime($days_data['sunday_timing_end_time']));
                    $start_time = $selected_date . ' ' . $start_time_in_24_hour_format;
                    $end_time = $selected_date . ' ' . $end_time_in_24_hour_format;
                    $time_by_amount = array();
                    if(isset($days_data['sunday']['time_by_amount'])) {
                        $time_by_amount = $days_data['sunday']['time_by_amount'];
                    }
                    list($available_time_slots, $unavailable_time_slots) = fn_ec_table_booking_system_time_slots_array($start_time, $end_time, $book_slot, $break_slot, $booking_info,$time_by_amount,$selected_date,$n_booked_info);
                }
            } elseif ($day == 'Mon') {
                if ($days_data['monday_status'] == '1') {
                    $start_time_in_24_hour_format = date("H:i:s", strtotime($days_data['monday_timing_start_time']));
                    $end_time_in_24_hour_format  = date("H:i:s", strtotime($days_data['monday_timing_end_time']));
                    $start_time = $selected_date . ' ' . $start_time_in_24_hour_format;
                    $end_time = $selected_date . ' ' . $end_time_in_24_hour_format;
                    $time_by_amount = array();
                    if(isset($days_data['monday']['time_by_amount'])) {
                        $time_by_amount = $days_data['monday']['time_by_amount'];
                    }
                    list($available_time_slots, $unavailable_time_slots) = fn_ec_table_booking_system_time_slots_array($start_time, $end_time, $book_slot, $break_slot, $booking_info,$time_by_amount,$selected_date,$n_booked_info);
                }
            } elseif ($day == 'Tue') {
                if ($days_data['tuesday_status'] == '1') {
                    $start_time_in_24_hour_format = date("H:i:s", strtotime($days_data['tuesday_timing_start_time']));
                    $end_time_in_24_hour_format = date("H:i:s", strtotime($days_data['tuesday_timing_end_time']));
                    $start_time = $selected_date . ' ' . $start_time_in_24_hour_format;
                    $end_time   = $selected_date . ' ' . $end_time_in_24_hour_format;
                    $time_by_amount = array();
                    if(isset($days_data['tuesday']['time_by_amount'])) {
                        $time_by_amount = $days_data['tuesday']['time_by_amount'];
                    }
                    list($available_time_slots, $unavailable_time_slots) = fn_ec_table_booking_system_time_slots_array($start_time, $end_time, $book_slot, $break_slot, $booking_info,$time_by_amount,$selected_date,$n_booked_info);
                }
            } elseif ($day == 'Wed') {
                if ($days_data['wednesday_status'] == '1') {
                    $start_time_in_24_hour_format = date("H:i:s", strtotime($days_data['wednesday_timing_start_time']));
                    $end_time_in_24_hour_format   = date("H:i:s", strtotime($days_data['wednesday_timing_end_time']));
                    $start_time = $selected_date . ' ' . $start_time_in_24_hour_format;
                    $end_time   = $selected_date . ' ' . $end_time_in_24_hour_format;
                    $time_by_amount = array();
                    if(isset($days_data['wednesday']['time_by_amount'])) {
                        $time_by_amount = $days_data['wednesday']['time_by_amount'];
                    }
                    list($available_time_slots, $unavailable_time_slots) = fn_ec_table_booking_system_time_slots_array($start_time, $end_time, $book_slot, $break_slot, $booking_info,$time_by_amount,$selected_date,$n_booked_info);
                }
            } elseif ($day == 'Thu') {
                if ($days_data['thursday_status'] == '1') {
                    $start_time_in_24_hour_format = date("H:i:s", strtotime($days_data['thursday_timing_start_time']));
                    $end_time_in_24_hour_format   = date("H:i:s", strtotime($days_data['thursday_timing_end_time']));
                    $start_time = $selected_date . ' ' . $start_time_in_24_hour_format;
                    $end_time   = $selected_date . ' ' . $end_time_in_24_hour_format;
                    $time_by_amount = array();
                    if(isset($days_data['thursday']['time_by_amount'])) {
                        $time_by_amount = $days_data['thursday']['time_by_amount'];
                    }
                    list($available_time_slots, $unavailable_time_slots) = fn_ec_table_booking_system_time_slots_array($start_time, $end_time, $book_slot, $break_slot, $booking_info,$time_by_amount,$selected_date,$n_booked_info);
                }
            } elseif ($day == 'Fri') {
                if ($days_data['friday_status'] == '1') {
                    $start_time_in_24_hour_format = date("H:i:s", strtotime($days_data['friday_timing_start_time']));
                    $end_time_in_24_hour_format   = date("H:i:s", strtotime($days_data['friday_timing_end_time']));
                    $start_time = $selected_date . ' ' . $start_time_in_24_hour_format;
                    $end_time   = $selected_date . ' ' . $end_time_in_24_hour_format;
                    $time_by_amount = array();
                    if(isset($days_data['friday']['time_by_amount'])) {
                        $time_by_amount = $days_data['friday']['time_by_amount'];
                    }
                    list($available_time_slots, $unavailable_time_slots) = fn_ec_table_booking_system_time_slots_array($start_time, $end_time, $book_slot, $break_slot, $booking_info,$time_by_amount,$selected_date,$n_booked_info);
                }
            } elseif ($day == 'Sat') {
                if ($days_data['saturday_status'] == '1') {
                    $start_time_in_24_hour_format = date("H:i:s", strtotime($days_data['saturday_timing_start_time']));
                    $end_time_in_24_hour_format   = date("H:i:s", strtotime($days_data['saturday_timing_end_time']));
                    $start_time = $selected_date . ' ' . $start_time_in_24_hour_format;
                    $end_time   = $selected_date . ' ' . $end_time_in_24_hour_format;
                    $time_by_amount = array();
                    if(isset($days_data['saturday']['time_by_amount'])) {
                        $time_by_amount = $days_data['saturday']['time_by_amount'];
                    }
                    list($available_time_slots, $unavailable_time_slots) = fn_ec_table_booking_system_time_slots_array($start_time, $end_time, $book_slot, $break_slot, $booking_info,$time_by_amount,$selected_date,$n_booked_info);
                }
            }
            return array('available_time_slots' => $available_time_slots, 'unavailable_time_slots' => $unavailable_time_slots);
        } else {
            return false;
        }
    }
    else {
        return false;
    }
}
function fn_ec_table_booking_system_check_quantity_selector($product_id) {
    $quantity_selector = db_get_field("SELECT quantity_selector FROM ?:ec_table_booking_system WHERE product_id = ?i",$product_id);
    return $quantity_selector;
}
/**
 * Check if product is a booking product
 *
 * @param mixed $product_id
 *
 * @return boolean
 */
function Fn_Ec_Table_Booking_System_Check_If_Booking_product($product_id)
{
    $booking_id = db_get_field("SELECT count(*) FROM ?:ec_table_booking_system WHERE booking_type != ?s AND product_id = ?i", 'N', $product_id);
    if ($booking_id) {
        return true;
    }
    return false;
}
/**
 * Get all booking products
 *
 * @param mixed $type
 * @param mixed $params
 *
 * @return array $booking_data,$params
 */
function Fn_Ec_Table_Booking_System_Get_All_Booking_products($type = '', $params = array())
{
    $condition = db_quote(" AND ?:product_descriptions.lang_code = ?s", DESCR_SL);
    if (isset($params['product_name'])) {
        $condition .= db_quote(" AND ?:product_descriptions.product LIKE ?s", '%' . $params['product_name'] . '%');
    }
    if (Registry::get('runtime.company_id')) {
        $condition .= db_quote(' AND ?:products.company_id = ?i', Registry::get('runtime.company_id'));
    }
    $params['total_items'] = db_get_field("SELECT count(*) FROM ?:ec_table_booking_system INNER JOIN `?:products` ON ?:products.product_id = ?:ec_table_booking_system.product_id INNER JOIN `?:product_descriptions` ON ?:product_descriptions.product_id = ?:ec_table_booking_system.product_id WHERE ?:ec_table_booking_system.booking_type = ?s $condition", $type);
    if (!empty($params['items_per_page'])) {
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        $params['limit'] = $limit;
    }
    $query = db_quote("SELECT ?:ec_table_booking_system.*,?:products.status FROM `?:products` INNER JOIN `?:ec_table_booking_system` ON ?:products.product_id = ?:ec_table_booking_system.product_id INNER JOIN `?:product_descriptions` ON ?:product_descriptions.product_id = ?:ec_table_booking_system.product_id WHERE ?:ec_table_booking_system.booking_type = ?s $condition $limit", $type);
    $booking_data = db_get_array($query);
    // fn_print_r($booking_data);
    return array($booking_data, $params);
}
/**
 * Get booking info of a booking product
 *
 * @param mixed $product_id
 * @param mixed $type
 * @param mixed $status
 * @param mixed $params
 *
 * @return array $all_booking_info,$params
 */
function Fn_Ec_Table_Booking_System_Get_Booked_info($product_id = 0, $type = '', $status = 'A', &$params = array())
{
    $default_params = array (
        'page'              => 1,
        'items_per_page'    => Registry::get('settings.Appearance.elements_per_page')
    );
    $params     = array_merge($default_params, $params);
    $condition  = '';
    if ($status == 'A') {
        $condition = db_quote(' AND status = ?s', 'A');
    }
    $limit          = '';
    $booking_data   = array();
    if ($product_id) {
        if (!empty($params['items_per_page'])) {
            $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:ec_table_booking_system_booking_info WHERE product_id = ?i $condition", $product_id);
            $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
            $params['limit'] = $limit;
        }
        $booking_data = db_get_array("SELECT * FROM ?:ec_table_booking_system_booking_info WHERE product_id = ?i $condition $limit", $product_id);
    } else {
        if (!empty($params['items_per_page'])) {
            $params['total_items']  = db_get_field("SELECT COUNT(*) FROM ?:ec_table_booking_system_booking_info WHERE 1 $condition");
            $limit                  = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
            $params['limit']        = $limit;
        }
        $booking_data = db_get_array("SELECT * FROM ?:ec_table_booking_system_booking_info WHERE 1 $condition $limit");
    }
    $all_booking_info = array();
    foreach ($booking_data as $in_dex => $b_data) {
        $booking_info = $b_data;
        $booking_info['booking_info'] = unserialize($b_data['booking_info']);
        if ($booking_info['booking_info']['booking_type'] == $type) {
            $all_booking_info[] = $booking_info;
        } elseif (empty($type)) {
            $all_booking_info[] = $booking_info;
        }
    }
    return array($all_booking_info, $params);
}
/**
 * Get booking information of a product
 *
 * @param mixed $product_id
 * @param mixed $type
 * @param mixed $status
 * @param mixed $params
 *
 * @return array $booking_info,$params
 */
function Fn_Ec_Table_Booking_System_Get_Booked_information($product_id = 0, $type = '', $status = 'A', &$params = array())
{
    $default_params = array (
        'page'              => 1,
        'items_per_page'    => Registry::get('settings.Appearance.admin_elements_per_page')
    );
    $limit  = '';
    $join   = $condition = '';
    $params = array_merge($default_params, $params);
    // Define fields that should be retrieved
    $fields = array (
        'order_id'      => "orders.order_id",
        'user_id'       => "orders.user_id",
        'firstname'     => "orders.firstname",
        'lastname'      => "orders.lastname",
        'email'         => "orders.email",
        'id'            => "booking_and_reservation_booking_info.id",
        'booking_info'  => "booking_and_reservation_booking_info.booking_info",
        'status'        => "booking_and_reservation_booking_info.status"
    );
    $sortings = array(
        'name'      => array(
            "orders.firstname",
            "orders.lastname"
        ),
        'email'     => "orders.email",
        'order_id'  => "orders.order_id"
    );
    $join .= "LEFT JOIN ?:orders as orders ON orders.order_id = booking_and_reservation_booking_info.order_id ";
    $join .= "LEFT JOIN ?:ec_table_booking_system as booking_and_reservation ON booking_and_reservation.product_id = booking_and_reservation_booking_info.product_id ";
    if ($status == 'A') {
        $condition .= db_quote('AND status = ?s', 'A');
    }
    if (Registry::get('runtime.company_id')) {
        $condition .= db_quote('AND orders.company_id = ?i', Registry::get('runtime.company_id'));
    }
    if(!empty($product_id)) {
        $condition.= db_quote(" AND booking_and_reservation_booking_info.product_id =?i", $product_id);
    }
    if(!empty($type))
        $condition.= db_quote(" AND booking_and_reservation.booking_type IN ?s", $type);
    if (empty($params['sort_order'])) {
        $params['sort_order'] = 'asc';
    }
    if (empty($params['sort_by'])) {
        $params['sort_by'] = 'order_id';
    }
    if (isset($params['email']) && fn_string_not_empty($params['email'])) {
        $condition .= db_quote(" AND orders.email LIKE ?l", "%" . trim($params['email']) . "%");
    }
    if (!empty($params['status'])) {
        $condition .= db_quote(" AND booking_and_reservation_booking_info.status IN (?a)", $params['status']);
    }
    if (isset($params['order_id']) && !empty($params['order_id'])) {
        $condition.= db_quote(' AND orders.order_id IN (?a)', $params['order_id']);
    }
    if (isset($params['name']) && fn_string_not_empty($params['name'])) {
        $arr = fn_explode(' ', $params['name']);
        foreach ($arr as $k => $v) {
            if (!fn_string_not_empty($v)) {
                unset($arr[$k]);
            }
        }
        if (sizeof($arr) == 2) {
            $condition.= db_quote(" AND orders.firstname LIKE ?l AND orders.lastname LIKE ?l",  "%" . array_shift($arr) . "%", "%" . array_shift($arr) . "%");
        } else {
            $condition.= db_quote(" AND (orders.firstname LIKE ?l OR orders.lastname LIKE ?l)", "%" . trim($params['name']) . "%", "%" . trim($params['name']) . "%");
        }
    }
    if (isset($params['order_id']) && !empty($params['order_id'])) {
        $condition.= db_quote(' AND orders.order_id IN (?a)', $params['order_id']);
    }
    $condition.= db_quote(' AND orders.order_id IS NOT NULL AND orders.order_id  != ?s', "");
    $sorting = db_sort($params, $sortings, 'name', 'desc');
    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(*) FROM ?:ec_table_booking_system_booking_info AS booking_and_reservation_booking_info $join WHERE 1 $condition");
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
        $params['limit'] = $limit;
    }
    $booking_info = db_get_array('SELECT ' . implode(', ', $fields) . " FROM ?:ec_table_booking_system_booking_info as booking_and_reservation_booking_info $join WHERE 1 $condition $sorting $limit");
    foreach ($booking_info as $in_dex => $b_data) {
        $booking_info[$in_dex]['booking_info'] = unserialize($b_data['booking_info']);
        // if (empty($b_data['order_id'])){
        //     undet($booking_info[$in_dex]);
        // }
    }
    return array($booking_info, $params);
}
/**
 * Reorder of a product
 *
 * @param mixed $order_info
 * @param mixed $cart
 *
 * @return void
 */
function Fn_Ec_Table_Booking_System_reorder(&$order_info, &$cart)
{
    foreach ($order_info['products'] as $key => $product) {
        if (fn_ec_Table_Booking_System_check_if_booking_product($product['product_id'])) {
            unset($order_info['products'][$key]);
            fn_set_notification('W', __('warning'), __('ec_table_booking_system.please_choose_booking_manually'));
        }
    }
}
/**
 * Check if booking slot is already booked
 *
 * @param mixed $product_id
 * @param mixed $booking_info
 *
 * @return boolean
 */
function Fn_Ec_Table_Booking_System_Check_Slot_If_Already_booked($product_id, $booking_info)
{
    if (isset($booking_info['booking_type']) && !empty($booking_info['booking_type'])) {
        if ($booking_info['booking_type'] == 'T') {
            $selected_date          = $booking_info['booking_date'];
            if(isset($booking_info['booking_slot_amount']))
                $booking_slot_amount  = $booking_info['booking_slot_amount'];
            else
                $booking_slot_amount = 1;
            $selected_date_format   = fn_date_format($selected_date, "%Y-%m-%d");
            $all_slots              = fn_ec_table_booking_system_get_single_day_all_slots($selected_date_format, $product_id);

            // $total_order_placed  =  db_get_field("SELECT SUM(quantity) FROM ?:ec_table_booking_system_booking_info WHERE booking_type = ?s AND slot = ?s AND start_date =?s AND product_id = ?i",'T',$booking_info['booking_slot'],$selected_date_format,$product_id);
            if (isset($all_slots['available_time_slots'])) {
                foreach ($all_slots['available_time_slots'] as $_key => $time_slots) {
                    $amount = $time_slots['amount'];
                    unset($time_slots['amount']);
                    $time_slot = implode(' - ', $time_slots);
                    if ($booking_info['booking_slot'] == $time_slot) {
                        if($amount >= $booking_slot_amount)
                            return true;
                        else
                            return false;
                    }
                }
            }
            return false;
        }
        elseif($booking_info['booking_type'] == 'R') {
            $alreay_booked_slots    = fn_ec_table_booking_system_get_already_booked_slot($product_id);
            $booked = array('formated' => array());
            if ($alreay_booked_slots == false) {
                fn_set_notification('W', __('warning'), __('ec_table_booking_system.error_occurred'));
                return false;
            }
            if(!empty($booking_info['booking_date'])) {
                $booking_date = explode("to",$booking_info['booking_date']);
                $dates = fn_ec_table_booking_get_between_dates($booking_date[0], $booking_date[1]);
                foreach ($dates as $in_key => $stamp_time) {
                    if (in_array(strtotime($stamp_time), $alreay_booked_slots['time_stamp'])) {
                        $booked['formated'][] = fn_date_format(strtotime($stamp_time), '%d-%b-%Y');
                    }
                }
            }
            if (empty($booked['formated'])) {
                return true;
            } else {
                $booked_str = implode(',', $booked['formated']);
                fn_set_notification('W', __('warning'), __('ec_table_booking_system.dates_already_booked', array('[dates]' => $booked_str)));
                return false;
            }
        }
    }
    return false;
}
/**
 * Check booking order status
 *
 * @param mixed $order_id
 * @param mixed $product_id
 *
 * @return void
 */
function Fn_Ec_Table_Booking_System_Check_Booking_Order_status($order_id, $product_id)
{
    $booking_status = db_get_row('SELECT * FROM ?:ec_table_booking_system_booking_info WHERE order_id = ?i AND product_id = ?i', $order_id, $product_id);
    return $booking_status;
}
/**
 * Get statuses of booking
 *
 * @return array $item_status
 */
function Fn_Ec_Table_Booking_System_Get_Booking_Status_params()
{
    $items_status = array('A' => __('ec_table_booking_system.booked'), 'D' => __('ec_table_booking_system.cancelled'));
    return $items_status;
}
/**
 * Change status of booking
 *
 * @param mixed $params
 * @param mixed $result
 *
 * @return void
 */
function Fn_Ec_Table_Booking_System_Tools_Change_status($params, &$result)
{
    if ($params['table'] == "ec_table_booking_system_booking_info" && !empty($result)) {
        if ($params['status'] == 'A') {
            if (fn_ec_table_booking_system_allow_status_change($params['id'])) {
                fn_set_notification('E', __('error'), __('ec_table_booking_system.same_product_exist_in_different_order_first_cancel_previous_order'));
                $result = db_query("UPDATE ?:$params[table] SET status = ?s WHERE ?w", 'D', array($params['id_name'] => $params['id']));
                $result = false;
            }
        }
        // else {
        //     $booked_order_info  = db_get_row('SELECT * FROM ?:ec_table_booking_system_booking_info WHERE id = ?i', $params['id']);
        //     $booked_booking_info = unserialize($booked_order_info['booking_info']);
        //     if($booked_booking_info['booking_type'] == 'T') {
        //         $available_booking_data = db_get_row("SELECT * FROM ?:ec_table_booking_system WHERE product_id = ?i",$booked_order_info['product_id']);
        //         $days_data = unserialize($available_booking_data['days_data']);
        //         $day = strtolower(date('l', $booked_booking_info['booking_date']));
        //         $booking_slot = explode(" - ",$booked_booking_info['booking_slot']);
        //         foreach($days_data[$day]['time_by_amount'] as $key => $item) {
        //             if($item['start_time'] == $booking_slot[0] && $item['end_time'] == $booking_slot[1]) {
        //                 $remaining_amount = $item['amount'] + $booked_booking_info['booking_slot_amount'];
        //                 $days_data[$day]['time_by_amount'][$key]['amount'] = $remaining_amount;
        //                 $days_data = serialize($days_data);
        //                 $__data = array(
        //                     'days_data' => $days_data
        //                 );
        //                 db_query('UPDATE ?:ec_table_booking_system SET ?u WHERE product_id = ?i', $__data, $booked_order_info['product_id']);
        //                 break;
        //             }
        //         }
        //     }
        // }
        $force_notification=array();
        $notify_user        = !empty($params['notify_user']) && $params['notify_user'] == 'Y';
        $notify_department  = !empty($params['notify_department']) && $params['notify_department'] == 'Y';
        $notify_vendor      = !empty($params['notify_vendor']) && $params['notify_vendor'] == 'Y';
        $force_notification=array();
        if ($notify_user) {
            $notify_user                = $force_notification['C'];
            $force_notification['C']    = true;
        }
        if ($notify_department) {
            $notify_department          = $force_notification['A'];
            $force_notification['A']    = true;
        }
        if ($notify_vendor) {
            $notify_vendor              = $force_notification['V'];
            $force_notification['V']    = true;
        }

        if(isset($params['order_id']) && !empty($result)) {

            $addon_settings = fn_get_ec_table_booking_system_settings();
            if($params['status'] == 'D') {
                $status_to  = $addon_settings['cancel_status'];
            }
            else {
                $status_to = $addon_settings['approve_status'];
            }
            $order_info = fn_get_order_info($params['order_id']);
            $edp_data = fn_generate_ekeys_for_edp(['status_from' => $order_info['status'], 'status_to' => $status_to], $order_info);
            $order_info['status'] = $status_to;

            db_query('UPDATE ?:orders SET status = ?s, updated_at = ?i WHERE order_id = ?i', $status_to, TIME, $params['order_id']);

            if ($status_to !== STATUS_PARENT_ORDER && $status_to !== STATUS_INCOMPLETED_ORDER) {
                $status_id = strtolower($status_to);
                $event_dispatcher = EventDispatcherProvider::getEventDispatcher();
                $notification_settings_factory = EventDispatcherProvider::getNotificationSettingsFactory();
                $notification_rules = $notification_settings_factory->create($force_notification);

                $event_dispatcher->dispatch(
                    "order.status_changed.{$status_id}",
                    ['order_info' => $order_info],
                    $notification_rules,
                    new OrderProvider($order_info)
                );

                if ($edp_data) {
                    $notification_rules = fn_get_edp_notification_rules($force_notification ?: [], $edp_data);
                    $event_dispatcher->dispatch(
                        'order.edp',
                        ['order_info' => $order_info, 'edp_data' => $edp_data],
                        $notification_rules,
                        new OrderProvider($order_info, $edp_data)
                    );
                }
            }

            fn_order_notification($order_info, $edp_data, $force_notification);
        }
    }
}
/**
 * Check status change allowed or not
 *
 * @param mixed $id
 *
 * @return boolean
 */
function Fn_Ec_Table_Booking_System_Allow_Status_change($id)
{
    $booked_order_info  = db_get_row('SELECT * FROM ?:ec_table_booking_system_booking_info WHERE id = ?i', $id);
    $allow              = 1;
    $booked_booking_info = unserialize($booked_order_info['booking_info']);
    if (!empty($booked_order_info)) {
        $booked_info = db_get_array('SELECT * from ?:ec_table_booking_system_booking_info WHERE product_id = ?i && id != ?i', $booked_order_info['product_id'], $id);
        if($booked_booking_info['booking_type'] == 'R') {
            foreach ($booked_info as $inde_x => $other_booked_data) {
                if ($other_booked_data['id'] != $id) {
                    $booking_info = unserialize($other_booked_data['booking_info']);
                    if ($booking_info['booking_type'] == $booked_booking_info['booking_type'] && $other_booked_data['status'] == 'A') {
                        if($booking_info['booking_date'] == $booked_booking_info['booking_date']) {
                            $allow = 0;
                            break;
                        }
                    }
                }
            }
        }
        else {
            $available_booking_data = db_get_row("SELECT * FROM ?:ec_table_booking_system WHERE product_id = ?i",$booked_order_info['product_id']);
            $days_data = unserialize($available_booking_data['days_data']);
            $day = strtolower(date('l', $booked_booking_info['booking_date']));
            $already_available_product_data = $days_data[$day]['time_by_amount'];
            $booking_slot = explode(" - ",$booked_booking_info['booking_slot']);
            foreach($already_available_product_data as $key => $item) {
                if($item['start_time'] == $booking_slot[0] && $item['end_time'] == $booking_slot[1]) {
                    if($item['amount'] >= $booked_booking_info['booking_slot_amount']){
                        break;
                    }
                    else {
                        $allow = 0;
                        break;
                    }
                }
            }
        }
    }
    if ($allow == 0) {
        return true;
    }
    return false;
}
/**
 * Get Dates in a formatted manner
 *
 * @param mixed $date_array
 *
 * @return array $formatted_date_array
 */
function Fn_Ec_Table_Booking_System_Get_Formated_dates($date_array = array())
{
    $formated_date_array                = array();
    $formated_date_array['date_str']    = '';
    if (count($date_array) == 1) {
        $formated_date_array['date_str'] = $formated_date_array['first'] = fn_date_format($date_array[0], Registry::get('settings.Appearance.date_format'));
    } else {
        for ($index=count($date_array) - 1; $index >= 0 ; $index--) {
            $formated_date_array['more'][] = fn_date_format($date_array[$index], Registry::get('settings.Appearance.date_format'));
        }
        $formated_date_array['date_str']    = implode(', ', $formated_date_array['more']);
        $formated_date_array['first']       = $formated_date_array['more'][0];
        unset($formated_date_array['more'][0]);
    }
    $formated_date_array['total'] = count($date_array);
    return $formated_date_array;
}
/**
 * Get simple order statuses
 *
 * @return array $order_status
 */
function Fn_Settings_Variants_Addons_Ec_Table_Booking_System_Decline_status()
{
    $order_status = fn_get_simple_statuses(STATUSES_ORDER, true, true);
    return $order_status;
}
/**
 * Change booking order status
 *
 * @param mixed $status_to
 * @param mixed $status_from
 * @param mixed $order_info
 * @param mixed $force_notification
 * @param mixed $order_statuses
 * @param mixed $place_order
 *
 * @return void
 */
function Fn_Ec_Table_Booking_System_Change_Order_status($status_to, $status_from, $order_info, $force_notification, $order_statuses, $place_order)
{
    $decline_status     = Registry::get('addons.ec_table_booking_system.cancel_status');
    $order_id           = $order_info['order_id'];
    $booking_status     = array();
    $order_booking_data = db_get_row('SELECT * FROM ?:ec_table_booking_system_booking_info WHERE order_id = ?i', $order_id);
    if (is_array($order_booking_data) && !empty($order_booking_data)) {
        if ($decline_status === $status_to) {
            $booking_status['status'] = 'D';
        } else {
            $booking_status['status'] = 'A';
        }
        db_query('UPDATE ?:ec_table_booking_system_booking_info SET ?u WHERE order_id = ?i', $booking_status, $order_id);
    }
}
/**
 * Updation of data before order placing
 *
 * @param mixed $cart
 * @param mixed $allow
 * @param mixed $product_groups
 *
 * @return void
 */
function Fn_Ec_Table_Booking_System_Pre_Place_order(&$cart, &$allow, &$product_groups)
{
    if (isset($cart['products']) && is_array($cart['products'])) {
        foreach ($cart['products'] as $key => $product_data) {
            if (!empty($product_data['extra']['booking_info'])) {
                $product_id = $product_data['product_id'];
                if (!fn_ec_table_booking_system_check_slot_if_already_booked($product_id, $product_data['extra']['booking_info'])) {
                    fn_set_notification('E', __('error'), __('ec_table_booking_system.booking_already_placed_just'));
                    $allow = false;
                }
            }
        }
    }
}
/**
 * Check if product is a booking product
 *
 * @param mixed $product_id
 *
 * @return string $type
 */
function Fn_Ec_Table_Booking_System_Is_Booking_product($product_id)
{
    $type = db_get_field("SELECT booking_type FROM ?:ec_table_booking_system WHERE product_id = ?i", $product_id);
    return $type;
}
/**
 * Calculate cart content
 *
 * @param mixed $cart
 * @param mixed $cart_products
 * @param mixed $auth
 * @param mixed $apply_cart_promotions
 *
 * @return void
 */
// function Fn_Ec_Table_Booking_System_Calculate_Cart_items(&$cart, &$cart_products, $auth, $apply_cart_promotions)
// {
//     if (isset($cart['products']) && is_array($cart['products'])) {
//         foreach ($cart['products'] as $key => $product_data) {
//             if (!empty($product_data['extra']['booking_info'])) {
//                 $product_id = $product_data['product_id'];
//                 if (!fn_ec_table_booking_system_check_slot_if_already_booked($product_id, $product_data['extra']['booking_info'])) {
//                     fn_set_notification('W', __('warning'), __('ec_table_booking_system.booking_already_reserved_or_expired'));
//                     if(isset($cart['product_groups'])) {
//                         foreach ($cart['product_groups'] as $g_key => $value) {
//                             unset($cart['product_groups'][$g_key]['products'][$key]);
//                         }
//                     }
//                     unset($cart_products[$key]);
//                     unset($cart['products'][$key]);
//                 }
//             }
//         }
//     }
// }

function Fn_Ec_Table_Booking_System_calculate_cart(&$cart, &$cart_products, $auth, $calculate_shipping, $calculate_taxes, $apply_cart_promotions)
{
    if (!empty($cart['products']) && is_array($cart['products'])) {
        foreach ($cart['products'] as $key => $product_data) {
            if (!empty($product_data['extra']['booking_info'])) {
                $product_id = $product_data['product_id'];
                if (!fn_ec_table_booking_system_check_slot_if_already_booked($product_id, $product_data['extra']['booking_info'])) {
                    fn_set_notification('W', __('warning'), __('ec_table_booking_system.booking_already_reserved_or_expired'));
                    // fn_delete_cart_product($cart,$key);
                    // unset($cart_products[$key]);

                    fn_delete_cart_product($cart, $key);

                    if (fn_cart_is_empty($cart) == true) {
                        fn_clear_cart($cart);
                    }

                    fn_save_cart_content($cart, $auth['user_id']);

                    $cart['recalculate'] = true;
                    fn_calculate_cart_content($cart, $auth, 'A', true, 'F', true);

                }
            }
        }
    }
}
function fn_ec_Table_Booking_System_get_all_open_days($days_data) {
    $disable_days = array();
    if(!empty($days_data)) {
        $days_data = unserialize($days_data);
        if($days_data['sunday_status'] == '0')
            $disable_days[] = 0;
        if($days_data['monday_status'] == '0')
            $disable_days[] = 1;
        if($days_data['tuesday_status'] == '0')
            $disable_days[] = 2;
        if($days_data['wednesday_status'] == '0')
            $disable_days[] = 3;
        if($days_data['thursday_status'] == '0')
            $disable_days[] = 4;
        if($days_data['friday_status'] == '0')
            $disable_days[] = 5;
        if($days_data['saturday_status'] == '0')
            $disable_days[] = 6;
    }
    if(!empty($disable_days)) {
        return implode(",",$disable_days);
    }
    return false;
}
function fn_ec_table_booking_get_blocked_date($company_id,$blocked_date) {
    $company_blocked_date = db_get_field("SELECT blocked_date FROM ?:companies WHERE company_id = ?i",$company_id);
    if(!empty($company_blocked_date)) {
        $company_blocked_date = explode(",",$company_blocked_date);
        $blocked_date = explode(",",$blocked_date);
        $blocked_date = array_merge($company_blocked_date,$blocked_date);
        $blocked_date = implode(",",$blocked_date);
    }
    return $blocked_date;
}

function fn_ec_table_booking_get_blocked_date_product_id($blocked_date) {
    if(!empty($blocked_date)) {
        $blocked_date = explode(",",$blocked_date);
        foreach($blocked_date as $key => $item) {
            $startDate = str_replace('/', '-', $item);
            $blocked_date[$key] = date('Y-m-d',strtotime($startDate));

        }
    }
    return $blocked_date;
}

function Fn_Ec_Table_Booking_System_Get_Already_Booked_slot($product_id)
{
    $booking_info = db_get_row('SELECT * FROM ?:ec_table_booking_system WHERE product_id = ?i', $product_id);
    if ($booking_info['booking_type'] == 'R') {
        $all_disabled_dates = array('formated_dates' => array(),'time_stamp'=> array());
        $booked_time        = db_get_array('SELECT * FROM ?:ec_table_booking_system_booking_info WHERE status = ?s AND product_id = ?i AND booking_type = ?s', 'A', $product_id,'R');
        foreach ($booked_time as $key => $order_dates) {
            $date_slots = unserialize($order_dates['booking_info']);
            if ($date_slots['booking_type'] == 'R') {
                $booking_date = explode("to",$date_slots['booking_date']);
                $dates = fn_ec_table_booking_get_between_dates($booking_date[0], $booking_date[1]);
                if(is_array($dates)) {
                    foreach ($dates as $key => $booked_dates) {
                        $all_disabled_dates['time_stamp'][]     = strtotime($booked_dates);
                        $all_disabled_dates['formated_dates'][] = fn_date_format(strtotime($booked_dates), '%m/%d/%Y');
                    }
                }
            }
        }
        return $all_disabled_dates;
    }
    return false;
}


function Fn_Ec_Table_Booking_System_Get_Already_Booked_Slot_By_Table_booking($product_id,$booking_type)
{
    $available_booking_data = db_get_row("SELECT * FROM ?:ec_table_booking_system WHERE product_id = ?i",$product_id);
    $days_data = unserialize($available_booking_data['days_data']);
    $available_days_slot = array();
    if(isset($days_data['sunday_status']) == '1') {
        $total_amount = 0;
        if(!empty($days_data['sunday']['time_by_amount'])) {
            foreach($days_data['sunday']['time_by_amount'] as $key => $item) {
                $total_amount += $item['amount'];
            }
        }
        $available_days_slot['sunday'] = $total_amount;
    }
    if(isset($days_data['monday_status']) == '1') {
        $total_amount = 0;
        if(!empty($days_data['monday']['time_by_amount'])) {
            foreach($days_data['monday']['time_by_amount'] as $key => $item) {
                $total_amount += $item['amount'];
            }
        }
        $available_days_slot['monday'] = $total_amount;
    }
    if(isset($days_data['tuesday_status']) == '1') {
        $total_amount = 0;
        if(!empty($days_data['tuesday']['time_by_amount'])) {
            foreach($days_data['tuesday']['time_by_amount'] as $key => $item) {
                $total_amount += $item['amount'];
            }
        }
        $available_days_slot['tuesday'] = $total_amount;
    }
    if(isset($days_data['wednesday_status']) == '1') {
        $total_amount = 0;
        if(!empty($days_data['wednesday']['time_by_amount'])) {
            foreach($days_data['wednesday']['time_by_amount'] as $key => $item) {
                $total_amount += $item['amount'];
            }
        }
        $available_days_slot['wednesday'] = $total_amount;
    }
    if(isset($days_data['thursday_status']) == '1') {
        $total_amount = 0;
        if(!empty($days_data['thursday']['time_by_amount'])) {
            foreach($days_data['thursday']['time_by_amount'] as $key => $item) {
                $total_amount += $item['amount'];
            }
        }
        $available_days_slot['thursday'] = $total_amount;
    }
    if(isset($days_data['friday_status']) == '1') {
        $total_amount = 0;
        if(!empty($days_data['friday']['time_by_amount'])) {
            foreach($days_data['friday']['time_by_amount'] as $key => $item) {
                $total_amount += $item['amount'];
            }
        }
        $available_days_slot['friday'] = $total_amount;
    }
    if(isset($days_data['saturday_status']) == '1') {
        $total_amount = 0;
        if(!empty($days_data['saturday']['time_by_amount'])) {
            foreach($days_data['saturday']['time_by_amount'] as $key => $item) {
                $total_amount += $item['amount'];
            }
        }
        $available_days_slot['saturday'] = $total_amount;
    }
    $selected_date_format  = fn_date_format(TIME, "%Y-%m-%d");
    $booked_order = db_get_array("SELECT start_date,SUM(quantity) AS quantity FROM ?:ec_table_booking_system_booking_info WHERE product_id = ?i AND booking_type = ?s AND DATE(start_date) >= ?s GROUP BY start_date", $product_id,$booking_type,$selected_date_format);

    $reserved_dates = array();
    if(!empty($booked_order)) {
        foreach($booked_order as $key => $item) {
            $timestamp = strtotime($item['start_date']);
            $day = strtolower(date('l', $timestamp));
            if(!empty($available_days_slot[$day])) {
                if($available_days_slot[$day] <= $item['quantity']) {
                    $reserved_dates[] = $item['start_date'];
                }
            }
        }
    }

    return $reserved_dates;
}



function fn_get_ec_table_booking_system_settings($company_id = null)
{
    static $cache;

    if (!isset($cache['settings_' . $company_id])) {
        $settings = Settings::instance()->getValue('ec_table_booking_system', '', $company_id);
        $settings = unserialize($settings);

        if (empty($settings)) {
            $settings = array();
        }

        $cache['settings_' . $company_id] = $settings;
    }

    return $cache['settings_' . $company_id];
}

function fn_ec_get_special_booking_price_by_product_id($product_id) {

    $price = db_get_array("SELECT * FROM ?:ec_table_booking_system_price WHERE product_id = ?i",$product_id);
    if(!empty($price)) {
        foreach($price as $key => $item) {
            $price[$key]['price'] = fn_format_price_by_currency($item['price'],CART_PRIMARY_CURRENCY,CART_SECONDARY_CURRENCY);
        }
    }
    return $price;
}

function fn_ec_table_booking_system_generate_filter_field_params(&$params, $filters, $selected_filters, $filter_fields, $filter, $structure){
    if ($structure['condition_type'] == FilterTypes::DATEFILTER) {
        $params['ec_date'] = $selected_filters[$filter['filter_id']];
    }
}
function fn_ec_table_booking_system_get_product_filter_fields(array &$filters){
    $filters[FilterTypes::DATEFILTER] = array(
      'condition_type' => FilterTypes::DATEFILTER,
      'description' => 'ec_date_range_filter',
      'slider' => true,
  );
}

function fn_ec_table_booking_system_get_current_filters_post(array $params, array &$filters, array $selected_filters, $area, $lang_code, array $variant_values, array $range_values, array &$field_variant_values, array $field_range_values)
{
    foreach ($filters as &$filter) {
        if ($filter['field_type'] == FilterTypes::DATEFILTER) {
            if(!empty($selected_filters[$filter['filter_id']])) {
                $field_variant_values[$filter['filter_id']]['variants'] = array(
                    'left' =>$selected_filters[$filter['filter_id']][0],
                    'right' =>$selected_filters[$filter['filter_id']][1],
                );
            }
            else {
                $field_variant_values[$filter['filter_id']]['variants'] = array();
            }
        }
    }
}

function fn_ec_table_booking_system_get_additional_information(&$product, $product_data) {
    if(!empty($product_data['product_data'])) {
        foreach($product_data['product_data'] as $key => $data) {
            if(!empty($data['booking_info']['booking_date'])) {
                $price = 0;
                if ($data['booking_info']['booking_type'] == 'T') {
                    $dates = $data['booking_info']['booking_date'];
                    $price = fn_ec_table_booking_price_wise_product_price($dates,$product['product_id']);
                }
                elseif($data['booking_info']['booking_type'] == 'R') {
                    $dates_selected = explode("to",$data['booking_info']['booking_date']);
                    $dates = fn_ec_table_booking_get_between_dates($dates_selected[0],$dates_selected[1]);
                    $product['booking_amount'] = count($dates);
                    $dates = implode("|",$dates);
                    $price = fn_ec_table_booking_price_wise_product_price($dates,$product['product_id']);
                }
                $product['price'] = $price;
            }
        }
    }
}

function fn_Ec_table_booking_system_get_all_slots_by_amount($request_data)
{
    $day = $request_data['day'];
    $book_slot = $request_data['book_slot'];
    $break_slot = $request_data['break_Slot'];
    $start_time_in_24_hour_format = date("H:i:s", strtotime($request_data['start_time']));
    $end_time_in_24_hour_format = date("H:i:s", strtotime($request_data['end_time']));
    $selected_date = date('Y-m-d', TIME);
    $start_time = $selected_date . ' ' . $start_time_in_24_hour_format;
    $end_time   = $selected_date . ' ' . $end_time_in_24_hour_format;
    $booking_info = array();
    list($available_time_slots, $unavailable_time_slots) = fn_ec_table_booking_system_time_slots_array($start_time, $end_time, $book_slot, $break_slot, $booking_info);
    if(!empty($available_time_slots)) {
        return $available_time_slots;
    }
    else {
        return false;
    }
}



function fn_ec_table_booking_system_update_company_pre(&$company_data, $company_id, $lang_code, $can_update) {

    try {
        if(!empty($company_data['update_products'])) {
            $products = explode(",",$company_data['update_products']);
            if(!empty($company_data['booking_data'])) {
                $booking_data = $company_data['booking_data'];
                foreach($products as $key => $item) {
                    fn_ec_table_booking_system_update_booking_data($booking_data, $item);
                    $new_booking_data = db_get_field("SELECT booking_data FROM ?:companies WHERE company_id = ?i",$company_id);
                    $new_booking_data = unserialize($new_booking_data);
                    $d_booking_data = array(
                        'days_data' => $new_booking_data['days_data']
                    );
                    db_query('UPDATE ?:ec_table_booking_system SET ?u WHERE product_id = ?i', $d_booking_data, $item);
                }
                fn_set_notification('N', __('notice'), __('ec_table_booking_system.product_update_sucessfullly'));
            }
        }

        if (isset($company_data['booking_data']) && !empty($company_data['booking_data'])) {
            $booking_data = $company_data['booking_data'];
            $company_data['booking_data'] = array();
            $param_data = array(
                'booking_type' => '',
                'from_date'    => TIME,
                'to_date'      => TIME,
                'slot_time'    => '0',
                'free_time'   => '0',
                'days_data'    => '',
                'quantity_selector'    => '',
            );
            $f= 1;
            $from_date = fn_parse_date($booking_data['from_date']);
            $to_date = fn_parse_date($booking_data['to_date']);
            $date_curr = strtotime('today midnight');
            if(isset($booking_data['booking_type']) && $booking_data['booking_type'] == 'N') {
                $booking_data = array_merge($param_data, $booking_data);
                $company_data['booking_data'] = $booking_data;
            }
            if(isset($booking_data['booking_type']) && $booking_data['booking_type'] == 'R') {
                $service_data  = $booking_data['R'];
                $service_data['blocked_date']  = $booking_data['blocked_date'];
                $service_data['booking_type'] = 'R';
                $service_data['quantity_selector'] = 'Y';
                $service_data['show_price_date'] = $booking_data['show_price_date'];
                $from_date = fn_parse_date($service_data['from_date']);
                $to_date = fn_parse_date($service_data['to_date']);
                $date_curr = strtotime('today midnight');
                $service_data['from_date']  = isset($service_data['from_date'])? fn_parse_date($service_data['from_date']):'';
                $service_data['to_date']    = isset($service_data['to_date'])? fn_parse_date($service_data['to_date']):'';
                $service_data               = array_merge($param_data, $service_data);

                foreach($service_data['price_wise'] as $key => $item) {
                    if(empty($item['price'])) {
                        unset($service_data['price_wise'][$key]);
                    }
                }

                $company_data['booking_data'] = $service_data;
            }
            else {
                $f = 0;
                $from_date = fn_parse_date($booking_data['from_date']);
                $to_date = fn_parse_date($booking_data['to_date']);
                $date_curr = strtotime('today midnight');
                if(!isset($booking_data['sunday_status'])) {
                    $booking_data['sunday_status'] = 0;
                }
                if(!isset($booking_data['monday_status'])) {
                    $booking_data['monday_status'] = 0;
                }
                if(!isset($booking_data['tuesday_status'])) {
                    $booking_data['tuesday_status'] = 0;
                }
                if(!isset($booking_data['wednesday_status'])) {
                    $booking_data['wednesday_status'] = 0;
                }
                if(!isset($booking_data['thursday_status'])) {
                    $booking_data['thursday_status'] = 0;
                }
                if(!isset($booking_data['friday_status'])) {
                    $booking_data['friday_status'] = 0;
                }
                if(!isset($booking_data['saturday_status'])) {
                    $booking_data['saturday_status'] = 0;
                }
                if (isset($booking_data['sunday_status'])) {
                    $days_data = array(
                    'sunday_status'               => $booking_data['sunday_status'],
                    'sunday_timing_start_time'    => $booking_data['sunday_timing_start_time'],
                    'sunday_timing_end_time'      => $booking_data['sunday_timing_end_time'],
                    'monday_status'               => $booking_data['monday_status'],
                    'monday_timing_start_time'    => $booking_data['monday_timing_start_time'],
                    'monday_timing_end_time'      => $booking_data['monday_timing_end_time'],
                    'tuesday_status'              => $booking_data['tuesday_status'],
                    'tuesday_timing_start_time'   => $booking_data['tuesday_timing_start_time'],
                    'tuesday_timing_end_time'     => $booking_data['tuesday_timing_end_time'],
                    'wednesday_status'            => $booking_data['wednesday_status'],
                    'wednesday_timing_start_time' => $booking_data['wednesday_timing_start_time'],
                    'wednesday_timing_end_time'   => $booking_data['wednesday_timing_end_time'],
                    'thursday_status'             => $booking_data['thursday_status'],
                    'thursday_timing_start_time'  => $booking_data['thursday_timing_start_time'],
                    'thursday_timing_end_time'    => $booking_data['thursday_timing_end_time'],
                    'friday_status'               => $booking_data['friday_status'],
                    'friday_timing_start_time'    => $booking_data['friday_timing_start_time'],
                    'friday_timing_end_time'      => $booking_data['friday_timing_end_time'],
                    'saturday_status'             => $booking_data['saturday_status'],
                    'saturday_timing_start_time'  => $booking_data['saturday_timing_start_time'],
                    'saturday_timing_end_time'    => $booking_data['saturday_timing_end_time']
                    );
                    $diff = [];
                    //change for invalid book time start
                    if ($days_data['sunday_status']=='1') {
                        $diff[]=strtotime($days_data['sunday_timing_end_time'])-strtotime($days_data['sunday_timing_start_time']);
                    }
                    if ($days_data['monday_status']=='1') {
                        $diff[]=strtotime($days_data['monday_timing_end_time'])-strtotime($days_data['monday_timing_start_time']);
                    }
                    if ($days_data['tuesday_status']=='1') {
                        $diff[]=strtotime($days_data['tuesday_timing_end_time'])-strtotime($days_data['tuesday_timing_start_time']);
                    }
                    if ($days_data['wednesday_status']=='1') {
                        $diff[]=strtotime($days_data['wednesday_timing_end_time'])-strtotime($days_data['wednesday_timing_start_time']);
                    }
                    if ($days_data['thursday_status']=='1') {
                        $diff[]=strtotime($days_data['thursday_timing_end_time'])-strtotime($days_data['thursday_timing_start_time']);
                    }
                    if ($days_data['friday_status']=='1') {
                        $diff[]=strtotime($days_data['friday_timing_end_time'])-strtotime($days_data['friday_timing_start_time']);
                    }
                    if ($days_data['saturday_status']=='1') {
                        $diff[]=strtotime($days_data['saturday_timing_end_time'])-strtotime($days_data['saturday_timing_start_time']);
                    }
                    if(!empty($diff)){
                        $t_diff=min($diff)/60;
                    } else {
                        $t_diff=0;
                    }
                }

                $new_booking_data = db_get_field("SELECT booking_data FROM ?:companies WHERE company_id = ?i",$company_id);
                $new_booking_data = unserialize($new_booking_data);
                $new_days_data = unserialize($new_booking_data['days_data']);
                if ($new_days_data) {
                    $days_data = array_merge($new_days_data, $days_data);
                }
                $booking_data['days_data'] = serialize($days_data);
                if (!is_numeric($booking_data['slot_time'])) {
                    $booking_data['slot_time'] = 0;
                    fn_set_notification('W', __('warning'), __('ec_table_booking_system.please_set_booking_time_numeric_only'));
                }
                if (!is_numeric($booking_data['free_time'])) {
                    $booking_data['free_time'] = 0;
                    fn_set_notification('W', __('warning'), __('ec_table_booking_system.please_set_break_time_numeric_only'));
                }

                if ($f == 0) {
                    $booking_data['from_date']  = isset($booking_data['from_date'])? fn_parse_date($booking_data['from_date']):'';
                    $booking_data['to_date']    = isset($booking_data['to_date'])? fn_parse_date($booking_data['to_date']):'';
                    $booking_data               = array_merge($param_data, $booking_data);
                    foreach($booking_data['R']['price_wise'] as $key => $item) {
                        if(empty($item['price'])) {
                            unset($booking_data['R']['price_wise'][$key]);
                        }
                    }
                    $booking_data['price_wise'] = $booking_data['R']['price_wise'];

                    if(!empty($booking_data['R']['price_wise'])) {
                        unset($booking_data['R']['price_wise']);
                    }
                    $company_data['booking_data'] = $booking_data;

                }
            }

            if(!empty($company_data['booking_data'])) {
                $company_data['booking_data'] = serialize($company_data['booking_data']);
            }
        }
    }
    catch(Exception $e){
        fn_set_notification("E",__("error"),$e->getMessage());
    }
}

function fn_ec_table_booking_system_get_company_data_post($company_id, $lang_code, $extra, &$company_data) {
    if(!empty($company_data['booking_data'])) {
        $company_data['booking_data'] = unserialize($company_data['booking_data']);
    }
}
