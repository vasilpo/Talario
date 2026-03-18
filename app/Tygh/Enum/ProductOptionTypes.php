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
 * ProductOptionTypes contains possible values for option type
 *
 * @package Tygh\Enum
 */
class ProductOptionTypes
{
    const SELECTBOX = 'S';
    const RADIO_GROUP = 'R';
    const CHECKBOX = 'C';
    const INPUT = 'I';
    const TEXT = 'T';
    const FILE = 'F';

    /**
     * Returns a value indicating whether the option type is selectable.
     *
     * @param string $type Option type
     *
     * @return bool
     */
    public static function isSelectable($type)
    {
        return in_array($type, self::getSelectable(), true);
    }

    /**
     * Gets list of selectable option types.
     *
     * @return array
     */
    public static function getSelectable()
    {
        return array(self::SELECTBOX, self::RADIO_GROUP, self::CHECKBOX);
    }
}