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

class VendorStatuses
{
    const ACTIVE = 'A';
    const PENDING = 'P';
    const DISABLED = 'D';
    const NEW_ACCOUNT = 'N';
    const SUSPENDED = 'S';

    /**
     * Gets all statuses, which can be set to (except NEW status)
     *
     * @return array<string>
     */
    public static function getStatusesTo()
    {
        return self::getList([VendorStatuses::NEW_ACCOUNT]);
    }

    /**
     * Gets all vendor statuses
     *
     * @param array<string> $exclude List of type codes of vendor statuses to be excluded
     *
     * @return array<string>
     */
    public static function getList(array $exclude = [])
    {
        $statuses = [
            self::ACTIVE,
            self::PENDING,
            self::DISABLED,
            self::SUSPENDED,
            self::NEW_ACCOUNT,
        ];

        return array_filter($statuses, static function ($status_code) use ($exclude) {
            return !in_array($status_code, $exclude);
        });
    }
}
