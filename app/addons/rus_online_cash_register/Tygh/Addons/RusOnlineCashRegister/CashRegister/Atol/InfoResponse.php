<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

namespace Tygh\Addons\RusOnlineCashRegister\CashRegister\Atol;

use Tygh\Addons\RusOnlineCashRegister\CashRegister\InfoResponse as BaseReportResponse;
use Tygh\Addons\RusOnlineCashRegister\Receipt\Requisites;

/**
 * The response class represents request response on retrieve receipt data.
 *
 * @package Tygh\Addons\RusOnlineCashRegister\CashRegister\Atol
 */
class InfoResponse extends BaseReportResponse
{
    /**
     * InfoResponse constructor.
     *
     * @param string $response Raw response string.
     */
    public function __construct($response)
    {
        $data = SendResponse::parseJson($response, $this);

        if ($data) {
            $receipt_requisites = Requisites::fromArray(array(
                'fiscal_receipt_number' => isset($data['payload']['fiscal_receipt_number']) ? $data['payload']['fiscal_receipt_number'] : null,
                'shift_number' => isset($data['payload']['shift_number']) ? $data['payload']['shift_number'] : null,
                'receipt_timestamp' => isset($data['payload']['receipt_datetime']) ? $data['payload']['receipt_datetime'] : null,
                'receipt_total' => isset($data['payload']['total']) ? (float) $data['payload']['total'] : null,
                'fn_number' => isset($data['payload']['fn_number']) ? $data['payload']['fn_number'] : null,
                'ecr_registration_number' => isset($data['payload']['ecr_registration_number']) ? $data['payload']['ecr_registration_number'] : null,
                'fiscal_document_number' => isset($data['payload']['fiscal_document_number']) ? $data['payload']['fiscal_document_number'] : null,
                'fiscal_document_attribute' => isset($data['payload']['fiscal_document_attribute']) ? $data['payload']['fiscal_document_attribute'] : null,
            ));

            $this->setReceiptRequisites($receipt_requisites);
        }
    }
}