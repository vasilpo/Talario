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
 * Class YesNo contains possible values of boolean type used in the database.
 *
 * @package Tygh\Enum
 */
class YesNo
{
    const YES = 'Y';
    const NO = 'N';

    /**
     * Converts value to the string representation.
     *
     * @param bool|string $val Value
     *
     * @return string
     */
    public static function toId($val)
    {
        return $val === true || $val === self::YES
            ? YesNo::YES
            : YesNo::NO;
    }

    /**
     * Converts value to the boolean representation.
     *
     * @param bool|string|int|null $val Value
     *
     * @return bool
     */
    public static function toBool($val)
    {
        return $val === true || $val === self::YES;
    }

    /**
     * Checks whether value represents true value.
     *
     * @param bool|string|int|null $val Value
     *
     * @return bool
     */
    public static function isTrue($val)
    {
        return static::toBool($val);
    }

    /**
     * Checks whether value represents false value.
     *
     * @param bool|string|int|null $val Value
     *
     * @return bool
     */
    public static function isFalse($val)
    {
        return !static::toBool($val);
    }
}
