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
 *  UserTypes contains possible values for `users`.`user_type` DB field.
 *
 * @package Tygh\Enum
 */
class UserTypes
{
    const ADMIN = 'A';
    const CUSTOMER = 'C';
    const VENDOR = 'V';

    /**
     * @param string $user_type User type
     *
     * @return bool
     */
    public static function isVendor($user_type)
    {
        return $user_type === self::VENDOR;
    }

    /**
     * @param string $user_type User type
     *
     * @return bool
     */
    public static function isAdmin($user_type)
    {
        return $user_type === self::ADMIN;
    }

    /**
     * @param string $user_type User type
     *
     * @return bool
     */
    public static function isCustomer($user_type)
    {
        return $user_type === self::CUSTOMER;
    }
}
