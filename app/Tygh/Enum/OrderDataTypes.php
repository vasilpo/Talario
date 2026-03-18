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

namespace Tygh\Enum;

/**
 * Class OrderDataTypes stores possible type identifiers for records in ?:order_data table.
 *
 * @package Tygh\Enum
 */
class OrderDataTypes
{
    /**
     * @const SHIPPING Shipping information
     */
    const SHIPPING = 'L';

    /**
     * @const CURRENCY Secondary currency
     */
    const CURRENCY = 'R';

    /**
     * @const PAYMENT Payment information
     */
    const PAYMENT = 'P';

    /**
     * @const GROUPS Product groups
     */
    const GROUPS = 'G';

    /**
     * @const TAXES Taxes information
     */
    const TAXES = 'T';

    /**
     * @const COUPONS Promotion coupons information
     */
    const COUPONS = 'C';

    /**
     * @const PAYMENT_STARTED Whether payment was started
     */
    const PAYMENT_STARTED = 'S';
}