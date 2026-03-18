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
 * SiteArea contains possible site area values.
 *
 * @see AREA
 *
 * @package Tygh\Enum
 */
class SiteArea
{
    const STOREFRONT = 'C';
    const VENDOR_PANEL = 'V';
    const ADMIN_PANEL = 'A';

    /**
     * @param string $area Area
     *
     * @return bool
     */
    public static function isStorefront($area)
    {
        return $area === self::STOREFRONT;
    }

    /**
     * @param string $area Area
     *
     * @return bool
     */
    public static function isAdmin($area)
    {
        return $area === self::ADMIN_PANEL;
    }

    /**
     * @param string $area Area
     *
     * @return bool
     */
    public static function isVendor($area)
    {
        return $area === self::VENDOR_PANEL;
    }
}
