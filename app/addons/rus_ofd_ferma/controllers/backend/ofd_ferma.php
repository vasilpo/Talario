<?php

use Tygh\Registry;
use Tygh\Addons\OfdFerma\OfdFerma;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

/** @var string $mode */

if ($mode === 'receipts') {
    
    $ofdferma = Tygh::$app['addons.rus_ofd_ferma.ofd_ferma'];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_REQUEST['update'])){ 
            $response = $ofdferma->UpdateChecksStatus(array($_REQUEST['update']));
            
            fn_rus_ofd_ferma_show_notify($response, "Для чека {$_REQUEST['update']} статус успешно обновлен");
            
            return array(CONTROLLER_STATUS_OK, 'ofd_ferma.receipts');
        }
    }  
    

    $view = Tygh::$app['view'];

    $search = $conditions = array();
    $limit = null;
    $page = isset($_REQUEST['page']) ? (int) $_REQUEST['page'] : 1;

    if (isset($_REQUEST['items_per_page'])) {
        $limit = (int) $_REQUEST['items_per_page'];
    }

    if (!$limit) {
        $limit = (int) Registry::ifGet('settings.Appearance.admin_elements_per_page', 30);
    }

    if (isset($_REQUEST['search']['type']) && !empty($_REQUEST['search']['type'])) {
        $search['type'] = $conditions['type'] = $_REQUEST['search']['type'];
    }

    if (isset($_REQUEST['search']['order_id']) && !empty($_REQUEST['search']['order_id'])) {
        $search['order_id'] = $conditions['order_id'] = $_REQUEST['search']['order_id'];
    }

    
    if (isset($_REQUEST['receipts_period'])) {
        $search['receipts_period'] = $_REQUEST['receipts_period'];
        $search['receipts_time_from'] = $_REQUEST['receipts_time_from'];
        $search['receipts_time_to'] = $_REQUEST['receipts_time_to'];

        if(isset($_REQUEST['receipts_time_from']) && !empty($_REQUEST['receipts_time_from']) ) { 
            $time_from = DateTime::createFromFormat('m/d/Y', $_REQUEST['receipts_time_from']);
            $conditions[] = array('created_at', '>=', $time_from->format('Y-m-d 00:00:00') );
        }

        if(isset($_REQUEST['receipts_time_to']) && !empty($_REQUEST['receipts_time_to']) ) { 
            $time_to = DateTime::createFromFormat('m/d/Y', $_REQUEST['receipts_time_to']);
            $conditions[] = array('created_at', '<=', $time_to->format('Y-m-d 23:59:59') );
        }

    }


    $total_items =  $ofdferma->getCount($conditions);  

    $page = db_get_valid_page($page, $limit, $total_items);
    $offset = ($page - 1) * $limit;

    $receipts = $ofdferma->getList( $offset, $limit, $conditions);  


    $search = array_merge($search, array(
        'items_per_page' => $limit,
        'total_items' => $total_items,
        'page' => $page
    ));

    $view->assign('receipts', $receipts);
    $view->assign('search', $search);
    
    $view->assign('types', array(
        'Income'        => __('rus_ofd_ferma.receipts_list.type.income'),
        'IncomeReturn'  => __('rus_ofd_ferma.receipts_list.type.income_return'),
    ));

} elseif($mode === 'manual'){

    $view = Tygh::$app['view'];
    
    $view->assign('cron_url', fn_url('ofd_ferma.cron', 'C') );    
}