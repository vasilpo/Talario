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

use Stripe\Account;

class AccountTypes
{
    const STANDARD = 'S';
    const EXPRESS = 'E';

    /**
     * Converts Stripe account type value to the system representation.
     *
     * @param string $value Stripe account type value
     *
     * @return string
     */
    public static function toId($value)
    {
        if ($value === Account::TYPE_EXPRESS) {
            return self::EXPRESS;
        }

        return self::STANDARD;
    }

    /**
     * Checks if the account type is Express
     *
     * @param string $account_type Account type
     *
     * @return bool
     */
    public static function isExpress($account_type)
    {
        return $account_type === self::EXPRESS;
    }

    /**
     * Checks if the account type is Standard
     *
     * @param string $account_type Account type
     *
     * @return bool
     */
    public static function isStandard($account_type)
    {
        return $account_type === self::STANDARD;
    }
}
