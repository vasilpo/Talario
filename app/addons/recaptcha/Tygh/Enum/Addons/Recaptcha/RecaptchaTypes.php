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

namespace Tygh\Enum\Addons\Recaptcha;

/**
 * Class RecaptchaTypes
 * Describes types of recaptcha
 *
 * @package Tygh\Addons\Recaptcha\Enum
 */
class RecaptchaTypes
{
    const RECAPTCHA_TYPE_V2 = 'recaptcha_v2';
    const RECAPTCHA_TYPE_V3 = 'recaptcha_v3';

    /**
     * Validates type
     *
     * @param string $type recapthca type
     *
     * @return bool
     */
    public static function isRecapthcaType($type)
    {
        if (in_array($type, [static::RECAPTCHA_TYPE_V2, static::RECAPTCHA_TYPE_V3])) {
            return true;
        }

        return false;
    }
}
