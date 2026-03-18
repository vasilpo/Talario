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

namespace Tygh\Enum\Addons\StripeConnect;

use ReflectionClass;

class RejectedReasons
{
    const FRAUD  = 'rejected.fraud';
    const LISTED = 'rejected.listed';
    const TERMS  = 'rejected.terms_of_service';
    const OTHER  = 'rejected.other';

    /**
     * Gets all values
     *
     * @return string[]
     */
    public static function getAll()
    {
        $reflection = new ReflectionClass(static::class);
        return $reflection->getConstants();
    }
}
