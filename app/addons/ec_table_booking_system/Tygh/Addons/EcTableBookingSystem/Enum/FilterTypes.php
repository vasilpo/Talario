<?php

namespace Tygh\Addons\EcTableBookingSystem\Enum;
/**
 * Class FilterTypes
 * Describes types of filters by category
 *
 * @package Tygh\Addons\EcTableBookingSystem\Enum
 */
class FilterTypes
{
    const DATEFILTER = 'B';
    /**
     * @return array
     */
    public static function all()
    {
        return array(self::DATEFILTER);
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
