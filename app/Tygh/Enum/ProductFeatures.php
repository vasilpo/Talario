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
 * ProductFeatures contains possible values for feature type
 *
 * @package Tygh\Enum
 */
class ProductFeatures
{
    const GROUP = 'G';

    const SINGLE_CHECKBOX = 'C';
    const MULTIPLE_CHECKBOX = 'M';

    const TEXT_SELECTBOX = 'S';
    const NUMBER_SELECTBOX = 'N';

    const EXTENDED = 'E';

    const TEXT_FIELD = 'T';
    const NUMBER_FIELD = 'O';

    const DATE = 'D';

    public static function getSelectable()
    {
        return self::TEXT_SELECTBOX . self::MULTIPLE_CHECKBOX . self::NUMBER_SELECTBOX . self::EXTENDED;
    }

    /**
     * Gets product feature types that uses variants
     *
     * @return string[]
     */
    public static function getSelectableList()
    {
        return [self::TEXT_SELECTBOX, self::MULTIPLE_CHECKBOX, self::NUMBER_SELECTBOX, self::EXTENDED];
    }

    public static function getAllTypes()
    {
        return self::SINGLE_CHECKBOX . self::MULTIPLE_CHECKBOX . self::TEXT_SELECTBOX . self::NUMBER_SELECTBOX . self::EXTENDED . self::TEXT_FIELD . self::NUMBER_FIELD . self::DATE;
    }
}
