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
 * Contains font weights and their numerical representation.
 *
 * @package Tygh\Enum
 */
class FontWeight
{
    const NORMAL = 400;
    const NORMAL_TEXT = 'normal';

    const BOLD = 700;
    const BOLD_TEXT = 'bold';

    /**
     * Converts font-weight CSS property into font weight ID.
     *
     * @param string $value CSS property value
     *
     * @return int
     */
    public static function getByValue($value)
    {
        switch ($value) {
            case self::BOLD_TEXT:
                return self::BOLD;
            case self::NORMAL_TEXT:
                return self::NORMAL;
            default:
                return (int) $value;
        }
    }
}
