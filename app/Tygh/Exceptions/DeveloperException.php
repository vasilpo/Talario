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

namespace Tygh\Exceptions;

class DeveloperException extends AException
{
    /**
     * @param string $cache_level Cache level
     */
    public static function undefinedCacheLevel($cache_level)
    {
        self::throwException(sprintf('Registry: undefined cache level %s', $cache_level));
    }

    public static function hookHandlerIsNotCallable($func)
    {
        self::throwException(sprintf('Hook %s is not callable', $func));
    }

    public static function undefinedStorageDriver()
    {
        self::throwException('Storage: undefined storage backend');
    }

    public static function undefinedStorageType($type)
    {
        self::throwException('Storage: undefined storage type - ' . $type);
    }

    public static function throwException($message)
    {
        throw new DeveloperException($message);
    }
}
