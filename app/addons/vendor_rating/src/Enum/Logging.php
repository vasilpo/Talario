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

namespace Tygh\Addons\VendorRating\Enum;

/**
 * Class Logging contains logging-specific constants and methods.
 *
 * @package Tygh\Addons\VendorRating\Enum
 */
class Logging
{
    const LOG_TYPE_VENDOR_RATING = 'vendor_rating';
    const ACTION_SUCCESS = 'vr_success';
    const ACTION_FAILURE = 'vr_failure';

    public static function getActions()
    {
        return [
            self::ACTION_SUCCESS,
            self::ACTION_FAILURE,
        ];
    }
}
