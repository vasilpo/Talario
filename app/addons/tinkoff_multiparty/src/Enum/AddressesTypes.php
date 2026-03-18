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
