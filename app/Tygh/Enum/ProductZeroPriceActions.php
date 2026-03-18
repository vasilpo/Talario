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
 * ProductZeroPriceActions contains possible values for actions if product price is zero
 *
 * @package Tygh\Enum
 */
class ProductZeroPriceActions
{
    /**
     * Do not allow to add product to cart
     */
    const NOT_ALLOW_ADD_TO_CART = 'R';

    /**
     * Allow to add product to cart
     */
    const ALLOW_ADD_TO_CART = 'P';

    /**
     * Ask customer to enter product price
     */
    const ASK_TO_ENTER_PRICE = 'A';
}
