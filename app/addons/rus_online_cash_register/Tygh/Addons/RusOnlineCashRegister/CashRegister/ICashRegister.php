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


use Tygh\Addons\RusOnlineCashRegister\Receipt\Receipt;

/**
 * Interface for online cash register.
 *
 * @package Tygh\Addons\RusOnlineCashRegister
 */
interface ICashRegister
{
    /**
     * Sends receipt to cash register.
     *
     * @param Receipt $receipt Instance of the receipt.
     *
     * @return SendResponse
     */
    public function send(Receipt $receipt);

    /**
     * Gets receipt info by UUID.
     *
     * @param string $uuid UUID of the receipt.
     *
     * @return InfoResponse
     */
    public function info($uuid);
}