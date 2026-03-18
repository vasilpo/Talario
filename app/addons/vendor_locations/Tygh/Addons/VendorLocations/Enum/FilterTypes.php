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

namespace Tygh\Addons\VendorLocations\Enum;

/**
 * Class FilterTypes
 * Describes types of filters by geolocation
 *
 * @package Tygh\Addons\VendorLocations\Enum
 */
class FilterTypes
{
    const REGION = 'R';
    const ZONE = 'Z';

    /**
     * @return array
     */
    public static function all()
    {
        return array(self::REGION, self::ZONE);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public static function has($type)
    {
        return in_array($type, self::all(), true);
    }
}
