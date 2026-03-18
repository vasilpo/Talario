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
 * ProductFilterStyles contains possible values for filter style
 *
 * @package Tygh\Enum
 */
class ProductFilterStyles
{
    const CHECKBOX = 'checkbox';
    const SLIDER = 'slider';
    const COLOR = 'color';
    const DATE = 'date';

    public static function getAllStyles()
    {
        return [
            self::CHECKBOX,
            self::SLIDER,
            self::COLOR,
            self::DATE,
        ];
    }
}
