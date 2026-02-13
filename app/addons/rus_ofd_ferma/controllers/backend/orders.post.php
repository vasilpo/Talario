<?php

use Tygh\Registry;
use Tygh\Addons\OfdFerma\OfdFerma;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $suffix = ".details?order_id=$_REQUEST[order_id]";
    
    $ofdferma = Tygh::$app['addons.rus_ofd_ferma.ofd_ferma'];
    
    $response = null;
    if (isset($_REQUEST['order_id']) && $_REQUEST['order_id']){
        if ($mode == 'income_ofd_ferma') {

            $response = $ofdferma->OFDcreate($_REQUEST['order_id']);
        }else
        if ($mode == 'income_return_ofd_ferma') {
            
            $response = $ofdferma->OFDcreate($_REQUEST['order_id'], 'IncomeReturn');
        }
    }
    
    if ($response){
        fn_rus_ofd_ferma_show_notify($response);
            
        return array(CONTROLLER_STATUS_OK, 'orders' . $suffix);
    }
}

if ($mode == 'details') {

    Registry::set('navigation.tabs.ofd_ferma', array (
        'title' => __('ofd_ferma'),
        'js'    => true
    ));
}