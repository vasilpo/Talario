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
 * BackendMenuLocations contains possible values for locations of admin/vendor menu
 *
 * @package Tygh\Enum
 */
class BackendMenuLocations
{
    const TOP = 'top';
    const CENTRAL = 'central';

    /**
     * Gets list of all backend menu locations
     *
     * @return array<string>
     */
    public static function getAll()
    {
        return [
            self::TOP,
            self::CENTRAL,
        ];
    }
}
