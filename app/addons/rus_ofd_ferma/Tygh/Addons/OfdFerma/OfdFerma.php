<?php

namespace Tygh\Addons\OfdFerma;

use Tygh\Registry;
use Tygh\Addons\RusTaxes\ReceiptFactory;
use Tygh\Addons\RusTaxes\TaxType;
use Tygh\Addons\RusTaxes\Receipt\Item;
use Tygh\Addons\RusTaxes\Receipt\Receipt;
use Tygh\Settings;

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

class OfdFerma {

    private $_debug = 0;

    private function checkExists($order_id, $type) {
        return db_get_field("SELECT COUNT(*) FROM `?:rus_ofd_ferma_receipts` WHERE `order_id` = ?i AND `type` = ?s ", $order_id, $type);
    }

    public function setDebug($val) {
        $this->_debug = $val;
    }

    public function log_msg($mess, $status = false, $params = array()) {

        if (!$status && $this->_debug && $mess) {
            file_put_contents( $_SERVER['DOCUMENT_ROOT'].'/var/ofd_ferma.log', "[".date("Y-m-d H:i:s").'] '. (string)$mess ."\r\n", FILE_APPEND); 
            
            $ofd_email = Registry::get('addons.rus_ofd_ferma.setting_email');
            if ($ofd_email) {
                fn_rus_ofd_ferma_send($ofd_email, 'OFD Ferma - Ошибка', $mess);
            }
        }

        return array_merge(array(
            'status'    => $status,
            'mess'      => $mess
        ), $params);
    }

    public function getTextType($type) {
        $res = '';

        switch ($type) {
            case 'Income':
                $res = 'Приход';
                break;

            case 'IncomeReturn':
                $res = 'Возврат';
                break;
        }

        return $res;
    }

    private function formatFloat( $int, $count = 2){
        return	round( (float)$int , $count);
    }
    
    public function prepareData($order_data, $type) {

        $prefix     = 'o';
        
        $ofd_inn    = Registry::get('addons.rus_ofd_ferma.setting_inn');
        $ofd_nalog  = Registry::get('addons.rus_ofd_ferma.setting_nalog');
        $ofd_nds    = Registry::get('addons.rus_ofd_ferma.setting_nds');
        
        //свертка
        $ofd_collapse      = Registry::get('addons.rus_ofd_ferma.setting_collapse');
		if ('Y' == $ofd_collapse){
			$ofd_collapse = TRUE;
		}else{
			$ofd_collapse = FALSE;
		}
		
        $ofd_collapse_name = Registry::get('addons.rus_ofd_ferma.setting_collapse_name');
        
        $receipt_factory = new ReceiptFactory( 'RUB', TaxType::getMap(), false );        
        $receipt = $receipt_factory->createReceiptFromOrder($order_data, 'RUB', false);
        
        if (is_null($receipt)) {
            return [];
        }

        // Get Order
        $id = $order_data['order_id'];
        $customer_phone = $receipt->getPhone(); 
        $customer_email = $receipt->getEmail();

        $products = $receipt->getItems();

        if (!$products) {
            return $this->log_msg("В заказе #{$order_id} нет товаров");
        }

        //Формируем данные
        $data = array();
        $data['Request']['Inn'] = $ofd_inn;
        $data['Request']['Type'] = $type;
        $data['Request']['InvoiceId'] = $prefix . $id . '-' . $type;
        $data['Request']['LocalDate'] = date('Y-m-d\TH:i:s');
        $data['Request']['CustomerReceipt'] = array(
            'TaxationSystem' => $ofd_nalog,
            'Email' => $customer_email,
            'Phone' => $customer_phone,
            'Items' => array(),
        );

       
        // Get Items: Price / Sold / Email
        foreach ($products as $item) {
            $vat = $item->getTaxType();
			
			SWITCH($vat){
				case 'vat0':
					$vat = 'Vat0';
				break;
				
				case 'vat10':
					$vat = 'Vat10';
				break;
				
				case 'vat18':
					$vat = 'Vat18';
				break;
				
				case 'vat110':
					$vat = 'CalculatedVat10110';
				break;
				
				case 'vat118':
					$vat = 'CalculatedVat18118';
				break;
				
				default:
					$vat = $ofd_nds;
			}
			
            array_push($data['Request']['CustomerReceipt']['Items'], array(
                    'Label'     => $item->getName(),
                    'Price'     => $this->formatFloat( $item->getPrice() ),
                    'Quantity'  => $this->formatFloat( $item->getQuantity(), 3),
                    'Amount'    => $this->formatFloat( $item->getPrice() * $item->getQuantity() - $item->getTotalDiscount() ),
                    'Vat'       => $vat,
                )
            );
        }

        //Если включена свертка
        if ($ofd_collapse) {
            $pos_name = $ofd_collapse_name ? $ofd_collapse_name : 'Undefined';

            $sum = 0;
            foreach ($data['Request']['CustomerReceipt']['Items'] as $item) {
                $sum += $item['Amount'];
            }
            $data['Request']['CustomerReceipt']['Items'] = array(array(
                    'Label' => $pos_name,
                    'Price' => $sum,
                    'Quantity' => 1,
                    'Amount' => $sum,
                    'Vat' => $ofd_nds,
            ));
        }
		
        return $data;
    }

