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

namespace Tygh\Tools;

/**
 * Class Math contains methods commonly used for mathematic operations.
 *
 * @package Tygh\Tools
 * @since   4.3.5
 */
class Math
{
    /**
     * Floors given number to the given precision.
     *
     * @param float|int $x         Number to floor
     * @param float|int $precision Precision in format: 0.01, 0.1, 1, 10, 100, ...
     *
     * @return mixed
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    public static function floorToPrecision($x, $precision)
    {
        return $precision * self::floor($x / $precision);
    }

    /**
     * Ceils given number to the given precision.
     *
     * @param float|int $x         Number to ceil
     * @param float|int $precision Precision in format: 0.01, 0.1, 1, 10, 100, ...
     *
     * @return mixed
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint
     */
    public static function ceilToPrecision($x, $precision)
    {
        $fmod = $x - self::floorToPrecision($x, $precision);

        if ($fmod > 0) {
            return $x + $precision - $fmod;
        }

        return $x + $precision - $precision;
    }

    /**
     * floor() PHP function replacement that doesn't returns floats like 1.999999...
     *
     * @param int|float $x
     *
     * @return int
     */
    public static function floor($x)
    {
        // (int) (string) emulates floor() call for positive values.
        // This is required because floor((double) 201) may be equal to 200, because of
        // PHP's internal floating point numbers representation (i.e. 201.0 is stored as 200.999999...)
        $is_negative = $x < 0;
        $x = (string) $x;
        // negative values have to be floored down
        if ($is_negative && strpos($x, '.')) {
            list($x, $dec) = explode('.', $x);
            $x = $x - ($dec ? 1 : 0);
        }

        return (int) $x;
    }
}
