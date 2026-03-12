<?php

namespace Tygh\Addons\OfdFerma;

use ErrorException;
use Exception;
use Tygh\Registry;
use Tygh\Addons\RusTaxes\ReceiptFactory;
use Tygh\Addons\RusTaxes\TaxType;
use Tygh\Settings;

class OfdFerma
{
    /** @var bool Enables verbose error logging. */
    private $debug = false;

    private const PAYMENT_AGENT_TYPE = 'AGENT';

    /**
     * Checks whether a receipt with the same order and type already exists.
     *
     * @param int    $order_id Order identifier.
     * @param string $type     Receipt type.
     *
     * @return int
     */
    private function checkExists(int $order_id, string $type): int
    {
        return (int) db_get_field(
            "SELECT COUNT(*) FROM `?:rus_ofd_ferma_receipts` WHERE `order_id` = ?i AND `type` = ?s ",
            $order_id,
            $type
        );
    }

    /**
     * Toggles debug logging mode.
     *
     * @param bool|int $val Debug flag value.
     *
     * @return void
     */
    public function setDebug(bool|int $val): void
    {
        $this->debug = (bool) $val;
    }

    /**
     * Builds a standard operation result payload and writes debug log when needed.
     *
     * @param string $mess   Result message.
     * @param bool   $status Operation status.
     * @param array  $params Additional payload fields.
     *
     * @return array<string, mixed>
     */
    public function logMsg(string $mess, bool $status = false, array $params = array()): array
    {
        if (!$status && $this->debug && $mess) {
            file_put_contents(
                $_SERVER['DOCUMENT_ROOT'] . '/var/ofd_ferma.log',
                '[' . date('Y-m-d H:i:s') . '] ' . (string) $mess . "\r\n",
                FILE_APPEND
            );

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

    /**
     * Converts an internal receipt type to a display caption.
     *
     * @param string $type Receipt type code.
     *
     * @return string
     */
    public function getTextType(string $type): string
    {
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

    /**
     * Rounds a numeric value to the required precision.
     *
     * @param float|int $int   Source value.
     * @param int       $count Precision.
     *
     * @return float
     */
    private function formatFloat(float|int $int, int $count = 2): float
    {
        return round((float) $int, $count);
    }

    /**
     * Builds a single-line transfer agent address from marketplace settings.
     *
     * @return string
     */
    private function buildTransferAgentAddress(): string
    {
        // The OFD payload expects a single transfer agent address line.
        $address_parts = array_filter(array(
            Registry::get('settings.Company.company_country'),
            Registry::get('settings.Company.company_city'),
            Registry::get('settings.Company.company_address'),
        ));

        return implode(', ', $address_parts);
    }

    /**
     * Returns marketplace supplier requisites used as a fallback.
     *
     * @return array<string, mixed>
     */
    private function getMarketplaceSupplierData(): array
    {
        // Marketplace details are used as a fallback when vendor requisites are incomplete.
        return array(
            'SupplierInn' => Registry::get('addons.rus_ofd_ferma.setting_inn'),
            'SupplierName' => Registry::get('settings.Company.company_name'),
            'SupplierPhone' => Registry::get('settings.Company.company_phone'),
        );
    }

    /**
     * Resolves the company identifier associated with the order.
     *
     * @param array<string, mixed> $order_data Order data.
     *
     * @return int
     */
    private function getOrderCompanyId(array $order_data): int
    {
        if (!empty($order_data['company_id'])) {
            return (int) $order_data['company_id'];
        }

        if (empty($order_data['products']) || !is_array($order_data['products'])) {
            return 0;
        }

        foreach ($order_data['products'] as $product) {
            if (!empty($product['company_id'])) {
                return (int) $product['company_id'];
            }
        }

        return 0;
    }

    /**
     * Collects supplier requisites with marketplace fallback for missing fields.
     *
     * @param array<string, mixed> $order_data Order data.
     *
     * @return array<string, mixed>
     */
    private function getSupplierData(array $order_data): array
    {
        $marketplace_supplier_data = $this->getMarketplaceSupplierData();
        $company_id = $this->getOrderCompanyId($order_data);

        if (!$company_id) {
            return $marketplace_supplier_data;
        }

        // Vendor fiscal requisites are stored in companies table and merged with marketplace fallback.
        $company_data = db_get_row(
            'SELECT company_id, tax_number, phone FROM ?:companies WHERE company_id = ?i',
            $company_id
        );

        $supplier_data = array(
            'SupplierInn' => !empty($company_data['tax_number']) ? $company_data['tax_number'] : '',
            'SupplierName' => fn_get_company_name($company_id),
            'SupplierPhone' => !empty($company_data['phone']) ? $company_data['phone'] : '',
        );

        foreach ($supplier_data as $key => $value) {
            if ($value === '' || $value === null) {
                $supplier_data[$key] = $marketplace_supplier_data[$key];
            }
        }

        return $supplier_data;
    }

    /**
     * Builds payment agent requisites for the Ferma request.
     *
     * @param array<string, mixed> $order_data Order data.
     *
     * @return array<string, mixed>
     */
    private function buildPaymentAgentInfo(array $order_data): array
    {
        // Marketplace company settings are the source of payment agent attributes.
        return array_merge(array(
            'AgentType' => self::PAYMENT_AGENT_TYPE,
            'TransferAgentPhone' => Registry::get('settings.Company.company_phone'),
            'TransferAgentName' => Registry::get('settings.Company.company_name'),
            'TransferAgentAddress' => $this->buildTransferAgentAddress(),
            'TransferAgentINN' => Registry::get('addons.rus_ofd_ferma.setting_inn'),
        ), $this->getSupplierData($order_data));
    }

    /**
     * Builds the Ferma receipt payload for the specified order.
     *
     * @param array<string, mixed> $order_data Order data.
     * @param string               $type       Receipt type.
     *
     * @return array<string, mixed>
     */
    public function prepareData(array $order_data, string $type): array
    {
        $prefix     = 'o';

        $ofd_inn    = Registry::get('addons.rus_ofd_ferma.setting_inn');
        $ofd_nalog  = Registry::get('addons.rus_ofd_ferma.setting_nalog');
        $ofd_nds    = Registry::get('addons.rus_ofd_ferma.setting_nds');

        //свертка
        $ofd_collapse      = Registry::get('addons.rus_ofd_ferma.setting_collapse');
        if ('Y' === $ofd_collapse) {
            $ofd_collapse = true;
        } else {
            $ofd_collapse = false;
        }

        $ofd_collapse_name = Registry::get('addons.rus_ofd_ferma.setting_collapse_name');

        $receipt_factory = new ReceiptFactory('RUB', TaxType::getMap(), false);
        $receipt = $receipt_factory->createReceiptFromOrder($order_data, 'RUB', false);

        if (is_null($receipt)) {
            return [];
        }

        // Get Order
        $id = $order_data['order_id'];
        $customer_phone = $receipt->getPhone();
        $customer_email = $receipt->getEmail();
        $payment_agent_info = $this->buildPaymentAgentInfo($order_data);

        $products = $receipt->getItems();

        if (!$products) {
            return $this->logMsg("В заказе #{$id} нет товаров");
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
            'PaymentAgentInfo' => $payment_agent_info,
            'Items' => array(),
        );

        // Get Items: Price / Sold / Email
        foreach ($products as $item) {
            $vat = $item->getTaxType();

            switch ($vat) {
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

            $data['Request']['CustomerReceipt']['Items'][] = array(
                'Label' => $item->getName(),
                'Price' => $this->formatFloat($item->getPrice()),
                'Quantity' => $this->formatFloat($item->getQuantity(), 3),
                'Amount' => $this->formatFloat(
                    $item->getPrice() * $item->getQuantity() - $item->getTotalDiscount()
                ),
                'Vat' => $vat,
                'PaymentAgentInfo' => $payment_agent_info,
            );
        }

        //Если включена свертка
        if ($ofd_collapse) {
            $pos_name = $ofd_collapse_name ?: 'Undefined';

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
                'PaymentAgentInfo' => $payment_agent_info,
            ));
        }

        return $data;
    }

    /**
     * Converts PHP warnings from network calls into exceptions.
     *
     * @param int    $errno   Error level.
     * @param string $errstr  Error message.
     * @param string $errfile Source file path.
     * @param int    $errline Source line number.
     *
     * @return bool
     *
     * @throws ErrorException
     */
    public function customErrorHandler(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        $this->logMsg($errstr);

        if (0 === error_reporting()) {
            return false;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * Verifies that all required add-on settings are present.
     *
     * @return bool
     */
    private function checkSettings(): bool
    {
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

    /**
     * Returns the current auth token if it is still valid.
     *
     * @return string|false
     */
    private function checkToken(): string|false
    {
        $ofd_token          = Registry::get('addons.rus_ofd_ferma.setting_token');
        $ofd_token_exp_date = (int) Registry::get('addons.rus_ofd_ferma.setting_token_exp_date');

        // Token is valid only while the stored expiration timestamp is in the future.
        if ($ofd_token && $ofd_token_exp_date > time()) {
            return $ofd_token;
        }

        return false;
    }

    /**
     * Builds stream context options for Ferma HTTP requests.
     *
     * @param array<string, mixed> $data Request payload.
     *
     * @return array<string, mixed>
     */
    private function getHTTPOpt(array $data): array
    {
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

    /**
     * Updates the status of one or multiple fiscal receipts.
     *
     * @param array<int, int|string> $ids Receipt identifiers.
     *
     * @return array<string, mixed>|null
     */
    public function updateChecksStatus(array $ids = array()): ?array
    {
        $sWhere = '';
        if ($ids) {
            $aWhere = array();
            foreach ($ids as $id) {
                $aWhere[] = "'" . addslashes($id) . "'";
            }

            $sWhere = ' && `id` IN (' . implode(',', $aWhere) . ')';
        }

        $results = db_get_array(
            "SELECT * FROM `?:rus_ofd_ferma_receipts` "
            . "WHERE (`status` IS NULL OR `status` <> 'CONFIRMED' OR `status` <> 'FAILED')" . $sWhere
        );

        $res = null;
        foreach ($results as $result) {
            $data = array();
            $data['Request']['ReceiptId'] = $result['id'];

            $data_ins = $this->updateNewCheckStatus($data);
            if (is_object($data_ins)) {
                $res = $this->updateNewCheckInDb($result['id'], $data_ins);
                continue;
            }

            $data_ins = $this->updateOldCheckStatus($data);
            if (is_string($data_ins)) {
                $res = $this->updateOldCheckInDb($result['id'], $data_ins);
                continue;
            }

            $this->updateFailedCheckInDb($result['id']);
            $res = null;
        }

        if ($ids && count($ids) == 1) {
            return $res;
        }

        return null;
    }

    /**
     * Requests the legacy receipt status endpoint.
     *
     * @param array<string, mixed> $data Request payload.
     *
     * @return array<string, mixed>|string
     */
    private function updateOldCheckStatus(array $data): array|string
    {
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
            return $this->logMsg($e->getMessage() . 'UpdateOldCheckStatus');
        }
        restore_error_handler();
        $result = json_decode($result, false, 512, JSON_THROW_ON_ERROR);
        if (isset($result->Status) && ($result->Status === 'Success')) {
            return $result->Data->ReceiptId;
        }

        if (isset($result->Status) && ($result->Status === 'Failed')) {
            return $this->logMsg($result->Error->Message);
        }

        return $this->logMsg('some error');
    }

    /**
     * Requests the current receipt status endpoint.
     *
     * @param array<string, mixed> $data Request payload.
     *
     * @return array<string, mixed>|object
     */
    private function updateNewCheckStatus(array $data): array|object
    {
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
            return $this->logMsg($e->getMessage() . 'UpdateNewCheckStatus');
        }
        restore_error_handler();
        $result = json_decode($result, false, 512, JSON_THROW_ON_ERROR);
        if (isset($result->Status) && ($result->Status === 'Success')) {
            return $result->Data;
        }

        if (isset($result->Status) && ($result->Status === 'Failed')) {
            return $this->logMsg($result->Error->Message);
        }

        return $this->logMsg('some error');
    }

    /**
     * Persists the updated receipt status received from Ferma.
     *
     * @param int|string $check_id Receipt identifier.
     * @param object     $data     Receipt status response object.
     *
     * @return array<string, mixed>
     */
    private function updateNewCheckInDb(int|string $check_id, object $data): array
    {

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

        $sql = "UPDATE `?:rus_ofd_ferma_receipts` SET "
            . implode(',', $aDataUpdate)
            . " WHERE `id` = '{$check_id}'";
        db_query($sql);

        return $this->logMsg('', true);
    }

    /**
     * Handles persistence for the legacy status endpoint response.
     *
     * @param int|string $check_id Receipt identifier.
     * @param string     $data     Legacy endpoint response value.
     *
     * @return array<string, mixed>
     */
    private function updateOldCheckInDb(int|string $check_id, string $data): array
    {
        unset($check_id, $data);

        return $this->logMsg('', true);
    }

    /**
     * Placeholder for failed status persistence.
     *
     * @param int|string $check_id Receipt identifier.
     *
     * @return void
     */
    // @phpstan-ignore-next-line Legacy extension point is intentionally left empty.
    private function updateFailedCheckInDb(int|string $check_id): void
    {
        unset($check_id);
    }

    /**
     * Authenticates against Ferma and stores a fresh auth token.
     *
     * @return array<string, mixed>
     */
    public function setAuthToken(): array
    {
        if (!$this->checkSettings()) {
            return $this->logMsg('Для корректной работы заполните необходимы настройки');
        }

        if ($this->checkToken()) {
            return $this->logMsg('', true);
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
            return $this->logMsg($e->getMessage() . 'setAuthToken');
        }

        restore_error_handler();
        $result = json_decode($result, false, 512, JSON_THROW_ON_ERROR);

        if (isset($result->Status) && ($result->Status === 'Success')) {
            // Store token expiration as Unix timestamp to avoid string-based date comparisons.
            $token_expiration_timestamp = strtotime($result->Data->ExpirationDateUtc);
            if (!$token_expiration_timestamp) {
                return $this->logMsg(__('rus_ofd_ferma.invalid_token_expiration_date'));
            }

            //Обновляем токен
            Settings::instance()->updateValue('setting_token', $result->Data->AuthToken, 'rus_ofd_ferma');
            Registry::set('addons.rus_ofd_ferma.setting_token', $result->Data->AuthToken);

            Settings::instance()->updateValue(
                'setting_token_exp_date',
                (string) $token_expiration_timestamp,
                'rus_ofd_ferma'
            );
            Registry::set('addons.rus_ofd_ferma.setting_token_exp_date', $token_expiration_timestamp);

            return $this->logMsg('', true);
        }

        if (isset($result->Status) && ($result->Status === 'Failed')) {
            return $this->logMsg($result->Error->Message);
        }

        return $this->logMsg('some error');
    }

    /**
     * Returns a user-friendly message for the specified Ferma error code.
     *
     * @param int                  $code_id Error code.
     * @param array<string, mixed> $data    Request payload.
     *
     * @return string
     */
    private function getErrorByCode(int $code_id, array $data): string
    {
        $msg = '';

        switch ($code_id) {
            case 1019:
                $msg = "Идентификатор счета '{$data['Request']['InvoiceId']}' уже существует в ОФД";
                break;
        }

        return $msg;
    }

    /**
     * Sends the prepared receipt payload to Ferma.
     *
     * @param array<string, mixed> $data Request payload.
     *
     * @return array<string, mixed>
     */
    public function sendDataToOFD(array $data): array
    {

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
            return $this->logMsg($e->getMessage() . 'sendDataToOFD');
        }

        restore_error_handler();
        $result = json_decode($result, false, 512, JSON_THROW_ON_ERROR);

        if (isset($result->Status) && ($result->Status === 'Success')) {
            return $this->logMsg('', true, array('check_id' => $result->Data->ReceiptId));
        }

        if (isset($result->Status) && ($result->Status === 'Failed')) {
            if ($result->Error->Code) {
                if ($val = $this->getErrorByCode($result->Error->Code, $data)) {
                    return $this->logMsg($val);
                }
            }

            return $this->logMsg($result->Error->Message . 'sendDataToOFD');
        }

        return $this->logMsg('some error');
    }

    /**
     * Saves a newly created receipt to the local receipts table.
     *
     * @param int|string           $check_id Receipt identifier.
     * @param int                  $order_id Order identifier.
     * @param array<string, mixed> $data     Request payload.
     * @param float|int            $total    Order total.
     *
     * @return array<string, mixed>
     */
    private function saveCheckInDB(int|string $check_id, int $order_id, array $data, float|int $total): array
    {
        try {
            $sql = "INSERT INTO `?:rus_ofd_ferma_receipts` (id, type, inn, order_id, total, created_at) "
                . "VALUES ('{$check_id}','{$data['Request']['Type']}',"
                . "'{$data['Request']['Inn']}','{$order_id}','{$total}','"
                . gmdate('Y-m-d H:i:s')
                . "')";

            $check_item_id = db_query($sql);

            return $this->logMsg('', true, array('check_item_id' => $check_item_id));
        } catch (Exception $e) {
            return $this->logMsg($e->getMessage() . 'saveCheckInDB');
        }
    }

    /**
     * Creates and registers a fiscal receipt for the order.
     *
     * @param int    $order_id Order identifier.
     * @param string $type     Receipt type.
     *
     * @return array<string, mixed>
     */
    public function OFDcreate(int $order_id, string $type = 'Income'): array
    {

        $order_data = fn_get_order_info($order_id);
        if (empty($order_data)) {
            return $this->logMsg("Заказ #{$order_id} не существует");
        }

        //Тип чека
        $temptype = $this->getTextType($type);


        if ($this->checkExists($order_id, $type)) {
            return $this->logMsg("Чек {$temptype} для заказа #{$order_id} уже оформлен");
        }

        //Формируем чек
        $data = $this->prepareData($order_data, $type);

        if (empty($data)) {
            return [];
        }

        if (isset($data['status']) && !$data['status']) {
            return $data;
        }

        $ans = $this->sendDataToOFD($data);
        if (!empty($ans['check_id'])) {
            $save = $this->saveCheckInDB($ans['check_id'], $order_id, $data, $order_data['total']);
            if (!$save['check_item_id']) {
                return $this->logMsg("Ошибка сохранения чека {$temptype} для заказа #{$order_id}");
            }

            return $this->logMsg(
                'Чек ' . $temptype . ' для заказа #' . $order_id . ' успешно зарегистрирован.',
                true
            );
        }

        return $ans;
    }

    /**
     * Returns the number of stored receipts matching the filter.
     *
     * @param array<int|string, mixed> $where Query conditions.
     *
     * @return int
     */
    public function getCount(array $where = array()): int
    {

        if (empty($where)) {
            return (int) db_get_field("SELECT COUNT(*) FROM `?:rus_ofd_ferma_receipts` ");
        }

        return (int) db_get_field("SELECT COUNT(*) FROM `?:rus_ofd_ferma_receipts`  WHERE ?w ", $where);
    }

    /**
     * Returns a paginated list of receipts for backend display.
     *
     * @param int                      $offset    Offset.
     * @param int                      $countPage Page size.
     * @param array<int|string, mixed> $where     Query conditions.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getList(int $offset = 0, int $countPage = 30, array $where = array()): array
    {
        if (!empty($where)) {
            $list = db_get_array(
                "SELECT * FROM `?:rus_ofd_ferma_receipts` WHERE ?w ORDER BY `created_at` DESC LIMIT ?i , ?i ",
                $where,
                $offset,
                $countPage
            );
        } else {
            $list = db_get_array(
                "SELECT * FROM `?:rus_ofd_ferma_receipts` ORDER BY `created_at` DESC LIMIT ?i , ?i ",
                $offset,
                $countPage
            );
        }

        if ($list) {
            $ofd_inn            = Registry::get('addons.rus_ofd_ferma.setting_inn');
            $ofd_check_url      = Registry::get('addons.rus_ofd_ferma.setting_check_url');
            $ofd_check_url_demo = Registry::get('addons.rus_ofd_ferma.setting_check_url_demo');

            foreach ($list as &$item) {
                $item['update'] = false;
                $item['type_name'] = $this -> getTextType($item['type']);
                $item['created_at'] = date('d.m.Y h:i:s', strtotime(($item['created_at'])));

                if ('CONFIRMED' === $item['status']) {
                    if ($ofd_check_url_demo) {
                        $item['id_link'] = "'{$ofd_check_url_demo}" . ltrim($item['FDN'], '0') . "/{$item['FPD']}'";
                    } else {
                        $inn = $item['inn'];
                        if (! $inn) {
                            $inn = $ofd_inn;
                        }

                        $item['id_link'] = "'{$ofd_check_url}{$inn}/{$item['RNM']}/{$item['FN']}/"
                            . ltrim($item['FDN'], '0')
                            . "/{$item['FPD']}'";
                    }
                } else {
                    $item['update'] = true;
                }
            }
        }

        return $list;
    }
}