    public function customErrorHandler($errno, $errstr, $errfile, $errline, array $errcontext) {
        $this->log_msg($errstr);

        if (0 === error_reporting()) {
            return false;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    private function checkSettings() {
        $ofd_auth_url   = Registry::get('addons.rus_ofd_ferma.setting_auth_url');
        $ofd_api_url    = Registry::get('addons.rus_ofd_ferma.setting_api_url');
        $ofd_login      = Registry::get('addons.rus_ofd_ferma.setting_login');
        $ofd_pass       = Registry::get('addons.rus_ofd_ferma.setting_password');
        $ofd_nalog      = Registry::get('addons.rus_ofd_ferma.setting_nalog');
        $ofd_inn        = Registry::get('addons.rus_ofd_ferma.setting_inn');
        $ofd_nds        = Registry::get('addons.rus_ofd_ferma.setting_nds');

        return (
            $ofd_auth_url &&
            $ofd_api_url &&
            $ofd_login &&
            $ofd_pass &&
            $ofd_nalog &&
            $ofd_inn &&
            $ofd_nds
        );
    }

    private function checkToken() {
        $ofd_token          = Registry::get('addons.rus_ofd_ferma.setting_token');
        $ofd_token_exp_date = Registry::get('addons.rus_ofd_ferma.setting_token_exp_date');
        
        if ($ofd_token && ($ofd_token_exp_date > (time() - 10))) {
            return $ofd_token;
        } else {
            return false;
        }
    }

    private function getHTTPOpt($data) {
        $options = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
            'http' => array(
                'timeout' => 10,
                'ignore_errors' => true,
                'content' => json_encode($data),
                'header' => "Content-type: application/json\r\n" .
                "Accept: application/json" . "\r\n",
                "Content-Length: " . strlen(json_encode($data)) . "\r\n",
                'method' => 'POST',
            )
        );

        return $options;
    }

    public function UpdateChecksStatus($ids = array()) {
        $sWhere = '';
        if ($ids) {
            $aWhere = array();
            foreach ($ids as $id) {
                $aWhere[] = "'" . addslashes($id) . "'";
            }

            $sWhere = ' && `id` IN (' . implode(',', $aWhere) . ')';
        }

        $results = db_get_array("SELECT * FROM `?:rus_ofd_ferma_receipts` WHERE (`status` IS NULL OR `status` <> 'CONFIRMED' OR `status` <> 'FAILED')" . $sWhere);
         
        $res = null;
        foreach ($results as $result) {
            
            $data = array();
            $data['Request']['ReceiptId'] = $result['id'];
            if ($data_ins = $this->UpdateNewCheckStatus($data)) {
                $res = $this->UpdateNewCheckInDB($result['id'], $data_ins);
            } elseif ($data_ins = $this->UpdateOldCheckStatus($data)) {
                $res = $this->UpdateOldCheckInDB($result['id'], $data_ins);
            } else {
                $res = $this->UpdateFailedCheckInDB($result['id']);
            }
        }
        
        if ($ids && count($ids)==1){
            return $res;
        }
    }

    private function UpdateOldCheckStatus($data) {
        $res = $this->setAuthToken();
        if (!$res['status']) {
            return $res;
        }

        $ofd_api_url    = Registry::get('addons.rus_ofd_ferma.setting_api_url');
        $ofd_token      = Registry::get('addons.rus_ofd_ferma.setting_token');
        
        $options = $this->getHTTPOpt($data);
        $context = stream_context_create($options);
        set_error_handler(array($this, 'customErrorHandler'));
        try {
            $result = file_get_contents($ofd_api_url . "/list?AuthToken=" . $ofd_token, false, $context);
        } catch (Exception $e) {
            return $this->log_msg($e->getMessage());
        }
        restore_error_handler();
        $result = json_decode($result);
        if (isset($result->Status) && ($result->Status == 'Success')) {
            return $result->Data->ReceiptId;
        } else if (isset($result->Status) && ($result->Status == 'Failed')) {
            return $this->log_msg($result->Error->Message);
        } else {
            return $this->log_msg('some error');
        }
    }

    private function UpdateNewCheckStatus($data) {
        $res = $this->setAuthToken();
        if (!$res['status']) {
            return $res;
        }

        $ofd_api_url    = Registry::get('addons.rus_ofd_ferma.setting_api_url');
        $ofd_token      = Registry::get('addons.rus_ofd_ferma.setting_token');

        $options = $this->getHTTPOpt($data);
        $context = stream_context_create($options);
        set_error_handler(array($this, 'customErrorHandler'));
        try {
            $result = file_get_contents($ofd_api_url . "/status?AuthToken=" . $ofd_token, false, $context);
        } catch (Exception $e) {
            return $this->log_msg($e->getMessage());
        }
        restore_error_handler();
        $result = json_decode($result);
        if (isset($result->Status) && ($result->Status == 'Success')) {
            return $result->Data;
        } else if (isset($result->Status) && ($result->Status == 'Failed')) {
            return $this->log_msg($result->Error->Message);
        } else {
            return $this->log_msg('some error');
        }
    }

    private function UpdateNewCheckInDB($check_id, $data) {

        $aDataSave = array(
            "status" => $data->StatusName,
            "status_message" => $data->StatusMessage,
            "FN" => $data->Device->FN,
            "RNM" => $data->Device->RNM,
            "FDN" => $data->Device->FDN,
            "FPD" => $data->Device->FPD,
            "updated_at" => gmdate('Y-m-d H:i:s'),
        );

        $aDataUpdate = array();
        foreach ($aDataSave as $key => $value) {
            $aDataUpdate[] = "`$key` = '" . addslashes($value) . "'";
        }

        if ($aDataUpdate) {
            $sql = "UPDATE `?:rus_ofd_ferma_receipts` SET " . implode(',', $aDataUpdate) . " WHERE `id` = '{$check_id}'";
            db_query($sql);
        }
        
        return $this->log_msg('', true);
    }

    private function UpdateFailedCheckInDB($check_id) {
        
    }

    public function setAuthToken() {
        if (!$this->checkSettings()) {
            return $this->log_msg('Для корректной работы заполните необходимы настройки');
        };

        if ($this->checkToken()) {
            return $this->log_msg('', true);
        }

        $ofd_auth_url  = Registry::get('addons.rus_ofd_ferma.setting_auth_url');
        $ofd_login     = Registry::get('addons.rus_ofd_ferma.setting_login');
        $ofd_pass      = Registry::get('addons.rus_ofd_ferma.setting_password');

        $data = array(
            "Login"     => $ofd_login,
            "Password"  => $ofd_pass,
        );

        $options = $this->getHTTPOpt($data);
        $context = stream_context_create($options);
        set_error_handler(array($this, 'customErrorHandler'));

        try {
            $result = file_get_contents($ofd_auth_url, false, $context);
        } catch (Exception $e) {
            return $this->log_msg($e->getMessage());
        }

        restore_error_handler();
        $result = json_decode($result);

        if (isset($result->Status) && ($result->Status == 'Success')) {
            //Обновляем токен
            Settings::instance()->updateValue('setting_token', $result->Data->AuthToken, 'rus_ofd_ferma');
            Registry::set('addons.rus_ofd_ferma.setting_token', $result->Data->AuthToken);
            
            Settings::instance()->updateValue('setting_token_exp_date', $result->Data->ExpirationDateUtc, 'rus_ofd_ferma');
            Registry::set('addons.rus_ofd_ferma.setting_token_exp_date', $result->Data->ExpirationDateUtc);
            
            return $this->log_msg('', true);
        } else if (isset($result->Status) && ($result->Status == 'Failed')) {
            return $this->log_msg($result->Error->Message);
        } else {
            return $this->log_msg('some error');
        }
    }

    private function getErrorByCode($code_id, $data) {

        $msg = '';

        SWITCH ($code_id) {
            case 1019:
                $msg = "Идентификатор счета '{$data['Request']['InvoiceId']}' уже существует в ОФД";
                break;
        }

        return $msg;
    }

    public function sendDataToOFD($data) {

        $res = $this->setAuthToken();
        if (!$res['status']) {
            return $res;
        }

        $ofd_token   = Registry::get('addons.rus_ofd_ferma.setting_token');
        $ofd_api_url = Registry::get('addons.rus_ofd_ferma.setting_api_url');

        $options = $this->getHTTPOpt($data);
        $context = stream_context_create($options);

        set_error_handler(array($this, 'customErrorHandler'));
        try {
            $result = file_get_contents($ofd_api_url . "/receipt?AuthToken=" . $ofd_token, false, $context);
        } catch (Exception $e) {
            return $this->log_msg($e->getMessage());
        }

        restore_error_handler();
        $result = json_decode($result);

        if (isset($result->Status) && ($result->Status == 'Success')) {
            return $this->log_msg('', true, array('check_id' => $result->Data->ReceiptId));
        } else if (isset($result->Status) && ($result->Status == 'Failed')) {
            if ($result->Error->Code) {
                if ($val = $this->getErrorByCode($result->Error->Code, $data)) {
                    return $this->log_msg($val);
                }
            }

            return $this->log_msg($result->Error->Message);
        } else {
            return $this->log_msg('some error');
        }
    }

    private function saveCheckInDB($check_id, $order_id, $data, $total) {
        try {
            $sql = "INSERT INTO `?:rus_ofd_ferma_receipts` (id, type, inn, order_id, total, created_at) "
                    . "VALUES ('{$check_id}','{$data['Request']['Type']}','{$data['Request']['Inn']}','{$order_id}','{$total}','" . gmdate('Y-m-d H:i:s') . "')";

            $check_item_id = db_query($sql);
            
            return $this->log_msg('', true, array('check_item_id' => $check_item_id));
        } catch (Exception $e) {
            return $this->log_msg($e->getMessage());
        }
    }

    public function OFDcreate($order_id, $type = 'Income') {
        
        $order_data = fn_get_order_info($order_id);
        if( ! isset($order_data) || empty($order_data)) {
            return $this->log_msg("Заказ #{$order_id} не существует");
        }

        //Тип чека
        $temptype = $this->getTextType($type);


        if ($this->checkExists($order_id, $type)) {
            return $this->log_msg("Чек {$temptype} для заказа #{$order_id} уже оформлен");
        } else {
            //Формируем чек
            $data = $this->prepareData($order_data, $type);

            if (empty($data)) {
                return [];
            }

            if (isset($data['status']) && !$data['status']) {
                return $data;
            }

            $ans = $this->sendDataToOFD($data);
            if (isset($ans['check_id']) && !empty($ans['check_id'])) {
                $save = $this->saveCheckInDB($ans['check_id'], $order_id, $data, $order_data['total']);
                if (!$save['check_item_id']) {
                    return $this->log_msg("Ошибка сохранения чека {$temptype} для заказа #{$order_id}");
                }

                return $this->log_msg('Чек ' . $temptype . ' для заказа #' . $order_id . ' успешно зарегистрирован.', true);
            }

            return $ans;
        }
    }

    public function getCount($where = array()) {
        
        if (empty($where)) {
            return db_get_field("SELECT COUNT(*) FROM `?:rus_ofd_ferma_receipts` ");
        } else {
            return db_get_field("SELECT COUNT(*) FROM `?:rus_ofd_ferma_receipts`  WHERE ?w ", $where);
        }
    }

    public function getList($offset = 0, $countPage = 30, $where = array()) {

        if (!empty($where)) {
            $list = db_get_array("SELECT * FROM `?:rus_ofd_ferma_receipts` WHERE ?w ORDER BY `created_at` DESC LIMIT ?i , ?i ", $where, $offset, $countPage);
        } else {
            $list = db_get_array("SELECT * FROM `?:rus_ofd_ferma_receipts` ORDER BY `created_at` DESC LIMIT ?i , ?i ", $offset, $countPage);
        }

        if ($list){
            $ofd_inn            = Registry::get('addons.rus_ofd_ferma.setting_inn');
            $ofd_check_url      = Registry::get('addons.rus_ofd_ferma.setting_check_url');
            $ofd_check_url_demo = Registry::get('addons.rus_ofd_ferma.setting_check_url_demo');
            
            foreach ($list as &$item) {
                
                $item['update'] = FALSE;
                $item['type_name'] = $this -> getTextType($item['type']);
                $item['created_at'] = date('d.m.Y h:i:s', strtotime(($item['created_at'])));
                        
                if ('CONFIRMED' == $item['status']){
                    if ($ofd_check_url_demo){
                        $item['id_link'] = "'{$ofd_check_url_demo}".ltrim($item['FDN'],0)."/{$item['FPD']}'";
                    }else{
                        $inn = $item['inn'];
                        if ( ! $inn){
                            $inn = $ofd_inn;
                        }
                        
                        $item['id_link'] = "'{$ofd_check_url}{$inn}/{$item['RNM']}/{$item['FN']}/".ltrim($item['FDN'],0)."/{$item['FPD']}'";
                    }
                }else{
                    $item['update'] = TRUE;
                }

            }
        }

        return $list;
    }

}
