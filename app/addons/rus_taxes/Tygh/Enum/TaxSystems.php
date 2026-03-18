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

use ReflectionClass;

defined('BOOTSTRAP') or die('Access denied');

class TaxSystems
{
    const OSN = 'osn';
    const USN_INCOME = 'usn_income';
    const USN_INCOME_OUTCOME = 'usn_income_outcome';
    const PATENT = 'patent';
    const ENVD = 'envd';
    const ESN = 'esn';

    /**
     * Returns all tax systems.
     *
     * @return array<string, string>
     */
    public static function getAllValues()
    {
        $reflect = new ReflectionClass(self::class);
        return $reflect->getConstants();
    }
}
