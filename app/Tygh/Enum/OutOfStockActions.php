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
 * Class OutOfStockActions containg possible product out of stock actions.
 *
 * @package Tygh\Enum
 */
class OutOfStockActions
{
    const NONE = 'N';
    const BUY_IN_ADVANCE = 'B';
    const SUBSCRIBE = 'S';

    public static function getAll()
    {
        return array(
            static::NONE           => static::NONE,
            static::BUY_IN_ADVANCE => static::BUY_IN_ADVANCE,
            static::SUBSCRIBE      => static::SUBSCRIBE,
        );
    }
}