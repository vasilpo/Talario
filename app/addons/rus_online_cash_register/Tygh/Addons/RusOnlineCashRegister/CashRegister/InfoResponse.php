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


namespace Tygh\Addons\RusOnlineCashRegister\CashRegister;

use Tygh\Addons\RusOnlineCashRegister\Receipt\Requisites;

/**
 * The response class represents request response on retrieve receipt data.
 *
 * @package Tygh\Addons\RusOnlineCashRegister\CashRegister
 */
class InfoResponse extends Response
{
    /** @var Requisites|null */
    protected $receipt_requisites;

    /**
     * Gets receipt requisites.
     *
     * @return Requisites|null
     */
    public function getReceiptRequisites()
    {
        return $this->receipt_requisites;
    }

    /**
     * Sets receipt requisites.
     *
     * @param Requisites $receipt_requisites
     */
    public function setReceiptRequisites(Requisites $receipt_requisites)
    {
        $this->receipt_requisites = $receipt_requisites;
    }
}