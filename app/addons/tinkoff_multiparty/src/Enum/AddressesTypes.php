<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Addons\TinkoffMultiparty\Enum;

use ReflectionClass;

defined('BOOTSTRAP') or die('Access denied');

class AddressesTypes
{
    const LEGAL = 'legal';
    const ACTUAL = 'actual';
    const POST = 'post';
    const OTHER = 'other';

    /**
     * Convert value of addresses type to specific version of it for current object.
     *
     * @param string $addresses_type_value Value of agent type
     *
     * @return string
     */
    public static function getValue($addresses_type_value)
    {
        return (string) constant('self::' . strtoupper($addresses_type_value));
    }

    /**
     * Return all available addresses type variants for specific object.
     *
     * @return array<string, string>
     */
    public static function getAllValues()
    {
        $reflect = new ReflectionClass(self::class);
        return $reflect->getConstants();
    }
}
