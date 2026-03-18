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
 * ProductFeatureStyles contains possible values for feature style
 *
 * @package Tygh\Enum
 */
class ProductFeatureStyles
{
    const MULTIPLE_CHECKBOX = 'multiple_checkbox';
    const DROP_DOWN = 'dropdown';
    const DROP_DOWN_IMAGES = 'dropdown_images';
    const DROP_DOWN_LABELS = 'dropdown_labels';
    const CHECKBOX = 'checkbox';
    const NUMBER = 'number';
    const BRAND = 'brand';
    const COLOR = 'color';
    const TEXT = 'text';

    public static function getAllStyles()
    {
        return [
            self::MULTIPLE_CHECKBOX,
            self::DROP_DOWN,
            self::DROP_DOWN_IMAGES,
            self::DROP_DOWN_LABELS,
            self::CHECKBOX,
            self::NUMBER,
            self::BRAND,
            self::COLOR,
            self::TEXT,
        ];
    }
}
