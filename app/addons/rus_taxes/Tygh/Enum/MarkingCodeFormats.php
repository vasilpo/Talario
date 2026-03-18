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

/**
 * MarkingCodeFormats contains values of marking code formats.
 *
 * @package Tygh\Addons\RusTaxes\Enum
 */
class MarkingCodeFormats
{
    const GS1M = 'gs1m';
    const FUR  = 'fur';

    /**
     * Returns all possible values.
     *
     * @return int[]
     */
    public static function getAll(): array
    {
        $refl = new ReflectionClass(self::class);
        return $refl->getConstants();
    }

    /**
     * Checks if a value exists.
     *
     * @param string $value Check value
     */
    public static function isExists(string $value): bool
    {
        return in_array($value, self::getAll());
    }
}
