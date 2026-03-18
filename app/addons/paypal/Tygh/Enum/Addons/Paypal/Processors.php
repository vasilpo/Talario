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

namespace Tygh\Enum\Addons\Paypal;

use ReflectionClass;

class Processors
{
    const PRO = 'paypal_pro.php';
    const STANDARD = 'paypal.php';
    const PAYFLOW = 'payflow_pro.php';
    const EXPRESS = 'paypal_express.php';
    const ADVANCED = 'paypal_advanced.php';

    public static function getAll()
    {
        $reflector = new ReflectionClass(__CLASS__);

        return $reflector->getConstants();
    }

    public static function getAllWithTypes()
    {
        return array(
            self::PRO      => ProcessorTypes::PRO,
            self::STANDARD => ProcessorTypes::STANDARD,
            self::PAYFLOW  => ProcessorTypes::PAYFLOW,
            self::EXPRESS  => ProcessorTypes::EXPRESS,
            self::ADVANCED => ProcessorTypes::ADVANCED,
        );
    }
}
